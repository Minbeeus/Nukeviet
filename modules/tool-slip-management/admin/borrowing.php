<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Load language
$langfile = NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php';
include $langfile;

$page_title = $lang_module['borrowing_management'];

$xtpl = new XTemplate('borrowing.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);

// CSS loaded in template

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

// Xử lý form tạo phiếu mượn
$array = array();
$error = '';
$action = $nv_Request->get_title('action', 'post', '');
$id = $nv_Request->get_int('id', 'get,post', 0);

if ($action == 'add') {
    if ($nv_Request->isset_request('submit', 'post')) {
        $array['student_id'] = $nv_Request->get_int('student_id', 'post', 0);
        $array['borrow_date'] = $nv_Request->get_title('borrow_date', 'post', date('Y-m-d'));
        $array['due_date'] = $nv_Request->get_title('due_date', 'post', '');
        $array['note'] = $nv_Request->get_textarea('note', '', NV_ALLOWED_HTML_TAGS);
        $tool_ids = $nv_Request->get_array('tool_ids', 'post', array());

        if ($array['student_id'] == 0) {
            $error = $lang_module['error_student'];
        } elseif (empty($array['due_date'])) {
            $error = $lang_module['error_due_date'];
        } elseif (empty($tool_ids)) {
            $error = $lang_module['error_tools'];
        } else {
            $db->beginTransaction();
            try {
                $sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips (student_id, borrow_date, due_date, note) VALUES (:student_id, :borrow_date, :due_date, :note)';
                $sth = $db->prepare($sql);
                $sth->bindParam(':student_id', $array['student_id'], PDO::PARAM_INT);
                $sth->bindParam(':borrow_date', $array['borrow_date'], PDO::PARAM_STR);
                $sth->bindParam(':due_date', $array['due_date'], PDO::PARAM_STR);
                $sth->bindParam(':note', $array['note'], PDO::PARAM_STR);
                $sth->execute();
                $slip_id = $db->lastInsertId();

                foreach ($tool_ids as $tool_id) {
                    $sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slip_details (slip_id, tool_id) VALUES (:slip_id, :tool_id)';
                    $sth = $db->prepare($sql);
                    $sth->bindParam(':slip_id', $slip_id, PDO::PARAM_INT);
                    $sth->bindParam(':tool_id', $tool_id, PDO::PARAM_INT);
                    $sth->execute();

                    // Cập nhật trạng thái công cụ
                    $sql_update_tool = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET status = :status WHERE id = :id';
                    $sth_update_tool = $db->prepare($sql_update_tool);
                    $sth_update_tool->bindValue(':status', 'borrowed', PDO::PARAM_STR);
                    $sth_update_tool->bindValue(':id', (int)$tool_id, PDO::PARAM_INT);
                    $sth_update_tool->execute();
                }

                $db->commit();
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=borrowing');
            } catch (PDOException $e) {
                $db->rollBack();
                $error = $lang_module['error_save'];
            }
        }
    }

    // Danh sách students
    $sql = 'SELECT id, full_name, student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_students ORDER BY full_name';
    $students = $db->query($sql)->fetchAll();
    foreach ($students as $student) {
        $xtpl->assign('STUDENT', $student);
        $xtpl->parse('main.form.student');
    }

    // Danh sách tools available
    $sql = 'SELECT id, name, tool_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools WHERE status = "available" ORDER BY name';
    $tools = $db->query($sql)->fetchAll();
    foreach ($tools as $tool) {
        $xtpl->assign('TOOL', $tool);
        $xtpl->parse('main.form.tool');
    }

    $xtpl->assign('ACTION', $action);
    $xtpl->assign('DATA', $array);

    if ($error) {
        $xtpl->assign('ERROR', $error);
        $xtpl->parse('main.form.error');
    }

    $xtpl->parse('main.form');
} elseif ($action == 'return') {
    // Xử lý trả đồ
    $slip_id = $nv_Request->get_int('slip_id', 'post', 0);
    if ($slip_id > 0) {
        $return_date = date('Y-m-d');
        // Update slip return date and status safely
        $sql_return = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips SET return_date = :return_date, status = :status WHERE id = :id';
        $sth_return = $db->prepare($sql_return);
        $sth_return->bindValue(':return_date', $return_date, PDO::PARAM_STR);
        $sth_return->bindValue(':status', 'returned', PDO::PARAM_STR);
        $sth_return->bindValue(':id', (int)$slip_id, PDO::PARAM_INT);
        $sth_return->execute();

        // Cập nhật trạng thái công cụ
        $sql = 'SELECT tool_id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slip_details WHERE slip_id = :slip_id';
        $sth = $db->prepare($sql);
        $sth->bindValue(':slip_id', (int)$slip_id, PDO::PARAM_INT);
        $sth->execute();
        $tools = $sth->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tools as $tool_id) {
            $sql_update_tool = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET status = :status WHERE id = :id';
            $sth_update_tool = $db->prepare($sql_update_tool);
            $sth_update_tool->bindValue(':status', 'available', PDO::PARAM_STR);
            $sth_update_tool->bindValue(':id', (int)$tool_id, PDO::PARAM_INT);
            $sth_update_tool->execute();
        }
    }
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=borrowing');
} else {
    // Danh sách phiếu mượn
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'get', 1);
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=borrowing';
    // Preserve optional filter in base_url
    $filter = $nv_Request->get_title('filter', 'get', '');
    if (!empty($filter)) {
        $base_url .= '&filter=' . urlencode($filter);
    }

    try {
        // Build base SQL and append WHERE if filtering overdue
        $sql = 'SELECT bs.*, s.full_name, s.student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips bs LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_students s ON bs.student_id = s.id';
        $params = array();
        if ($filter === 'overdue') {
            $sql .= ' WHERE bs.status = :status';
            $params[':status'] = 'overdue';
        }
        $sql .= ' ORDER BY bs.id DESC LIMIT :limit OFFSET :offset';

        $sth = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $sth->bindValue($k, $v, PDO::PARAM_STR);
        }
        $sth->bindValue(':limit', (int)$per_page, PDO::PARAM_INT);
        $sth->bindValue(':offset', (int)(($page - 1) * $per_page), PDO::PARAM_INT);
        $sth->execute();
        $slips = $sth->fetchAll();
    } catch (PDOException $e) {
        $slips = array();
    }

    foreach ($slips as $slip) {
        $slip['status_text'] = isset($lang_module[$slip['status']]) ? $lang_module[$slip['status']] : $slip['status'];
        // Đếm số công cụ
        try {
            $sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slip_details WHERE slip_id = :slip_id';
            $sth_count = $db->prepare($sql);
            $sth_count->bindValue(':slip_id', (int)$slip['id'], PDO::PARAM_INT);
            $sth_count->execute();
            $slip['tool_count'] = (int)$sth_count->fetchColumn();
        } catch (PDOException $e) {
            $slip['tool_count'] = 0;
        }

        // Kiểm tra quá hạn
        if ($slip['status'] == 'borrowing' && strtotime($slip['due_date']) < time()) {
        try {
            $sql_overdue = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips SET status = :status WHERE id = :id';
            $sth_overdue = $db->prepare($sql_overdue);
            $sth_overdue->bindValue(':status', 'overdue', PDO::PARAM_STR);
            $sth_overdue->bindValue(':id', (int)$slip['id'], PDO::PARAM_INT);
            $sth_overdue->execute();
        } catch (PDOException $e) {
                // Ignore if table doesn't exist
            }
            $slip['status'] = 'overdue';
            $slip['status_text'] = isset($lang_module['overdue']) ? $lang_module['overdue'] : 'Quá hạn';
        }

        $xtpl->assign('SLIP', $slip);
        if ($slip['status'] == 'borrowing' || $slip['status'] == 'overdue') {
            $xtpl->parse('main.list.slip.return_btn');
        }
        $xtpl->parse('main.list.slip');
    }

    try {
        $sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips';
        $sth_count_all = $db->prepare($sql);
        $sth_count_all->execute();
        $num_items = (int)$sth_count_all->fetchColumn();
    } catch (PDOException $e) {
        $num_items = 0;
    }

    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.list.generate_page');
    }

    $xtpl->parse('main.list');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
