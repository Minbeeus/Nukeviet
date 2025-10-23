<?php

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

/**
 * Lấy tổng số lượng dụng cụ theo từng trạng thái
 */
function get_tool_stats()
{
    global $db;

    // Khởi tạo mảng chứa kết quả với giá trị mặc định là 0
    $stats = [
        'total' => 0,
        'available' => 0,
        'borrowed' => 0,
        'maintenance' => 0,
    ];

    // Lấy tổng số lượng dụng cụ
    $stats['total'] = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_tool_slip_management_tools')->fetchColumn();

    // Đếm số lượng dụng cụ theo từng trạng thái bằng một câu lệnh duy nhất cho hiệu quả
    $query = $db->query('SELECT status, COUNT(*) as count FROM ' . NV_PREFIXLANG . '_tool_slip_management_tools GROUP BY status');
    while ($row = $query->fetch()) {
        if ($row['status'] == 1) { // 1: Sẵn có
            $stats['available'] = $row['count'];
        } elseif ($row['status'] == 2) { // 2: Đang mượn
            $stats['borrowed'] = $row['count'];
        } elseif ($row['status'] == 3) { // 3: Đang bảo trì
            $stats['maintenance'] = $row['count'];
        }
    }
    
    return $stats;
}

/**
 * Đếm số phiếu mượn đang bị quá hạn
 * @return int Tổng số phiếu quá hạn
 */
function get_overdue_slips_count()
{
    global $db;

    // Trạng thái 3 là 'Quá hạn'
    $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_tool_slip_management_slips WHERE status = 3')->fetchColumn();
    
    return $count;
}