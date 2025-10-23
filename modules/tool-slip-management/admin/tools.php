<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Load language
$langfile = NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php';
include $langfile;

$page_title = $lang_module['tools_management'];

$xtpl = new XTemplate('tools.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);

// CSS loaded in template

// Định nghĩa options status
$status_options = array(1 => 'Sẵn có', 2 => 'Đang mượn', 3 => 'Đang bảo trì', 4 => 'Đã thanh lý');

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

// Xử lý form thêm/sửa
$array = array();
$error = '';
$action = $nv_Request->get_title('action', 'post', '');
$id = $nv_Request->get_int('id', 'get,post', 0);

if ($action == 'add' || $action == 'edit') {
    if ($action == 'edit') {
        $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools WHERE id = :id';
        $sth = $db->prepare($sql);
        $sth->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $sth->execute();
        $array = $sth->fetch();
        if (!$array) {
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools');
        }
    }

    if ($nv_Request->isset_request('submit', 'post')) {
        $array['code'] = $nv_Request->get_title('code', 'post', '');
        $array['name'] = $nv_Request->get_title('name', 'post', '');
        $array['description'] = $nv_Request->get_editor('description', '', NV_ALLOWED_HTML_TAGS);
        $array['category_id'] = $nv_Request->get_int('category_id', 'post', 0);
        $array['status'] = $nv_Request->get_int('status', 'post', 1);
        $array['added_date'] = $nv_Request->get_title('added_date', 'post', date('Y-m-d'));

        if (empty($array['code'])) {
            $error = $lang_module['error_tool_code'];
        } elseif (empty($array['name'])) {
            $error = $lang_module['error_name'];
        } elseif ($array['category_id'] == 0) {
            $error = $lang_module['error_category'];
        } else {
            if ($action == 'add') {
                $sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_tools (code, name, description, category_id, status, added_date) VALUES (:code, :name, :description, :category_id, :status, :added_date)';
            } else {
                $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET code = :code, name = :name, description = :description, category_id = :category_id, status = :status, added_date = :added_date WHERE id = :id';
                $sth->bindParam(':id', $id, PDO::PARAM_INT);
            }
            try {
                $sth = $db->prepare($sql);
                $sth->bindParam(':code', $array['code'], PDO::PARAM_STR);
                $sth->bindParam(':name', $array['name'], PDO::PARAM_STR);
                $sth->bindParam(':description', $array['description'], PDO::PARAM_STR);
                $sth->bindParam(':category_id', $array['category_id'], PDO::PARAM_INT);
                $sth->bindParam(':status', $array['status'], PDO::PARAM_INT);
                $sth->bindParam(':added_date', $array['added_date'], PDO::PARAM_STR);
                $sth->execute();
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools');
            } catch (PDOException $e) {
                $error = $lang_module['error_save'];
            }
        }
    }

    $xtpl->assign('ACTION', $action);
    $xtpl->assign('DATA', $array);

    // Danh sách categories
    $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
    $categories = $db->query($sql)->fetchAll();
    foreach ($categories as $category) {
        $xtpl->assign('CATEGORY', $category);
        $xtpl->parse('main.form.category');
    }

    // Status options đã define ở đầu
    foreach ($status_options as $key => $value) {
        $xtpl->assign('STATUS_KEY', $key);
        $xtpl->assign('STATUS_VALUE', $value);
        $xtpl->assign('STATUS_SELECTED', ($array['status'] == $key) ? 'selected' : '');
        $xtpl->parse('main.form.status');
    }

    if ($error) {
        $xtpl->assign('ERROR', $error);
        $xtpl->parse('main.form.error');
    }

    $xtpl->parse('main.form');
} else {
    // Danh sách tools
    // Quick status change handler (via GET: action=change_status&id=...&status=...)
    $get_action = $nv_Request->get_title('action', 'get', '');
    if ($get_action == 'change_status') {
        // Determine if this is an AJAX POST
        $is_post = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT';
        $change_id = $nv_Request->get_int('id', $is_post ? 'post' : 'get', 0);
        $new_status = $nv_Request->get_int('status', $is_post ? 'post' : 'get', 0);
        $result = array('success' => false);
        if ($change_id > 0 && isset($status_options[$new_status])) {
            try {
                $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET status = :status WHERE id = :id';
                $sth = $db->prepare($sql);
                $sth->bindValue(':status', $new_status, PDO::PARAM_INT);
                $sth->bindValue(':id', $change_id, PDO::PARAM_INT);
                $sth->execute();

                $result['success'] = true;
                $result['updated_status'] = $new_status;
                $result['updated_status_text'] = $status_options[$new_status];
                // map class
                $map = array(1 => 'success', 2 => 'warning', 3 => 'info', 4 => 'danger');
                $result['updated_status_class'] = $map[$new_status] ?? 'secondary';
            } catch (PDOException $e) {
                $result['error'] = $e->getMessage();
            }
        }

        if ($is_post) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        // fallback for GET links
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools');
    }

    $per_page = 20;
    $page = $nv_Request->get_int('page', 'get', 1);
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools';

    $q = $nv_Request->get_title('q', 'get', '');
    $category_filter = $nv_Request->get_int('category_id', 'get', 0);
    $status_filter = $nv_Request->get_int('status', 'get', 0);

    $where_clauses = [];
    $params = [];

    if (!empty($q)) {
        $where_clauses[] = '(t.name LIKE :q OR t.code LIKE :q)';
        $params[':q'] = '%' . $q . '%';
    }
    if ($category_filter > 0) {
        $where_clauses[] = 't.category_id = :category_id';
        $params[':category_id'] = $category_filter;
    }
    if ($status_filter > 0) {
        $where_clauses[] = 't.status = :status';
        $params[':status'] = $status_filter;
    }

    $where_sql = '';
    if (!empty($where_clauses)) {
        $where_sql = ' AND ' . implode(' AND ', $where_clauses);
    }

    $sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools t WHERE 1=1' . $where_sql;
    $sth = $db->prepare($sql);
    foreach ($params as $k => $v) {
        $sth->bindValue($k, $v);
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $sql = 'SELECT t.*, c.name as category_name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools t LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_categories c ON t.category_id = c.id WHERE 1=1' . $where_sql . ' ORDER BY t.id DESC LIMIT :limit OFFSET :offset';
    $sth = $db->prepare($sql);
    foreach ($params as $k => $v) {
        $sth->bindValue($k, $v);
    }
    $sth->bindValue(':limit', (int)$per_page, PDO::PARAM_INT);
    $sth->bindValue(':offset', (int)(($page - 1) * $per_page), PDO::PARAM_INT);
    $sth->execute();
    $tools = $sth->fetchAll();

    foreach ($tools as $tool) {
        $tool['tool_code'] = $tool['code'];
        $tool['status_text'] = $status_options[$tool['status']] ?? $tool['status'];
        switch ($tool['status']) {
            case 1:
                $tool['status_class'] = 'success';
                break;
            case 2:
                $tool['status_class'] = 'warning';
                break;
            case 3:
                $tool['status_class'] = 'info';
                break;
            case 4:
                $tool['status_class'] = 'danger';
                break;
            default:
                $tool['status_class'] = 'secondary';
        }
        $xtpl->assign('TOOL', $tool);
        // status options for dropdown
        foreach ($status_options as $skey => $sval) {
            $xtpl->assign('STATUS_KEY', $skey);
            $xtpl->assign('STATUS_VALUE', $sval);
            $xtpl->parse('main.list.status_option');
        }
        $xtpl->parse('main.list.tool');
    }

    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.list.generate_page');
    }

    // Filters
    $xtpl->assign('Q', $q);
    $xtpl->assign('CATEGORY_FILTER', $category_filter);
    $xtpl->assign('STATUS_FILTER', $status_filter);

    $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
    $categories = $db->query($sql)->fetchAll();
    foreach ($categories as $category) {
        $xtpl->assign('CATEGORY', $category);
        $xtpl->assign('SELECTED', ($category['id'] == $category_filter) ? 'selected' : '');
        $xtpl->parse('main.list.category_filter');
    }

    foreach ($status_options as $key => $value) {
        $xtpl->assign('STATUS_KEY', $key);
        $xtpl->assign('STATUS_VALUE', $value);
        $xtpl->assign('SELECTED', ($key == $status_filter) ? 'selected' : '');
        $xtpl->parse('main.list.status_filter');
    }

    $xtpl->parse('main.list');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
