<?php

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

/**
 * Lấy dữ liệu báo cáo lịch sử mượn/trả trong một khoảng thời gian
 * @param int $from_date_timestamp Timestamp của ngày bắt đầu
 * @param int $to_date_timestamp Timestamp của ngày kết thúc
 * @return array Mảng chứa dữ liệu báo cáo
 */
function get_borrowing_history_report($from_date_timestamp, $to_date_timestamp)
{
    global $db;

    // Đây là một câu lệnh SQL phức tạp, kết nối 4 bảng lại với nhau
    // slips -> slip_details -> tools
    // slips -> students
    $sql = 'SELECT
                s.id as slip_id,
                st.full_name as student_name,
                st.student_code,
                t.name as tool_name,
                t.code as tool_code,
                s.borrow_date,
                s.due_date,
                s.return_date,
                s.status
            FROM
                ' . NV_PREFIXLANG . '_tool_slip_management_slips s
            JOIN
                ' . NV_PREFIXLANG . '_tool_slip_management_slip_details sd ON s.id = sd.slip_id
            JOIN
                ' . NV_PREFIXLANG . '_tool_slip_management_tools t ON sd.tool_id = t.id
            JOIN
                ' . NV_PREFIXLANG . '_tool_slip_management_students st ON s.student_id = st.id
            WHERE
                s.borrow_date >= :from_date AND s.borrow_date <= :to_date
            ORDER BY
                s.borrow_date DESC, s.id DESC';

    $stmt = $db->prepare($sql);

    // Gán các tham số vào câu lệnh để tránh lỗi SQL Injection
    $stmt->bindValue(':from_date', $from_date_timestamp, PDO::PARAM_INT);
    $stmt->bindValue(':to_date', $to_date_timestamp, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
}