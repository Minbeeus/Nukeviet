<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// FIX: Khai báo các biến toàn cục cần thiết để sử dụng $lang_module, $module_file, và $global_config bên ngoài các hàm
global $lang_module, $global_config, $module_file, $module_name, $op;

// Lấy hành động (action) từ URL, mặc định là 'list' (hiển thị danh sách)
$action = $nv_Request->get_string('action', 'get', 'list');
require_once NV_ROOTDIR . '/modules/' . $module_file . '/models/tools.php';

// Sử dụng switch case để điều hướng
switch ($action) {
    case 'add':
    case 'edit':
        show_tool_form($action);
        break;

    case 'save':
        save_tool_data();
        break;

    default: // Mặc định là 'list'
        show_tools_list();
        break;
}

/**
 * Hàm hiển thị form thêm/sửa
 * @param string $action
 */
function show_tool_form($action)
{
    global $nv_Request, $lang_module, $module_file, $module_name, $op, $global_config;

    $id = $nv_Request->get_int('id', 'get', 0);
    $tool = ($id > 0) ? get_tool_by_id($id) : [];

    // Nếu action là edit nhưng không tìm thấy tool, chuyển về trang danh sách
    if ($id > 0 && empty($tool)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }

    // FIX: Sử dụng isset() để tránh lỗi Undefined array key nếu lang_module bị thiếu key
    $add_title = isset($lang_module['add_new_tool']) ? $lang_module['add_new_tool'] : 'Thêm mới';
    $edit_title = isset($lang_module['edit_tool']) ? $lang_module['edit_tool'] : 'Chỉnh sửa';
    $page_title = ($action == 'add') ? $add_title : $edit_title;

    $all_categories = get_all_categories();

    // Khởi tạo XTemplate với đường dẫn chuẩn
    $module_theme = !empty($global_config['module_theme']) ? $global_config['module_theme'] : 'admin_default';
    $template_path = str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $module_theme . '/modules/' . $module_file);
    
    $xtpl = new XTemplate('tools_form.tpl', $template_path);
    
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('PAGE_TITLE', $page_title);
    $xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=save');
    $xtpl->assign('TOOL', $tool);

    foreach ($all_categories as $category) {
        $category['selected'] = (!empty($tool) && $category['id'] == $tool['category_id']) ? ' selected="selected"' : '';
        $xtpl->assign('CAT', $category);
        $xtpl->parse('main.category_loop');
    }

    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

/**
 * Hàm lưu dữ liệu
 */
function save_tool_data()
{
    global $nv_Request, $module_name, $op;

    $id = $nv_Request->get_int('id', 'post', 0);
    $data = [
        'name' => $nv_Request->get_string('name', 'post', ''),
        'tool_code' => $nv_Request->get_string('tool_code', 'post', ''),
        'category_id' => $nv_Request->get_int('category_id', 'post', 0),
        'description' => $nv_Request->get_string('description', 'post', '')
    ];

    if ($id > 0) { // Cập nhật
        update_tool($id, $data);
    } else { // Thêm mới
        add_new_tool($data);
    }

    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    die();
}

/**
 * Hàm hiển thị danh sách
 */
function show_tools_list()
{
    global $nv_Request, $lang_module, $module_file, $module_name, $op, $global_config;

    // FIX: Sử dụng isset() để tránh lỗi Undefined array key nếu lang_module bị thiếu key
    $page_title = isset($lang_module['tools_manage']) ? $lang_module['tools_manage'] : 'Tool Management';

    $page = $nv_Request->get_int('page', 'get', 1);
    $per_page = 20;
    $keyword = $nv_Request->get_string('keyword', 'get', '');
    $category_id = $nv_Request->get_int('category_id', 'get', 0);

    // Dòng 99 trước đây bị lỗi nằm ở đây: $page_title = $lang_module['tools_manage'];
    
    list($tools_list, $total_rows) = get_tools_list($page, $per_page, $keyword, $category_id);
    $all_categories = get_all_categories();

    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&keyword=' . $keyword . '&category_id=' . $category_id;
    $generate_page = nv_generate_page($base_url, $total_rows, $per_page, $page);

    // Khởi tạo XTemplate với đường dẫn chuẩn
    $module_theme = !empty($global_config['module_theme']) ? $global_config['module_theme'] : 'admin_default';
    $template_path = str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $module_theme . '/modules/' . $module_file);
    
    $xtpl = new XTemplate('tools.tpl', $template_path);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('MODULE_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    $xtpl->assign('KEYWORD', $keyword);

    foreach ($all_categories as $category) {
        $category['selected'] = ($category['id'] == $category_id) ? ' selected="selected"' : '';
        $xtpl->assign('CAT', $category);
        $xtpl->parse('main.category_loop');
    }

    // Định nghĩa map trạng thái, đảm bảo không sử dụng các key ngôn ngữ để tránh lỗi nếu ngôn ngữ không tải
    $status_map = [
        1 => ['text' => 'Sẵn có', 'class' => 'success'],
        2 => ['text' => 'Đang mượn', 'class' => 'warning'],
        3 => ['text' => 'Đang bảo trì', 'class' => 'info'],
        4 => ['text' => 'Đã hủy', 'class' => 'danger']
    ];

    foreach ($tools_list as $tool) {
        $tool['status_text'] = $status_map[$tool['status']]['text'];
        $tool['status_class'] = $status_map[$tool['status']]['class'];
        $tool['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&id=' . $tool['id'];
        $xtpl->assign('TOOL', $tool);
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