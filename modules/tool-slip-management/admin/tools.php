<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

/**
 * Tools Management Module Admin Controller
 * Handles CRUD operations for tools, categories, and maintenance records
 */

// Load language file
$langfile = NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php';
include $langfile;

$page_title = $lang_module['tools_management'];

// Initialize template
$xtpl = new XTemplate('tools.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);

/**
 * Get status options array
 * @return array Status options with keys 1-4
 */
function get_status_options() {
    return array(
        1 => 'Sẵn có',
        2 => 'Đang mượn',
        3 => 'Đang bảo trì',
        4 => 'Đã thanh lý'
    );
}

/**
 * Get all categories from database
 * @return array Categories array
 */
function get_categories() {
    global $db, $module_data;
    $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
    return $db->query($sql)->fetchAll();
}

/**
 * Get status class for Bootstrap badge
 * @param int $status Status code
 * @return string Bootstrap class
 */
function get_status_class($status) {
    $map = array(
        1 => 'success',
        2 => 'warning',
        3 => 'info',
        4 => 'danger'
    );
    return $map[$status] ?? 'secondary';
}

/**
 * Ensure sample categories exist in database
 */
function ensure_sample_categories() {
    global $db, $module_data;
    $sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories';
    $count = $db->query($sql)->fetchColumn();
    if ($count == 0) {
        $sample_categories = ['Máy tính', 'Văn phòng phẩm', 'Công cụ', 'Khác'];
        foreach ($sample_categories as $name) {
            $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_categories (name) VALUES (' . $db->quote($name) . ')');
        }
    }
}

/**
 * Check if tools table exists
 * @return bool True if exists
 */
function check_tools_table_exists() {
    global $db, $module_data;
    $table_name = NV_PREFIXLANG . '_' . $module_data . '_tools';
    return $db->query("SHOW TABLES LIKE '" . $table_name . "'")->rowCount() > 0;
}

/**
 * Handle form validation and database operations for add/edit tool
 * @param string $action 'add' or 'edit'
 * @param int $id Tool ID for edit
 * @return array Result with success/error message
 */
function process_tool_form($action, $id = 0) {
    global $db, $module_data, $nv_Request, $lang_module, $admin_info;

    $array = array();

    // Get form data
    $array['code'] = $nv_Request->get_title('tool_code', 'post', '');
    $array['name'] = $nv_Request->get_title('name', 'post', '');
    $array['description'] = $nv_Request->get_title('description', 'post', '');
    $array['category_id'] = $nv_Request->get_int('category_id', 'post', 0);
    $array['status'] = $nv_Request->get_int('status', 'post', 1);

    // Validation
    if (empty($array['code'])) {
        return ['success' => false, 'message' => $lang_module['error_tool_code']];
    }
    if (empty($array['name'])) {
        return ['success' => false, 'message' => $lang_module['error_name']];
    }
    if ($array['category_id'] == 0) {
        return ['success' => false, 'message' => $lang_module['error_category']];
    }

    // Check code uniqueness for add
    if ($action == 'add') {
        $check = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools WHERE code = ' . $db->quote($array['code']))->fetchColumn();
        if ($check > 0) {
            return ['success' => false, 'message' => 'Mã công cụ đã tồn tại.'];
        }
    }

    // Check category exists
    $check_cat = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories WHERE id = ' . $array['category_id'])->fetchColumn();
    if ($check_cat == 0) {
        return ['success' => false, 'message' => 'Danh mục không tồn tại.'];
    }

    // Prepare SQL
    if ($action == 'add') {
        $sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_tools (code, name, description, category_id, status) VALUES (?, ?, ?, ?, ?)';
        $params = array($array['code'], $array['name'], $array['description'], $array['category_id'], $array['status']);
    } else {
        $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET code = ?, name = ?, description = ?, category_id = ?, status = ? WHERE id = ?';
        $params = array($array['code'], $array['name'], $array['description'], $array['category_id'], $array['status'], $id);
    }

    try {
        $sth = $db->prepare($sql);
        $sth->execute($params);

        // Log success
        error_log('TOOL UPDATE SUCCESS - Action: ' . $action . ', ID: ' . $id);

        return ['success' => true, 'message' => 'Cập nhật thành công!'];
    } catch (PDOException $e) {
        // Log error
        error_log('TOOL UPDATE ERROR - Action: ' . $action . ', ID: ' . $id . ', Error: ' . $e->getMessage());

        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Initialize status options
$status_options = get_status_options();

// Check table exists
if (!check_tools_table_exists()) {
    $xtpl->assign('MESSAGE', $lang_module['module_not_installed_message']);
    $xtpl->parse('main.not_installed');
    $contents = $xtpl->text('main');
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
    exit;
}





// Ensure sample categories
ensure_sample_categories();

/**
 * Main action processing
 */
$action = $nv_Request->get_title('action', 'get,post', '');
$id = $nv_Request->get_int('id', 'get,post', 0);
$array = array();
$error = '';

/**
 * Handle add/edit tool actions
 */
if ($action == 'add' || $action == 'edit') {
    // Load existing data for edit
    if ($action == 'edit') {
        $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools WHERE id = :id';
        $sth = $db->prepare($sql);
        $sth->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $sth->execute();
        $array = $sth->fetch();
        if (!$array) {
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools');
        }
        $array['tool_code'] = $array['code'];
    }

    // Process form submission
    if ($nv_Request->isset_request('submit', 'post')) {
        $result = process_tool_form($action, $id);
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    $xtpl->assign('ACTION', $action);
    $xtpl->assign('DATA', $array);

    // Danh sách categories
    $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
    $categories = $db->query($sql)->fetchAll();
    foreach ($categories as $category) {
        $xtpl->assign('CATEGORY', $category);
        $xtpl->assign('CATEGORY_SELECTED', ($category['id'] == $array['category_id']) ? 'selected' : '');
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

    // If AJAX, return form HTML
    if ($nv_Request->isset_request('ajax', 'get')) {
        ob_clean();
        header('Content-Type: text/html');

        // Build categories options
        $category_options = '<option value="0">-- Chọn danh mục --</option>';
        $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
        $categories = $db->query($sql)->fetchAll();
        foreach ($categories as $cat) {
            $selected = ($cat['id'] == $array['category_id']) ? 'selected' : '';
            $category_options .= '<option value="' . $cat['id'] . '" ' . $selected . '>' . $cat['name'] . '</option>';
        }

        // Build status options
        $status_options_html = '';
        foreach ($status_options as $key => $value) {
            $selected = ($array['status'] == $key) ? 'selected' : '';
            $status_options_html .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }

        $form_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools';

        $form_html = '
        <form id="edit-tool-form" method="post" action="' . $form_url . '">
            <input type="hidden" name="action" value="' . $action . '">
            <input type="hidden" name="id" value="' . $id . '">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tool_code"><i class="fas fa-hashtag"></i> Mã công cụ</label>
                        <input type="text" class="form-control" id="tool_code" name="tool_code" value="' . $array['tool_code'] . '" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag"></i> Tên công cụ</label>
                        <input type="text" class="form-control" id="name" name="name" value="' . $array['name'] . '" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3">' . $array['description'] . '</textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id"><i class="fas fa-folder"></i> Danh mục</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            ' . $category_options . '
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status"><i class="fas fa-info-circle"></i> Trạng thái</label>
                        <select class="form-control" id="status" name="status" required>
                            ' . $status_options_html . '
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group text-right">
                <button type="button" class="btn btn-secondary" onclick="hideModalById(\'tsmActionModal\', null);">Hủy</button>
                <button type="button" class="btn btn-success" onclick="submitEditForm()"><i class="fas fa-save"></i> Lưu</button>
            </div>
        </form>';

        echo $form_html;
        exit;
    }
/**
 * Handle view tool details action
 */
} elseif ($action == 'view') {
    // Get tool with category info
    $sql = 'SELECT t.*, c.name as category_name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools t LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_categories c ON t.category_id = c.id WHERE t.id = :id';
    $sth = $db->prepare($sql);
    $sth->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $sth->execute();
    $array = $sth->fetch();

    if (!$array) {
        if ($nv_Request->isset_request('ajax', 'get')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Tool not found']);
            exit;
        }
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools');
    }

    // Prepare display data
    $array['tool_code'] = $array['code'];
    $array['status_text'] = $status_options[$array['status']] ?? $array['status'];
    $array['status_class'] = get_status_class($array['status']);
    $array['added_date'] = nv_date('d/m/Y', strtotime($array['added_date'] ?? date('Y-m-d')));

    $xtpl->assign('TOOL', $array);
    $xtpl->parse('main.view');

    // If AJAX, return HTML
    if ($nv_Request->isset_request('ajax', 'get')) {
    error_log('TOOL VIEW AJAX - Starting response for ID: ' . $id);
    ob_clean();
    header('Content-Type: text/html');
    error_log('TOOL VIEW AJAX - Headers sent for ID: ' . $id);
    $view_html = '
    <div class="card shadow mb-4">
    <div class="card-body">
    <div class="row">
    <div class="col-md-6">
    <p><strong>Mã công cụ:</strong> ' . $array['tool_code'] . '</p>
    <p><strong>Tên công cụ:</strong> ' . $array['name'] . '</p>
    <p><strong>Danh mục:</strong> ' . $array['category_name'] . '</p>
    </div>
    <div class="col-md-6">
    <p><strong>Trạng thái:</strong> <span class="badge bg-' . $array['status_class'] . '">' . $array['status_text'] . '</span></p>
    <p><strong>Ngày thêm:</strong> ' . $array['added_date'] . '</p>
    </div>
    </div>
    <div class="row">
    <div class="col-md-12">
    <p><strong>Mô tả:</strong></p>
    <div>' . $array['description'] . '</div>
    </div>
    </div>
    </div>
    </div>';
    echo $view_html;
    exit;
    }
} elseif ($action == 'edit') {
    // AJAX form for edit tool
    if ($nv_Request->isset_request('ajax', 'get')) {
        error_log('TOOL EDIT AJAX - ID: ' . $id);
        ob_clean();
        header('Content-Type: text/html');

        $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools WHERE id = :id';
        $sth = $db->prepare($sql);
        $sth->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $sth->execute();
        $array = $sth->fetch();

        if (!$array) {
            echo '<div class="alert alert-danger">Công cụ không tồn tại</div>';
            exit;
        }

        $array['tool_code'] = $array['code'];

        // Get categories
        $category_options = '<option value="0">-- Chọn danh mục --</option>';
        $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
        $categories = $db->query($sql)->fetchAll();
        foreach ($categories as $cat) {
            $selected = ($cat['id'] == $array['category_id']) ? 'selected' : '';
            $category_options .= '<option value="' . $cat['id'] . '" ' . $selected . '>' . $cat['name'] . '</option>';
        }

        // Status options
        $status_options_html = '';
        $status_options = array(1 => 'Sẵn có', 2 => 'Đang mượn', 3 => 'Đang bảo trì', 4 => 'Đã thanh lý');
        foreach ($status_options as $key => $value) {
            $selected = ($key == $array['status']) ? 'selected' : '';
            $status_options_html .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }

        $form_url = '/nukeviet/admin/index.php?nv=tool-slip-management&op=tools';
        $form_html = '
        <form id="edit-tool-form" method="post" action="' . $form_url . '">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="' . $id . '">
        <input type="hidden" name="submit" value="submit">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tool_code"><i class="fas fa-hashtag"></i> Mã công cụ</label>
                        <input type="text" class="form-control" id="tool_code" name="tool_code" value="' . $array['tool_code'] . '" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag"></i> Tên công cụ</label>
                        <input type="text" class="form-control" id="name" name="name" value="' . $array['name'] . '" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3">' . $array['description'] . '</textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id"><i class="fas fa-folder"></i> Danh mục</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                        ' . $category_options . '
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status"><i class="fas fa-info-circle"></i> Trạng thái</label>
                        <select class="form-control" id="status" name="status" required>
                        ' . $status_options_html . '
                        </select>
                    </div>
                </div>
            </div>
            <!-- BEGIN: error -->
            <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="edit-tool-error">
                <i class="fas fa-exclamation-circle"></i> <span id="edit-tool-error-text"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <!-- END: error -->
            <div class="form-group text-right">
                <button type="button" class="btn btn-secondary" onclick="hideModalById(\'tsmActionModal\', null);">Hủy</button>
                <button type="button" class="btn btn-success" onclick="submitEditForm()"><i class="fas fa-save"></i> Lưu</button>
            </div>
        </form>';
        echo $form_html;
        exit;
    }
} elseif ($action == 'add') {
    // AJAX form for add tool
    if ($nv_Request->isset_request('ajax', 'get')) {
        ob_clean();
        header('Content-Type: text/html');
        $array = array('tool_code' => '', 'name' => '', 'description' => '', 'category_id' => 0, 'status' => 1, 'added_date' => date('Y-m-d'));
        $category_options = '<option value="0">-- Chọn danh mục --</option>';
        $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
        $categories = $db->query($sql)->fetchAll();
        foreach ($categories as $cat) {
            $category_options .= '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
        }
        $status_options_html = '';
        $status_options = array(1 => 'Sẵn có', 2 => 'Đang mượn', 3 => 'Đang bảo trì', 4 => 'Đã thanh lý');
        foreach ($status_options as $key => $value) {
            $selected = ($key == 1) ? 'selected' : '';
            $status_options_html .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $form_url = '/nukeviet/admin/index.php?nv=tool-slip-management&op=tools';
        $form_html = '
        <form id="add-tool-form" method="post" action="' . $form_url . '">
        <input type="hidden" name="action" value="add">
            <input type="hidden" name="submit" value="submit">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tool_code"><i class="fas fa-hashtag"></i> Mã công cụ</label>
                        <input type="text" class="form-control" id="tool_code" name="tool_code" value="' . $array['tool_code'] . '" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag"></i> Tên công cụ</label>
                        <input type="text" class="form-control" id="name" name="name" value="' . $array['name'] . '" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3">' . $array['description'] . '</textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id"><i class="fas fa-folder"></i> Danh mục</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            ' . $category_options . '
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status"><i class="fas fa-info-circle"></i> Trạng thái</label>
                        <select class="form-control" id="status" name="status" required>
                            ' . $status_options_html . '
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group text-right">
                <button type="button" class="btn btn-secondary" onclick="hideModalById(\'tsmActionModal\', null);">Hủy</button>
                <button type="button" class="btn btn-success" onclick="submitAddForm()"><i class="fas fa-save"></i> Lưu</button>
            </div>
        </form>';
        echo $form_html;
        exit;
    }
/**
 * Handle maintenance/disposal actions
 */
} elseif ($action == 'maintenance' || $action == 'disposal') {
    // Get tool data
    $tool_id = $nv_Request->get_int('tool_id', 'get,post', 0);
    $sql = 'SELECT t.*, c.name as category_name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools t LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_categories c ON t.category_id = c.id WHERE t.id = :id';
    $sth = $db->prepare($sql);
    $sth->bindValue(':id', (int)$tool_id, PDO::PARAM_INT);
    $sth->execute();
    $array = $sth->fetch();
    if (!$array) {
        if ($nv_Request->isset_request('ajax', 'get')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Tool not found']);
            exit;
        }
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=tools');
    }
    $array['tool_code'] = $array['code'];
    $array['status_text'] = $status_options[$array['status']] ?? $array['status'];
    switch ($array['status']) {
        case 1: $array['status_class'] = 'success'; break;
        case 2: $array['status_class'] = 'warning'; break;
        case 3: $array['status_class'] = 'info'; break;
        case 4: $array['status_class'] = 'danger'; break;
        default: $array['status_class'] = 'secondary';
    }
    $array['added_date'] = nv_date('d/m/Y', strtotime($array['added_date'] ?? date('Y-m-d')));

    // Handle POST submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $reason = $nv_Request->get_title('reason', 'post', '');
        if (empty($reason)) {
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập lý do.']);
            exit;
        }
        // Confirm in JS, but here just process
        try {
            if ($action == 'disposal') {
                // Update status to 4
                $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET status = 4 WHERE id = ?';
                $sth = $db->prepare($sql);
                $sth->bindParam(1, $tool_id, PDO::PARAM_INT);
                $sth->execute();
            } elseif ($action == 'maintenance') {
                // Update status to 3
                $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tools SET status = 3 WHERE id = ?';
                $sth = $db->prepare($sql);
                $sth->bindParam(1, $tool_id, PDO::PARAM_INT);
                $sth->execute();
            }
        } catch (PDOException $e) {
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage()]);
            exit;
        }

        // Insert to maintenance table
        try {
            $sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_maintenance (tool_id, type, reason, created_date) VALUES (?, ?, ?, CURDATE())';
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $tool_id, PDO::PARAM_INT);
            $sth->bindParam(2, $action, PDO::PARAM_STR);
            $sth->bindParam(3, $reason, PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            // Ignore if table not exist
        }
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Thực hiện thành công!']);
        exit;
    }

    // If AJAX, return form HTML
    if ($nv_Request->isset_request('ajax', 'get')) {
        error_log('TOOL ' . strtoupper($action) . ' AJAX - Tool ID: ' . $tool_id);
        ob_clean();
        header('Content-Type: text/html');
        $title = $action == 'maintenance' ? 'Tạo phiếu bảo trì' : 'Tạo phiếu huỷ';
        $reason_label = $action == 'maintenance' ? 'Lý do bảo trì' : 'Lý do huỷ';
        $form_html = '
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Mã công cụ:</strong> ' . $array['tool_code'] . '</p>
                        <p><strong>Tên công cụ:</strong> ' . $array['name'] . '</p>
                        <p><strong>Danh mục:</strong> ' . $array['category_name'] . '</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Trạng thái:</strong> <span class="badge bg-' . $array['status_class'] . '">' . $array['status_text'] . '</span></p>
                        <p><strong>Ngày thêm:</strong> ' . $array['added_date'] . '</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Mô tả:</strong></p>
                        <div>' . $array['description'] . '</div>
                    </div>
                </div>
                <hr>
                <form id="slip-form" method="post" action="/nukeviet/admin/index.php?nv=tool-slip-management&op=tools">
                <input type="hidden" name="action" value="' . $action . '">
                <input type="hidden" name="tool_id" value="' . $tool_id . '">
                <input type="hidden" name="submit" value="submit">
                    <div class="form-group">
                        <label for="reason">' . $reason_label . ':</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-secondary" onclick="hideModalById(\'tsmActionModal\', null);">Hủy</button>
                        <button type="button" class="btn btn-success" onclick="submitSlipForm()">Lưu</button>
                    </div>
                </form>
            </div>
        </div>';
        echo $form_html;
        exit;
    }
/**
 * Handle tools list and management actions
 */
} else {
    /**
     * Quick status change handler
     */
    // Quick status change handler (via GET/POST: action=change_status&id=...&status=...)
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

    $sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools t WHERE 1=1 AND t.status != 4' . $where_sql;
    $sth = $db->prepare($sql);
    foreach ($params as $k => $v) {
        $sth->bindValue($k, $v);
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $sql = 'SELECT t.*, c.name as category_name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tools t LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_categories c ON t.category_id = c.id WHERE 1=1 AND t.status != 4' . $where_sql . ' ORDER BY t.id DESC LIMIT :limit OFFSET :offset';
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

    // Danh sách categories
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
    $xtpl->parse('add-form-inline');

    // Check if AJAX request for search/filter
    if ($nv_Request->isset_request('ajax', 'get') && $nv_Request->get_string('ajax', 'get') === 'search') {
        header('Content-Type: application/json');
        try {
            $list_html = $xtpl->text('main.list');
            echo json_encode(['success' => true, 'html' => $list_html]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // Parse form for modal in list view
    $sql = 'SELECT id, name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY name';
    $categories = $db->query($sql)->fetchAll();
    foreach ($categories as $category) {
        $xtpl->assign('CATEGORY', $category);
        $xtpl->assign('CATEGORY_SELECTED', '');
        $xtpl->parse('main.form.category');
    }

    
    foreach ($status_options as $key => $value) {
        $xtpl->assign('STATUS_KEY', $key);
        $xtpl->assign('STATUS_VALUE', $value);
        $xtpl->assign('STATUS_SELECTED', '');
        $xtpl->parse('main.form.status');
    }

    $xtpl->assign('DATA', array('tool_code' => '', 'name' => '', 'description' => '', 'category_id' => 0, 'status' => 1, 'added_date' => date('Y-m-d')));
    $xtpl->parse('main.form');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
