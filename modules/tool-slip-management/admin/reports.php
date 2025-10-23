<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Load language
$langfile = NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php';
include $langfile;

$page_title = $lang_module['reports'];

$xtpl = new XTemplate('reports.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);

// Kiểm tra xem bảng có tồn tại không
$table_name = NV_PREFIXLANG . '_' . $module_data . '_tools';
$table_exists = $db->query("SHOW TABLES LIKE '" . $table_name . "'")->rowCount() > 0;

if (!$table_exists) {
    $xtpl->assign('MESSAGE', $lang_module['module_not_installed_message']);
    $xtpl->parse('main.not_installed');
    $contents = $xtpl->text('main');
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
    exit;
}

// Báo cáo lịch sử mượn/trả theo khoảng thời gian
$start_date = $nv_Request->get_title('start_date', 'get', date('Y-m-01'));
$end_date = $nv_Request->get_title('end_date', 'get', date('Y-m-d'));

// Validate and normalize dates (expecting YYYY-mm-dd)
$start_dt = DateTime::createFromFormat('Y-m-d', $start_date);
$end_dt = DateTime::createFromFormat('Y-m-d', $end_date);
if (!$start_dt) {
    $start_date = date('Y-m-01');
    $start_dt = new DateTime($start_date);
}
if (!$end_dt) {
    $end_date = date('Y-m-d');
    $end_dt = new DateTime($end_date);
}
$start_date_str = $start_dt->format('Y-m-d');
$end_date_str = $end_dt->format('Y-m-d');

$xtpl->assign('START_DATE', $start_date_str);
$xtpl->assign('END_DATE', $end_date_str);

// Lịch sử mượn/trả
try {
    $sql = 'SELECT bs.*, s.full_name, s.student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips bs'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_students s ON bs.student_id = s.id'
        . ' WHERE bs.borrow_date BETWEEN :start_date AND :end_date ORDER BY bs.borrow_date DESC';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':start_date', $start_date_str);
    $stmt->bindValue(':end_date', $end_date_str);
    $stmt->execute();
    $borrowings = $stmt->fetchAll();
} catch (PDOException $e) {
    $borrowings = array();
    // Log error for debugging (do not expose to users)
    if (function_exists('nv_insert_logs')) {
        $userid = (isset($admin_info['userid']) ? $admin_info['userid'] : 0);
        nv_insert_logs(NV_LANG_INTERFACE, $module_name, 'report_borrowings_query_error', $e->getMessage(), $userid);
    } else {
        error_log('[tool-slip-management] reports.php borrowings query error: ' . $e->getMessage());
    }
}

foreach ($borrowings as $borrowing) {
    $borrowing['status_text'] = isset($lang_module[$borrowing['status']]) ? $lang_module[$borrowing['status']] : $borrowing['status'];
    $xtpl->assign('BORROWING', $borrowing);
    $xtpl->parse('main.borrowing_history.row');
}

// Thống kê tần suất mượn của từng loại công cụ
try {
    $sql = 'SELECT c.name as category_name, COUNT(bd.id) as borrow_count FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slip_details bd'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tools t ON bd.tool_id = t.id'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_categories c ON t.category_id = c.id'
        . ' GROUP BY t.category_id ORDER BY borrow_count DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $category_stats = $stmt->fetchAll();
} catch (PDOException $e) {
    $category_stats = array();
    if (function_exists('nv_insert_logs')) {
        $userid = (isset($admin_info['userid']) ? $admin_info['userid'] : 0);
        nv_insert_logs(NV_LANG_INTERFACE, $module_name, 'report_category_stats_query_error', $e->getMessage(), $userid);
    } else {
        error_log('[tool-slip-management] reports.php category stats query error: ' . $e->getMessage());
    }
}

foreach ($category_stats as $stat) {
    $xtpl->assign('STAT', $stat);
    $xtpl->parse('main.category_stats.row');
}

// Danh sách các phiếu mượn bị quá hạn
try {
    $sql = 'SELECT bs.*, s.full_name, s.student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips bs'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_students s ON bs.student_id = s.id'
        . ' WHERE bs.status = :status ORDER BY bs.due_date DESC';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':status', 'overdue');
    $stmt->execute();
    $overdue_slips = $stmt->fetchAll();
} catch (PDOException $e) {
    $overdue_slips = array();
    if (function_exists('nv_insert_logs')) {
        $userid = (isset($admin_info['userid']) ? $admin_info['userid'] : 0);
        nv_insert_logs(NV_LANG_INTERFACE, $module_name, 'report_overdue_query_error', $e->getMessage(), $userid);
    } else {
        error_log('[tool-slip-management] reports.php overdue slips query error: ' . $e->getMessage());
    }
}

foreach ($overdue_slips as $slip) {
    $xtpl->assign('SLIP', $slip);
    $xtpl->parse('main.overdue_slips.row');
}

// Lịch sử bảo trì/thanh lý của các thiết bị
try {
    $sql = 'SELECT m.*, t.name as tool_name, t.code as tool_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_maintainance_disposal_slips m'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tools t ON m.tool_id = t.id ORDER BY m.create_date DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $maintenance_slips = $stmt->fetchAll();
} catch (PDOException $e) {
    $maintenance_slips = array();
    if (function_exists('nv_insert_logs')) {
        $userid = (isset($admin_info['userid']) ? $admin_info['userid'] : 0);
        nv_insert_logs(NV_LANG_INTERFACE, $module_name, 'report_maintenance_query_error', $e->getMessage(), $userid);
    } else {
        error_log('[tool-slip-management] reports.php maintenance query error: ' . $e->getMessage());
    }
}

foreach ($maintenance_slips as $slip) {
    $slip['type_text'] = isset($lang_module[$slip['type']]) ? $lang_module[$slip['type']] : $slip['type'];
    $xtpl->assign('SLIP', $slip);
    $xtpl->parse('main.maintenance_history.row');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
