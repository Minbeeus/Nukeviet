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
$start_date = $nv_Request->get_title('start_date', 'get/post', date('Y-m-01'));
$end_date = $nv_Request->get_title('end_date', 'get/post', date('Y-m-d'));



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
$sql = 'SELECT bs.*, s.full_name, s.student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slips bs'
. ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_students s ON bs.student_id = s.id'
. ' WHERE bs.borrow_date BETWEEN :start_date AND :end_date ORDER BY bs.borrow_date DESC';
$stmt = $db->prepare($sql);
$stmt->bindValue(':start_date', strtotime($start_date_str));
$stmt->bindValue(':end_date', strtotime($end_date_str) + 86399); // End of day
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

// Check if user has applied custom date filter (not default dates)
$is_custom_filter = false;
$default_start = date('Y-m-01');
$default_end = date('Y-m-d');

if ($start_date !== $default_start || $end_date !== $default_end) {
    $is_custom_filter = true;
}

// Get statistics based on filter
if ($is_custom_filter) {
    // Use filtered data for statistics
    try {
        $sql = 'SELECT
                    COUNT(*) as total_slips,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as returned_count,
                    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as overdue_count
                FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slips
                WHERE borrow_date BETWEEN :start_date AND :end_date';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':start_date', strtotime($start_date_str));
        $stmt->bindValue(':end_date', strtotime($end_date_str) + 86399);
        $stmt->execute();
        $stats = $stmt->fetch();
    } catch (PDOException $e) {
        $stats = array(
            'total_slips' => 0,
            'returned_count' => 0,
            'active_count' => 0,
            'overdue_count' => 0
        );
        if (function_exists('nv_insert_logs')) {
            $userid = (isset($admin_info['userid']) ? $admin_info['userid'] : 0);
            nv_insert_logs(NV_LANG_INTERFACE, $module_name, 'report_filtered_stats_query_error', $e->getMessage(), $userid);
        } else {
            error_log('[tool-slip-management] reports.php filtered stats query error: ' . $e->getMessage());
        }
    }
} else {
    // Use overall statistics when no custom filter applied
    try {
        $sql = 'SELECT
                    COUNT(*) as total_slips,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as returned_count,
                    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as overdue_count
                FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slips';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stats = $stmt->fetch();
    } catch (PDOException $e) {
        $stats = array(
            'total_slips' => 0,
            'returned_count' => 0,
            'active_count' => 0,
            'overdue_count' => 0
        );
        if (function_exists('nv_insert_logs')) {
            $userid = (isset($admin_info['userid']) ? $admin_info['userid'] : 0);
            nv_insert_logs(NV_LANG_INTERFACE, $module_name, 'report_overall_stats_query_error', $e->getMessage(), $userid);
        } else {
            error_log('[tool-slip-management] reports.php overall stats query error: ' . $e->getMessage());
        }
    }
}

// Statistics for filtered data (for display in history table)
$total_borrowings_filtered = count($borrowings);
$returned_count_filtered = 0;
$active_count_filtered = 0;
$overdue_count_filtered = 0;

// Check if we have borrowing data to display
if (empty($borrowings)) {
    $xtpl->parse('main.no_borrowing_data');
} else {
    foreach ($borrowings as $borrowing) {
        if ($borrowing['status'] == 0) {
            $borrowing['status_text'] = $lang_module['borrowing'];
            $borrowing['status_class'] = 'success';
            $active_count_filtered++;
        } elseif ($borrowing['status'] == 1) {
            $borrowing['status_text'] = $lang_module['returned'];
            $borrowing['status_class'] = 'secondary';
            $returned_count_filtered++;
        } elseif ($borrowing['status'] == 2) {
            $borrowing['status_text'] = $lang_module['overdue'];
            $borrowing['status_class'] = 'danger';
            $overdue_count_filtered++;
        } else {
            $borrowing['status_text'] = 'Unknown';
            $borrowing['status_class'] = 'secondary';
        }
        $borrowing['borrow_date'] = nv_date('d/m/Y', $borrowing['borrow_date']);
        $borrowing['due_date'] = nv_date('d/m/Y', $borrowing['due_date']);
        $borrowing['return_date'] = $borrowing['return_date'] ? nv_date('d/m/Y', $borrowing['return_date']) : '';
        $xtpl->assign('BORROWING', $borrowing);
        $xtpl->parse('main.borrowing_history');
    }
}

// Assign statistics (filtered or overall based on user selection)
$xtpl->assign('TOTAL_BORROWINGS', intval($stats['total_slips'] ?? 0));
$xtpl->assign('RETURNED_COUNT', intval($stats['returned_count'] ?? 0));
$xtpl->assign('ACTIVE_COUNT', intval($stats['active_count'] ?? 0));
$xtpl->assign('OVERDUE_COUNT', intval($stats['overdue_count'] ?? 0));





// Thống kê tần suất mượn của từng loại công cụ
try {
    $sql = 'SELECT c.name as category_name, COUNT(bd.id) as borrow_count FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slip_details bd'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tools t ON bd.tool_id = t.id'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_categories c ON t.category_id = c.id'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_slips bs ON bd.slip_id = bs.id';

    // Add date filter if custom filter is applied
    if ($is_custom_filter) {
        $sql .= ' WHERE bs.borrow_date BETWEEN :start_date AND :end_date';
    }

    $sql .= ' GROUP BY t.category_id ORDER BY borrow_count DESC';

    $stmt = $db->prepare($sql);

    // Bind parameters if custom filter is applied
    if ($is_custom_filter) {
        $stmt->bindValue(':start_date', strtotime($start_date_str));
        $stmt->bindValue(':end_date', strtotime($end_date_str) + 86399);
    }

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
    $xtpl->parse('main.category_stats');
}

