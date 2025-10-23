<?php

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

// KHAI BÁO BIẾN TOÀN CỤC BẮT BUỘC
global $db;

/**
 * Lấy danh sách phiếu mượn có phân trang
 * @param int $page
 * @param int $per_page
 * @return array
 */
function get_slips_list($page, $per_page)
{
    global $db;
    $sql_count = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_tool_slip_management_slips';
    
    // Sử dụng prepare/execute an toàn để đếm số dòng
    $stmt_count = $db->prepare($sql_count);
    $stmt_count->execute();
    $total_rows = $stmt_count->fetchColumn(); 

    // Truy vấn dữ liệu chính (JOIN với bảng students đã có student_id)
    $sql_data = 'SELECT s.*, st.full_name 
                  FROM ' . NV_PREFIXLANG . '_tool_slip_management_slips s 
                  LEFT JOIN ' . NV_PREFIXLANG . '_tool_slip_management_students st ON s.student_id = st.id 
                  ORDER BY s.id DESC 
                  LIMIT ' . ($page - 1) * $per_page . ',' . $per_page;
                  
    $stmt_data = $db->prepare($sql_data);
    $stmt_data->execute();
    $results = $stmt_data->fetchAll();
    
    return [$results, $total_rows];
}

/**
 * Lấy danh sách tất cả sinh viên
 * (Đã chuẩn hóa)
 */
function get_all_students()
{
    global $db;
    $sql = 'SELECT id, full_name, student_code FROM ' . NV_PREFIXLANG . '_tool_slip_management_students ORDER BY full_name ASC';
    return $db->prepare($sql)->execute()->fetchAll(); 
}

/**
 * Lấy danh sách các dụng cụ đang sẵn có
 * (Đã chuẩn hóa)
 */
function get_available_tools()
{
    global $db;
    $sql = 'SELECT id, name, tool_code FROM ' . NV_PREFIXLANG . '_tool_slip_management_tools WHERE status = 1 ORDER BY name ASC';
    return $db->prepare($sql)->execute()->fetchAll();
}

/**
 * Tạo phiếu mượn mới (Hàm giao dịch - Transaction)
 * (Mã đã đúng)
 */
function create_borrow_slip($data)
{
    global $db;

    $due_date_timestamp = 0;
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $data['due_date_str'], $m)) {
        $due_date_timestamp = mktime(23, 59, 59, $m[2], $m[1], $m[3]);
    }
    if ($due_date_timestamp == 0) return false;

    // Bắt đầu một giao dịch
    $db->beginTransaction();
    try {
        // 1. Thêm phiếu mượn vào bảng slips
        $sql1 = 'INSERT INTO ' . NV_PREFIXLANG . '_tool_slip_management_slips 
                     (student_id, borrow_date, due_date, notes, admin_id, status) 
                   VALUES 
                     (:student_id, ' . NV_CURRENTTIME . ', :due_date, :notes, :admin_id, 1)';
        $stmt1 = $db->prepare($sql1);
        $stmt1->bindValue(':student_id', $data['student_id'], PDO::PARAM_INT);
        $stmt1->bindValue(':due_date', $due_date_timestamp, PDO::PARAM_INT);
        $stmt1->bindValue(':notes', $data['notes'], PDO::PARAM_STR);
        $stmt1->bindValue(':admin_id', $data['admin_id'], PDO::PARAM_INT);
        $stmt1->execute();
        
        $slip_id = $db->lastInsertId();

        // 2. Thêm các dụng cụ đã mượn vào bảng slip_details
        $sql2 = 'INSERT INTO ' . NV_PREFIXLANG . '_tool_slip_management_slip_details (slip_id, tool_id) VALUES (:slip_id, :tool_id)';
        $stmt2 = $db->prepare($sql2);
        foreach ($data['tool_ids'] as $tool_id) {
            $stmt2->bindValue(':slip_id', $slip_id, PDO::PARAM_INT);
            $stmt2->bindValue(':tool_id', $tool_id, PDO::PARAM_INT);
            $stmt2->execute();
        }

        // 3. Cập nhật trạng thái của các dụng cụ thành "Đang mượn"
        $tool_ids_string = implode(',', array_map('intval', $data['tool_ids']));
        $sql3 = 'UPDATE ' . NV_PREFIXLANG . '_tool_slip_management_tools SET status = 2 WHERE id IN (' . $tool_ids_string . ')';
        $db->query($sql3); // Dùng query vì không có tham số ràng buộc

        // Nếu tất cả thành công, xác nhận giao dịch
        $db->commit();
        return true;

    } catch (Exception $e) {
        // Nếu có lỗi, hủy bỏ tất cả các thay đổi
        $db->rollBack();
        trigger_error($e->getMessage());
        return false;
    }
}

/**
 * Lấy chi tiết một phiếu mượn và danh sách dụng cụ của nó
 * (Mã đã đúng)
 */
function get_slip_details($id)
{
    global $db;
    $result = ['main_info' => [], 'tools' => []];

    // Lấy thông tin chính của phiếu
    $sql_main = 'SELECT s.*, st.full_name, st.student_code 
                  FROM ' . NV_PREFIXLANG . '_tool_slip_management_slips s 
                  LEFT JOIN ' . NV_PREFIXLANG . '_tool_slip_management_students st ON s.student_id = st.id 
                  WHERE s.id = :id';
    $result['main_info'] = $db->prepare($sql_main)->execute([':id' => $id])->fetch();

    if (empty($result['main_info'])) return [];

    // Lấy danh sách dụng cụ thuộc phiếu này
    $sql_tools = 'SELECT t.* FROM ' . NV_PREFIXLANG . '_tool_slip_management_slip_details sd 
                  JOIN ' . NV_PREFIXLANG . '_tool_slip_management_tools t ON sd.tool_id = t.id 
                  WHERE sd.slip_id = :id';
    $result['tools'] = $db->prepare($sql_tools)->execute([':id' => $id])->fetchAll();
    
    return $result;
}

/**
 * Xử lý việc trả đồ (Hàm giao dịch - Transaction)
 * (Mã đã đúng)
 */
function process_slip_return_db($id)
{
    global $db;

    // Bắt đầu giao dịch
    $db->beginTransaction();
    try {
        // 1. Cập nhật trạng thái phiếu mượn thành "Đã trả" và ghi nhận ngày trả
        $sql1 = 'UPDATE ' . NV_PREFIXLANG . '_tool_slip_management_slips SET status = 2, return_date = ' . NV_CURRENTTIME . ' WHERE id = :id';
        $db->prepare($sql1)->execute([':id' => $id]);

        // 2. Lấy danh sách ID của các dụng cụ thuộc phiếu này
        $sql_get_tools = 'SELECT tool_id FROM ' . NV_PREFIXLANG . '_tool_slip_management_slip_details WHERE slip_id = :id';
        $tool_ids_array = $db->prepare($sql_get_tools)->execute([':id' => $id])->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($tool_ids_array)) {
            // 3. Cập nhật trạng thái của các dụng cụ đó thành "Sẵn có"
            $tool_ids_string = implode(',', array_map('intval', $tool_ids_array));
            $sql3 = 'UPDATE ' . NV_PREFIXLANG . '_tool_slip_management_tools SET status = 1 WHERE id IN (' . $tool_ids_string . ')';
            $db->query($sql3); // Dùng query vì không có tham số ràng buộc
        }

        // Nếu tất cả thành công, xác nhận giao dịch
        $db->commit();
        return true;

    } catch (Exception $e) {
        // Nếu có lỗi, hủy bỏ tất cả
        $db->rollBack();
        trigger_error($e->getMessage());
        return false;
    }
}