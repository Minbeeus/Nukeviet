<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Load language
$langfile = NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php';
include $langfile;

// Bật hiển thị lỗi (hữu ích khi lập trình)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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

// Handle return action
if ($nv_Request->isset_request('action', 'post') && $nv_Request->get_string('action', 'post') == 'return') {
    $slip_id = $nv_Request->get_int('slip_id', 'post', 0);
    if ($slip_id > 0) {
        // ... (Logic trả đồ giữ nguyên, không thay đổi)
        $db->query("UPDATE " . NV_PREFIXLANG . '_' . $module_data . "_borrowing_slips SET status = 'returned', return_date = '" . date('Y-m-d') . "' WHERE id = " . $slip_id);
        $tool_ids = $db->query("SELECT tool_id FROM " . NV_PREFIXLANG . '_' . $module_data . "_borrowing_slip_details WHERE slip_id = " . $slip_id)->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($tool_ids)) {
            $in_clause = implode(',', array_map('intval', $tool_ids));
            $db->query("UPDATE " . NV_PREFIXLANG . '_' . $module_data . "_tools SET status = 1 WHERE id IN (" . $in_clause . ")");
        }
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

// Xử lý form tạo phiếu mượn
$array = array();
$error = '';
$action = $nv_Request->get_title('action', 'get,post', '');
$id = $nv_Request->get_int('id', 'get,post', 0);

if ($action == 'add') {
    // TRƯỜNG HỢP 1: Xử lý SUBMIT FORM (POST)
    if ($nv_Request->isset_request('submit', 'post') or $nv_Request->isset_request('ajax_submit', 'post')) {
        // Chú ý: tên input student đã đổi thành 'student_id' (input ẩn)
        $array['student_id'] = $nv_Request->get_int('student_id', 'post', 0);
        $array['borrow_date'] = $nv_Request->get_title('borrow_date', 'post', date('Y-m-d'));
        $array['due_date'] = $nv_Request->get_title('due_date', 'post', '');
        $array['note'] = $nv_Request->get_textarea('note', '', NV_ALLOWED_HTML_TAGS);
        
        // Lấy danh sách tool_ids, loại bỏ các giá trị '0' (không chọn)
        $tool_ids_raw = $nv_Request->get_array('tool_ids', 'post', array());
        $tool_ids = array_filter(array_map('intval', $tool_ids_raw), function($val) {
            return $val > 0;
        });

        if ($array['student_id'] == 0) {
            $error = $lang_module['error_student_not_found']; // Cần thêm ngôn ngữ này
        } elseif (empty($array['due_date'])) {
            $error = $lang_module['error_due_date'];
        } elseif (empty($tool_ids)) {
            $error = $lang_module['error_tools'];
        } else {
            $db->beginTransaction();
            try {
                // ... (Logic lưu DB giữ nguyên, không thay đổi)
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

                    $sql_update_tool = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET status = :status WHERE id = :id';
                    $sth_update_tool = $db->prepare($sql_update_tool);
                    $sth_update_tool->bindValue(':status', 2, PDO::PARAM_INT); // 2 = Đang mượn
                    $sth_update_tool->bindValue(':id', (int)$tool_id, PDO::PARAM_INT);
                    $sth_update_tool->execute();
                }

                $db->commit();
                
                // Trả về JSON success nếu submit từ AJAX (của form modal)
                if ($nv_Request->isset_request('ajax_submit', 'post')) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $lang_module['error_save_success']]); // Cần thêm ngôn ngữ này
                    exit;
                }
                
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=borrowing');
            
            } catch (PDOException $e) {
                $db->rollBack();
                $error = $lang_module['error_save'];
                
                if ($nv_Request->isset_request('ajax_submit', 'post')) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $error . ': ' . $e->getMessage()]);
                    exit;
                }
            }
        }
        
        // Nếu submit từ AJAX và có lỗi
        if ($nv_Request->isset_request('ajax_submit', 'post')) {
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }
    }
    // TRƯỜNG HỢP 2: Xử lý YÊU CẦU FORM QUA AJAX (GET + AJAX)
    elseif ($nv_Request->isset_request('ajax', 'get')) {

        try {
            // Chỉ lấy danh sách tools
            $sql_tools = 'SELECT id, name, code as tool_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools WHERE status = 1 ORDER BY name';
            $tools = $db->query($sql_tools)->fetchAll();

        } catch (PDOException $e) {
            ob_clean();
            header('Content-Type: text/html');
            echo '<div class="alert alert-danger"><strong>Lỗi Database:</strong> ' . $e->getMessage() . '</div>';
            exit;
        }

        // Tạo options cho công cụ
        $tool_options = '<option value="0">-- ' . $lang_module['select_tool'] . ' --</option>'; // Cần thêm ngôn ngữ 'select_tool'
        if (!empty($tools)) {
            foreach ($tools as $tool) {
                $tool_options .= '<option value="' . $tool['id'] . '">' . $tool['name'] . ' (' . $tool['tool_code'] . ')</option>';
            }
        }
        
        $form_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=borrowing';
        $student_check_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=borrowing&action=find_student';

        // =================================================================
        // BẮT ĐẦU KHỐI HTML MỚI CỦA FORM (ĐÃ XÓA JS)
        // =================================================================
        
        $form_html = '
        <style>
            #student-info-display { margin-top: 10px; border: 1px solid #ddd; padding: 10px; border-radius: 4px; display: none; }
            #student-info-display.error { border-color: #dc3545; color: #dc3545; }
            #student-info-display.success { border-color: #28a745; }
            .tool-input-row { display: flex; margin-bottom: 5px; }
            .tool-input-row .form-control { flex-grow: 1; }
            .tool-input-row .btn-remove-tool { margin-left: 5px; }
        </style>

        <form id="add-slip-form" method="post" action="' . $form_url . '" 
            data-student-check-url="' . $student_check_url . '"
            data-lang-student-code-empty="' . $lang_module['error_student_code_empty'] . '"
            data-lang-searching="' . $lang_module['searching'] . '"
            data-lang-student="' . $lang_module['student'] . '"
            data-lang-remove="' . $lang_module['remove'] . '">

            <input type="hidden" name="action" value="add">
            <input type="hidden" name="ajax_submit" value="1"> 
            <input type="hidden" id="student-id-hidden" name="student_id" value="0">

            <div class="form-group">
                <label>' . $lang_module['student_code'] . '</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="student-code-input" placeholder="' . $lang_module['student_code_placeholder'] . '">
                    <div class="input-group-append">
                        <button class="btn btn-default" type="button" id="btn-check-student" title="' . $lang_module['check'] . '"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div id="student-info-display">
                    </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>' . $lang_module['borrow_date'] . '</label>
                        <input type="date" class="form-control" name="borrow_date" value="' . date('Y-m-d') . '" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>' . $lang_module['due_date'] . '</label>
                        <input type="date" class="form-control" name="due_date" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>' . $lang_module['tools'] . '</label>
                <div id="tool-inputs-container">
                    <div class="tool-input-row">
                        <select class="form-control tool-select" name="tool_ids[]">
                            ' . $tool_options . '
                        </select>
                        <button type="button" class="btn btn-danger btn-remove-tool" style="display: none;" title="' . $lang_module['remove'] . '"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="btn-add-tool-row" style="margin-top: 5px;">
                    <i class="fas fa-plus"></i> ' . $lang_module['add_tool'] . '
                </button>
            </div>

            <div class="form-group">
                <label>' . $lang_module['note'] . '</label>
                <textarea class="form-control" name="note" rows="3"></textarea>
            </div>
            
            <div class="form-group text-right">
                <button type="button" class="btn btn-secondary" onclick="hideModalById(\'tsmActionModal\', null);">' . $lang_global['cancel'] . '</button>
                <button type="button" class="btn btn-success" onclick="submitAddSlipForm()">' . $lang_global['save'] . '</button>
            </div>
        </form>
        ';
        
        // =================================================================
        // KẾT THÚC KHỐI HTML
        // =================================================================

        echo $form_html;
        exit;
    }
    // TRƯỜNG HỢP 3: Hiển thị TRANG FORM ĐẦY ĐỦ (GET, không AJAX, hoặc sau khi POST lỗi)
    else {
        // ... (Logic này giữ nguyên)
        $sql = 'SELECT id, full_name, student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_students ORDER BY full_name';
        $students = $db->query($sql)->fetchAll();
        if (empty($students) && empty($error)) {
            $error = $lang_module['error_no_students'];
        } else {
            foreach ($students as $student) {
                $xtpl->assign('STUDENT', $student);
                $xtpl->parse('main.form.student');
            }
        }
        $sql = 'SELECT id, name, code as tool_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools WHERE status = 1 ORDER BY name';
        $tools = $db->query($sql)->fetchAll();
        if (empty($tools) && empty($error)) {
            $error = $lang_module['error_no_tools_available'];
        } else {
            foreach ($tools as $tool) {
                $xtpl->assign('TOOL', $tool);
                $xtpl->parse('main.form.tool');
            }
        }
        $xtpl->assign('ACTION', $action);
        $xtpl->assign('DATA', $array);
        if ($error) {
            $xtpl->assign('ERROR', $error);
            $xtpl->parse('main.form.error');
        }
        $xtpl->parse('main.form');
    }

