<?php

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

/**
 * Lấy danh sách tất cả các danh mục
 * @return array
 */
function get_all_categories()
{
    global $db;
    $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_tool_slip_management_categories ORDER BY name ASC';
    return $db->query($sql)->fetchAll();
}

/**
 * Lấy danh sách dụng cụ có phân trang và tìm kiếm
 * @param int $page Trang hiện tại
 * @param int $per_page Số mục mỗi trang
 * @param string $keyword Từ khóa tìm kiếm
 * @param int $category_id ID danh mục cần lọc
 * @return array Mảng chứa [danh sách dụng cụ, tổng số dòng]
 */
function get_tools_list($page, $per_page, $keyword, $category_id)
{
    global $db;
    $where = '';
    $params = [];

    if (!empty($keyword)) {
        $where .= ' AND (t.name LIKE :keyword OR t.tool_code LIKE :keyword)';
        $params[':keyword'] = '%' . $keyword . '%';
    }

    if ($category_id > 0) {
        $where .= ' AND t.category_id = :category_id';
        $params[':category_id'] = $category_id;
    }

    $base_sql = 'FROM ' . NV_PREFIXLANG . '_tool_slip_management_tools t
                  LEFT JOIN ' . NV_PREFIXLANG . '_tool_slip_management_categories c ON t.category_id = c.id
                  WHERE 1=1 ' . $where;

    // Đếm tổng số bản ghi thỏa mãn điều kiện
    $sql_count = 'SELECT COUNT(*) ' . $base_sql;
    
    // FIX LỖI: Tách các bước prepare, execute và fetchColumn
    $stmt_count = $db->prepare($sql_count);
    $stmt_count->execute($params);
    $total_rows = $stmt_count->fetchColumn(); // Dòng 48 đã được sửa

    // Lấy dữ liệu cho trang hiện tại
    $sql_data = 'SELECT t.*, c.name as category_name ' . $base_sql . ' ORDER BY t.id DESC LIMIT ' . ($page - 1) * $per_page . ',' . $per_page;
    
    // FIX LỖI: Tách các bước prepare, execute và fetchAll
    $stmt_data = $db->prepare($sql_data);
    $stmt_data->execute($params);
    $results = $stmt_data->fetchAll();

    return [$results, $total_rows];
}

/**
 * Lấy thông tin một dụng cụ bằng ID
 * @param int $id
 * @return array|false
 */
function get_tool_by_id($id)
{
    global $db;
    $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_tool_slip_management_tools WHERE id = :id';
    return $db->prepare($sql)->execute([':id' => (int)$id])->fetch();
}

/**
 * Thêm một dụng cụ mới vào CSDL
 * @param array $data
 * @return int ID của dụng cụ vừa được thêm
 */
function add_new_tool($data)
{
    global $db;
    $sql = 'INSERT INTO ' . NV_PREFIXLANG . '_tool_slip_management_tools (category_id, tool_code, name, description, status, added_date)
            VALUES (:category_id, :tool_code, :name, :description, 1, ' . NV_CURRENTTIME . ')';

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
    $stmt->bindValue(':tool_code', $data['tool_code'], PDO::PARAM_STR);
    $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
    $stmt->execute();

    return $db->lastInsertId();
}

/**
 * Cập nhật thông tin một dụng cụ
 * @param int $id
 * @param array $data
 * @return int Số dòng bị ảnh hưởng
 */
function update_tool($id, $data)
{
    global $db;
    $sql = 'UPDATE ' . NV_PREFIXLANG . '_tool_slip_management_tools SET
            category_id = :category_id,
            tool_code = :tool_code,
            name = :name,
            description = :description
            WHERE id = :id';

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
    $stmt->bindValue(':tool_code', $data['tool_code'], PDO::PARAM_STR);
    $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    return $stmt->execute()->rowCount();
}