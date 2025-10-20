<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Khai báo các biến toàn cục cần thiết
global $lang_module, $global_config, $module_file;

// Gọi file model để sử dụng các hàm truy vấn CSDL
require_once NV_ROOTDIR . '/modules/' . $module_file . '/models/dashboard.php';

// Đặt tiêu đề cho trang
$page_title = isset($lang_module['main']) ? $lang_module['main'] : 'Dashboard';

// Lấy dữ liệu thống kê từ model
$tool_stats = get_tool_stats();
$overdue_slips = get_overdue_slips_count();

// XÂY DỰNG ĐƯỜNG DẪN TEMPLATE THEO CHUẨN ADMIN_DEFAULT
// NukeViet sẽ tìm kiếm template trong: /themes/admin_default/modules/tool-slip-management/
$module_theme = !empty($global_config['module_theme']) ? $global_config['module_theme'] : 'admin_default';

$template_path_raw = NV_ROOTDIR . '/themes/' . $module_theme . '/modules/' . $module_file;

// Chuẩn hóa đường dẫn để tránh lỗi gạch chéo (\) trên Windows
$template_path = str_replace(array('\\', '//'), '/', $template_path_raw);

// Khởi tạo giao diện, trỏ đến file main.tpl
$xtpl = new XTemplate('main.tpl', $template_path);

$xtpl->assign('LANG', $lang_module);

$xtpl->assign('STATS', $tool_stats);
$xtpl->assign('OVERDUE_SLIPS', $overdue_slips);

$xtpl->parse('main');
$contents = $xtpl->text('main');

// Thêm CSS tùy chỉnh cho module
$css_path = NV_BASE_SITEURL . 'modules/' . $module_file . '/css/dashboard.css';
$contents = '<link rel="stylesheet" href="' . $css_path . '">' . $contents;

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';