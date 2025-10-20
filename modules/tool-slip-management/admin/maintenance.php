<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Lấy hành động từ URL, mặc định là 'list'
$action = $nv_Request->get_string('action', 'get', 'list');

// Gọi file model tương ứng
require_once NV_ROOTDIR . '/modules/' . $module_file . '/models/maintenance.php';

// Điều hướng theo action
switch ($action) {
    case 'add':
        show_maintenance_form();
        break;
    case 'save':
        save_maintenance_data();
        break;
    default: // 'list'
        show_maintenance_list();
        break;
}

/**
 * Hiển thị danh sách các phiếu bảo trì/hủy
 */
function show_maintenance_list()
{
    global $nv_Request, $lang_module, $module_file, $module_name, $op, $global_config;

    $page_title = isset($lang_module['maintenance_manage']) ? $lang_module['maintenance_manage'] : 'Maintenance Management';
    $page = $nv_Request->get_int('page', 'get', 1);
    $per_page = 20;

    // Lấy dữ liệu từ model
    list($maintenance_list, $total_rows) = get_maintenance_list($page, $per_page);

    // Tạo link phân trang
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;
    $generate_page = nv_generate_page($base_url, $total_rows, $per_page, $page);

    $xtpl = new XTemplate('maintenance.tpl', str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file));
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('MODULE_URL', $base_url);

    $type_map = [
        1 => ['text' => 'Bảo trì', 'class' => 'info'],
        2 => ['text' => 'Hủy', 'class' => 'danger']
    ];

    // Hiển thị danh sách
    if (!empty($maintenance_list)) {
        foreach ($maintenance_list as $slip) {
            $slip['type_text'] = $type_map[$slip['type']]['text'];
            $slip['type_class'] = $type_map[$slip['type']]['class'];
            $slip['created_date_formated'] = nv_date('d/m/Y', $slip['created_date']);
            $xtpl->assign('SLIP', $slip);
            $xtpl->parse('main.loop');
        }
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
 * Hiển thị form thêm mới phiếu
 */
function show_maintenance_form()
{
    global $lang_module, $module_file, $module_name, $op, $global_config;

    $page_title = isset($lang_module['add_new_maintenance']) ? $lang_module['add_new_maintenance'] : 'Add New Maintenance/Disposal Slip';

    // Lấy danh sách các dụng cụ đang "Sẵn có" để tạo phiếu
    $available_tools = get_tools_for_maintenance();

    $xtpl = new XTemplate('maintenance_form.tpl', str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file));
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('PAGE_TITLE', $page_title);
    $xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=save');

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
 * Lưu dữ liệu phiếu vào CSDL
 */
function save_maintenance_data()
{
    global $nv_Request, $module_name, $op, $user_info;

    $data = [
        'tool_id' => $nv_Request->get_int('tool_id', 'post', 0),
        'type' => $nv_Request->get_int('type', 'post', 0),
        'reason' => $nv_Request->get_string('reason', 'post', ''),
        'admin_id' => $user_info['admin_id']
    ];

    if ($data['tool_id'] > 0 && $data['type'] > 0 && !empty($data['reason'])) {
        create_maintenance_slip($data);
    }

    // Sau khi lưu, chuyển hướng về trang danh sách
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    die();
}