// =================================================================
// BẮT ĐẦU KHỐI LOGIC MỚI ĐỂ TRA CỨU SINH VIÊN
// =================================================================

} elseif ($action == 'find_student' && $nv_Request->isset_request('ajax', 'get')) {
    
    ob_clean();
    header('Content-Type: application/json');
    
    $student_code = $nv_Request->get_title('code', 'get', '');
    if (empty($student_code)) {
        echo json_encode(['success' => false, 'message' => $lang_module['error_student_code_empty']]);
        exit;
    }

    try {
        $sql = 'SELECT id, full_name, student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_students WHERE student_code = :student_code';
        $sth = $db->prepare($sql);
        $sth->bindParam(':student_code', $student_code, PDO::PARAM_STR);
        $sth->execute();
        $student = $sth->fetch();

        if ($student) {
            echo json_encode(['success' => true, 'student' => $student]);
        } else {
            echo json_encode(['success' => false, 'message' => $lang_module['error_student_not_found']]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()]);
    }
    exit; // Rất quan trọng

// =================================================================
// KẾT THÚC KHỐI LOGIC MỚI
// =================================================================

//
// XÓA KHỐI LẶP Ở ĐÂY (trước đây là dòng 417-444)
//

} elseif ($action == 'view') {
    // ... (Logic xem chi tiết giữ nguyên)
    $sql = 'SELECT bs.*, s.full_name, s.student_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips bs LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_students s ON bs.student_id = s.id WHERE bs.id = :id';
    $sth = $db->prepare($sql);
    $sth->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $sth->execute();
    $slip = $sth->fetch();
    if (!$slip) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
    $sql_tools = 'SELECT t.id, t.code, t.name, c.name as category_name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slip_details sd LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tools t ON sd.tool_id = t.id LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_categories c ON t.category_id = c.id WHERE sd.slip_id = :slip_id';
    $sth_tools = $db->prepare($sql_tools);
    $sth_tools->bindValue(':slip_id', (int)$id, PDO::PARAM_INT);
    $sth_tools->execute();
    $tools = $sth_tools->fetchAll();
    $slip['status_text'] = isset($lang_module[$slip['status']]) ? $lang_module[$slip['status']] : $slip['status'];
    $slip['borrow_date'] = nv_date('d/m/Y', strtotime($slip['borrow_date']));
    $slip['due_date'] = nv_date('d/m/Y', strtotime($slip['due_date']));
    $slip['return_date'] = $slip['return_date'] ? nv_date('d/m/Y', strtotime($slip['return_date'])) : '';
    $xtpl->assign('SLIP', $slip);
    foreach ($tools as $tool) {
        $xtpl->assign('TOOL', $tool);
        $xtpl->parse('main.view.tool');
    }
    $xtpl->parse('main.view');

} else {
    // Danh sách phiếu mượn (giữ nguyên)
    // ... (Toàn bộ khối 'else' hiển thị danh sách giữ nguyên)
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'get', 1);
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=borrowing';
    $filter = $nv_Request->get_title('filter', 'get', '');
    if (!empty($filter)) {
        $base_url .= '&filter=' . urlencode($filter);
    }
    try {
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
        try {
            $sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slip_details WHERE slip_id = :slip_id';
            $sth_count = $db->prepare($sql);
            $sth_count->bindValue(':slip_id', (int)$slip['id'], PDO::PARAM_INT);
            $sth_count->execute();
            $slip['tool_count'] = (int)$sth_count->fetchColumn();
        } catch (PDOException $e) {
            $slip['tool_count'] = 0;
        }
        if ($slip['status'] == 'borrowing' && strtotime($slip['due_date']) < time()) {
            try {
                $sql_overdue = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_borrowing_slips SET status = :status WHERE id = :id';
                $sth_overdue = $db->prepare($sql_overdue);
                $sth_overdue->bindValue(':status', 'overdue', PDO::PARAM_STR);
                $sth_overdue->bindValue(':id', (int)$slip['id'], PDO::PARAM_INT);
                $sth_overdue->execute();
            } catch (PDOException $e) {}
            $slip['status'] = 'overdue';
            $slip['status_text'] = isset($lang_module['overdue']) ? $lang_module['overdue'] : 'Quá hạn';
        }
        $xtpl->assign('SLIP', $slip);
        $xtpl->parse('main.list.slip.view_btn');
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

$contents .= '<script src="/nukeviet/modules/tool-slip-management/js/admin.js"></script>';
$contents .= '<script>console.log("JS added to contents");</script>';

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