// Danh sách các phiếu mượn bị quá hạn
try {
    $sql = 'SELECT bs.*, s.full_name, s.student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slips bs'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_students s ON bs.student_id = s.id'
        . ' WHERE bs.status = 2';

    // Add date filter if custom filter is applied
    if ($is_custom_filter) {
        $sql .= ' AND bs.borrow_date BETWEEN :start_date AND :end_date';
    }

    $sql .= ' ORDER BY bs.due_date DESC';

    $stmt = $db->prepare($sql);

    // Bind parameters if custom filter is applied
    if ($is_custom_filter) {
        $stmt->bindValue(':start_date', strtotime($start_date_str));
        $stmt->bindValue(':end_date', strtotime($end_date_str) + 86399);
    }

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
    $slip['due_date'] = nv_date('d/m/Y', $slip['due_date']);
    $xtpl->assign('SLIP', $slip);
    $xtpl->parse('main.overdue_slips');
}

// Lịch sử bảo trì/thanh lý của các thiết bị
try {
    $sql = 'SELECT m.*, t.name as tool_name, t.code as tool_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_maintenance m'
        . ' LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tools t ON m.tool_id = t.id';

    // Add date filter if custom filter is applied
    if ($is_custom_filter) {
        $sql .= ' WHERE DATE(m.created_date) BETWEEN :start_date AND :end_date';
    }

    $sql .= ' ORDER BY m.created_date DESC';

    $stmt = $db->prepare($sql);

    // Bind parameters if custom filter is applied
    if ($is_custom_filter) {
        $stmt->bindValue(':start_date', $start_date_str);
        $stmt->bindValue(':end_date', $end_date_str);
    }

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
    if ($slip['type'] == 'maintenance') {
        $slip['type_text'] = $lang_module['maintenance'];
    } elseif ($slip['type'] == 'disposal') {
        $slip['type_text'] = $lang_module['disposal'];
    } else {
        $slip['type_text'] = $slip['type'];
    }
    $slip['create_date'] = nv_date('d/m/Y', strtotime($slip['created_date']));
    $xtpl->assign('SLIP', $slip);
    $xtpl->parse('main.maintenance_history');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

// Handle export functionality
if ($nv_Request->get_string('export', 'get') == 'excel') {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="bao-cao-muon-tra-' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');

    // Create Excel content
    $excel_content = "<table border='1'>";
    $excel_content .= "<tr><th colspan='6' style='background-color: #007bff; color: white; text-align: center; font-size: 16px;'>BÁO CÁO MƯỢN/TRẢ CÔNG CỤ</th></tr>";
    if ($is_custom_filter) {
        $excel_content .= "<tr><th colspan='6'>Thời gian: {$start_date_str} - {$end_date_str}</th></tr>";
    } else {
        $excel_content .= "<tr><th colspan='6'>Thời gian: Tất cả</th></tr>";
    }
    $excel_content .= "<tr><th colspan='2'>Tổng phiếu: {$stats['total_slips']}</th><th colspan='2'>Đã trả: {$stats['returned_count']}</th><th colspan='2'>Quá hạn: {$stats['overdue_count']}</th></tr>";
    $excel_content .= "<tr></tr>"; // Empty row
    $excel_content .= "<tr><th>Mã phiếu</th><th>Học sinh</th><th>Ngày mượn</th><th>Hạn trả</th><th>Ngày trả</th><th>Trạng thái</th></tr>";

    if (!empty($borrowings)) {
        foreach ($borrowings as $borrowing) {
            $excel_content .= "<tr>";
            $excel_content .= "<td>#{$borrowing['id']}</td>";
            $excel_content .= "<td>{$borrowing['full_name']} ({$borrowing['student_code']})</td>";
            $excel_content .= "<td>{$borrowing['borrow_date']}</td>";
            $excel_content .= "<td>{$borrowing['due_date']}</td>";
            $excel_content .= "<td>{$borrowing['return_date']}</td>";
            $excel_content .= "<td>{$borrowing['status_text']}</td>";
            $excel_content .= "</tr>";
        }
    } else {
        $excel_content .= "<tr><td colspan='6' class='text-center'>Không có dữ liệu mượn/trả trong khoảng thời gian đã chọn</td></tr>";
    }

    // Add category stats
    $excel_content .= "<tr></tr><tr><th colspan='6' style='background-color: #28a745; color: white;'>THỐNG KÊ THEO DANH MỤC</th></tr>";
    $excel_content .= "<tr><th>Danh mục</th><th colspan='5'>Số lần mượn</th></tr>";
    foreach ($category_stats as $stat) {
        $excel_content .= "<tr><td>{$stat['category_name']}</td><td colspan='5'>{$stat['borrow_count']}</td></tr>";
    }

    // Add overdue slips
    if (!empty($overdue_slips)) {
        $excel_content .= "<tr></tr><tr><th colspan='6' style='background-color: #dc3545; color: white;'>PHIẾU QUÁ HẠN</th></tr>";
        $excel_content .= "<tr><th>Mã phiếu</th><th>Học sinh</th><th colspan='4'>Hạn trả</th></tr>";
        foreach ($overdue_slips as $slip) {
            $excel_content .= "<tr><td>#{$slip['id']}</td><td>{$slip['full_name']} ({$slip['student_code']})</td><td colspan='4'>{$slip['due_date']}</td></tr>";
        }
    }

    $excel_content .= "</table>";

    echo $excel_content;
    exit;
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
