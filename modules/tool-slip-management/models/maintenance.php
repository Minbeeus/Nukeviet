<?php

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

/**
 * Lấy danh sách các phiếu bảo trì/hủy có phân trang
 * @param int $page
 * @param int $per_page
 * @return array
 */
function get_maintenance_list($page, $per_page)
{
    global $db;
    $sql_count = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_tool_slip_management_maintenance';
    $total_rows = $db->query($sql_count)->fetchColumn();

    $sql_data = 'SELECT m.*, t.name as tool_name, t.code as tool_code
                 FROM ' . NV_PREFIXLANG . '_tool_slip_management_maintenance m
                 JOIN ' . NV_PREFIXLANG . '_tool_slip_management_tools t ON m.tool_id = t.id
                 ORDER BY m.id DESC
                 LIMIT ' . ($page - 1) * $per_page . ',' . $per_page;
    $results = $db->query($sql_data)->fetchAll();

    return [$results, $total_rows];
}

/**
 * Lấy danh sách các dụng cụ đang ở trạng thái "Sẵn có"
 * @return array
 */
function get_tools_for_maintenance()
{
    global $db;
    // Chỉ lấy các dụng cụ đang sẵn có (status=1) để tạo phiếu
    $sql = 'SELECT id, name, code as tool_code FROM ' . NV_PREFIXLANG . '_tool_slip_management_tools WHERE status = 1 ORDER BY name ASC';
    return $db->query($sql)->fetchAll();
}

/**
 * Tạo phiếu bảo trì/hủy mới và cập nhật trạng thái dụng cụ
 * @param array $data
 * @return bool
 */
function create_maintenance_slip($data)
{
    global $db;

    // Xác định trạng thái mới cho dụng cụ dựa vào loại phiếu
    // 1: Bảo trì -> status = 3
    // 2: Hủy -> status = 4
    $new_status = ($data['type'] == 1) ? 3 : 4;

    // Bắt đầu một giao dịch để đảm bảo cả 2 hành động cùng thành công
    $db->beginTransaction();
    try {
        // 1. Thêm phiếu mới vào bảng maintenance
        $sql1 = 'INSERT INTO ' . NV_PREFIXLANG . '_tool_slip_management_maintenance
                    (tool_id, type, reason, created_date, admin_id)
                 VALUES
                    (:tool_id, :type, :reason, ' . NV_CURRENTTIME . ', :admin_id)';

        $stmt1 = $db->prepare($sql1);
        $stmt1->bindValue(':tool_id', $data['tool_id'], PDO::PARAM_INT);
        $stmt1->bindValue(':type', $data['type'], PDO::PARAM_INT);
        $stmt1->bindValue(':reason', $data['reason'], PDO::PARAM_STR);
        $stmt1->bindValue(':admin_id', $data['admin_id'], PDO::PARAM_INT);
        $stmt1->execute();

        // 2. Cập nhật trạng thái của dụng cụ tương ứng
        $sql2 = 'UPDATE ' . NV_PREFIXLANG . '_tool_slip_management_tools SET status = :status WHERE id = :tool_id';
        $stmt2 = $db->prepare($sql2);
        $stmt2->bindValue(':status', $new_status, PDO::PARAM_INT);
        $stmt2->bindValue(':tool_id', $data['tool_id'], PDO::PARAM_INT);
        $stmt2->execute();

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