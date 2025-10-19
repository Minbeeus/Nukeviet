<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// FIX: Khai báo các biến toàn cục cần thiết
global $lang_module, $global_config, $module_file, $module_name, $op;

// Lấy hành động (action) từ URL, mặc định là 'list'
$action = $nv_Request->get_string('action', 'get', 'list');

// Gọi file model tương ứng
require_once NV_ROOTDIR . '/modules/' . $module_file . '/models/slips.php';

// Điều hướng theo action
switch ($action) {
    case 'create':
        show_slip_form();
        break;
    case 'save':
        save_slip_data();
        break;
    case 'detail':
        show_slip_detail();
        break;
    case 'process_return':
        process_slip_return();
        break;
    default: // 'list'
        show_slips_list();
        break;
}

/**
 * Hiển thị danh sách phiếu mượn
 */
function show_slips_list()
{
    global $nv_Request, $lang_module, $module_file, $module_name, $op, $global_config;

    $page_title = isset($lang_module['slips_manage']) ? $lang_module['slips_manage'] : 'Slip Management';
    $page = $nv_Request->get_int('page', 'get', 1);
    $per_page = 20;

    list($slips_list, $total_rows) = get_slips_list($page, $per_page);

    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;
    $generate_page = nv_generate_page($base_url, $total_rows, $per_page, $page);

    $module_theme = !empty($global_config['module_theme']) ? $global_config['module_theme'] : 'admin_default';
    $template_path = str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $module_theme . '/modules/' . $module_file);

    $xtpl = new XTemplate('slips.tpl', $template_path);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('MODULE_URL', $base_url);

    // Định nghĩa map trạng thái để hiển thị trong Template
    $status_map = [
        1 => ['text' => 'Đang mượn', 'class' => 'warning'],
        2 => ['text' => 'Đã trả', 'class' => 'success'],
        3 => ['text' => 'Quá hạn', 'class' => 'danger']
    ];

    foreach ($slips_list as $slip) {
        $slip['status_text'] = $status_map[$slip['status']]['text'];
        $slip['status_class'] = $status_map[$slip['status']]['class'];
        $slip['borrow_date_formatted'] = nv_date('d/m/Y H:i', $slip['borrow_date']);
        $slip['due_date_formatted'] = nv_date('d/m/Y', $slip['due_date']);
        $slip['link_detail'] = $base_url . '&action=detail&id=' . $slip['id'];
        $xtpl->assign('SLIP', $slip);
        $xtpl->parse('main.loop');
    }

    if (!empty($generate_page)) {
        $xtpl->assign('PAGINATION', $generate_page);
        $xtpl->parse('main.pagination');
    }

    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

/**
 * Hiển thị form tạo phiếu mượn
 */
function show_slip_form()
{
    global $lang_module, $module_file, $module_name, $op, $global_config, $nv_Request;
    
    $page_title = isset($lang_module['create_new_slip']) ? $lang_module['create_new_slip'] : 'Create New Slip';

    $available_tools = get_available_tools();
    $all_students = get_all_students();

    $module_theme = !empty($global_config['module_theme']) ? $global_config['module_theme'] : 'admin_default';
    $template_path = str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $module_theme . '/modules/' . $module_file);

    $xtpl = new XTemplate('slips_form.tpl', $template_path);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('PAGE_TITLE', $page_title);
    $xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=save');
    $xtpl->assign('TODAY', date('d/m/Y'));

    foreach ($all_students as $student) {
        $xtpl->assign('STUDENT', $student);
        $xtpl->parse('main.student_loop');
    }

    foreach ($available_tools as $tool) {
        $xtpl->assign('TOOL', $tool);
        $xtpl->parse('main.tool_loop');
    }
    
    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

/**
 * Hàm lưu dữ liệu
 * (Mã đã đúng)
 */
function save_slip_data()
{
    global $nv_Request, $module_name, $op, $user_info;

    $data = [
        'student_id' => $nv_Request->get_int('student_id', 'post', 0),
        'tool_ids' => $nv_Request->get_array('tool_ids', 'post', []),
        'due_date_str' => $nv_Request->get_string('due_date', 'post', ''),
        'notes' => $nv_Request->get_string('notes', 'post', ''),
        'admin_id' => $user_info['admin_id']
    ];

    if ($data['student_id'] > 0 && !empty($data['tool_ids']) && !empty($data['due_date_str'])) {
        create_borrow_slip($data);
    }

    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    die();
}

/**
 * Hiển thị chi tiết một phiếu mượn
 */
function show_slip_detail()
{
    global $nv_Request, $lang_module, $module_file, $module_name, $op, $global_config;

    $id = $nv_Request->get_int('id', 'get', 0);
    if ($id == 0) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
    
    $slip_details = get_slip_details($id);
    if (empty($slip_details)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
    
    $page_title = isset($lang_module['slip_detail']) ? $lang_module['slip_detail'] . ' #' . $id : 'Slip Detail #' . $id;

    $module_theme = !empty($global_config['module_theme']) ? $global_config['module_theme'] : 'admin_default';
    $template_path = str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $module_theme . '/modules/' . $module_file);
    
    $xtpl = new XTemplate('slips_detail.tpl', $template_path);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('PAGE_TITLE', $page_title);
    $xtpl->assign('RETURN_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=process_return&id=' . $id);
    
    $slip_details['main_info']['borrow_date_formatted'] = nv_date('d/m/Y H:i', $slip_details['main_info']['borrow_date']);
    $slip_details['main_info']['due_date_formatted'] = nv_date('d/m/Y', $slip_details['main_info']['due_date']);
    
    $xtpl->assign('SLIP', $slip_details['main_info']);

    foreach ($slip_details['tools'] as $tool) {
        $xtpl->assign('TOOL', $tool);
        $xtpl->parse('main.tool_loop');
    }

    // Chỉ hiển thị nút "Xác nhận trả" nếu phiếu đang ở trạng thái "Đang mượn" hoặc "Quá hạn"
    if ($slip_details['main_info']['status'] == 1 || $slip_details['main_info']['status'] == 3) {
        $xtpl->parse('main.return_button');
    }

    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

/**
 * Xử lý việc trả đồ (Mã đã đúng)
 */
function process_slip_return()
{
    global $nv_Request, $module_name, $op;
    
    $id = $nv_Request->get_int('id', 'get', 0);
    if ($id > 0) {
        process_slip_return_db($id);
    }
    
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    die();
}