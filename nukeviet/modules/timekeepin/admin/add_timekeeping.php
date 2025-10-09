<?php

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN'))
    die('Stop!!!');

/**
 * nv_add_timekeeping()
 * 
 * @return string
 */
function nv_add_timekeeping()
{
    global $module_info, $lang_module, $db, $module_data, $lang_global, $nv_Request;
    
    $page_title = $lang_module['add_timekeeping'];
    $error = array();
    $data = array();
    
    // Xử lý form khi submit
    if ($nv_Request->get_title('submit', 'post') != '') {
        $data['employee_id'] = $nv_Request->get_int('employee_id', 'post', 0);
        $data['date'] = $nv_Request->get_title('date', 'post', '');
        $data['check_in'] = $nv_Request->get_title('check_in', 'post', '');
        $data['check_out'] = $nv_Request->get_title('check_out', 'post', '');
        $data['note'] = $nv_Request->get_title('note', 'post', '');
        
        // Validate dữ liệu
        if (empty($data['employee_id'])) {
            $error[] = 'Vui lòng chọn nhân viên';
        }
        
        if (empty($data['date'])) {
            $error[] = 'Vui lòng chọn ngày';
        }
        
        // Nếu không có lỗi, thực hiện lưu dữ liệu
        if (empty($error)) {
            // TODO: Thêm code lưu vào database ở đây
            $contents = '<div class="alert alert-success">Thêm chấm công thành công!</div>';
            $contents .= '<a href="' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_info['module_theme'] . '&amp;' . NV_OP_VARIABLE . '=main" class="btn btn-primary">Quay lại danh sách</a>';
            return $contents;
        }
    }
    
    // Hiển thị form
    $contents = '';
    
    // Hiển thị lỗi nếu có
    if (!empty($error)) {
        $contents .= '<div class="alert alert-danger">';
        $contents .= '<strong>Lỗi:</strong><br>';
        foreach ($error as $err) {
            $contents .= '- ' . $err . '<br>';
        }
        $contents .= '</div>';
    }
    
    $contents .= '
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">' . $lang_module['add_timekeeping'] . '</h3>
        </div>
        <div class="panel-body">
            <form action="' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_info['module_theme'] . '&amp;' . NV_OP_VARIABLE . '=add_timekeeping" method="post" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4 control-label">' . $lang_module['employee_name'] . ':</label>
                    <div class="col-sm-8">
                        <select name="employee_id" class="form-control" required>
                            <option value="">-- Chọn nhân viên --</option>
                            <option value="1"' . (isset($data['employee_id']) && $data['employee_id'] == 1 ? ' selected' : '') . '>Nhân viên 1</option>
                            <option value="2"' . (isset($data['employee_id']) && $data['employee_id'] == 2 ? ' selected' : '') . '>Nhân viên 2</option>
                            <option value="3"' . (isset($data['employee_id']) && $data['employee_id'] == 3 ? ' selected' : '') . '>Nhân viên 3</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-4 control-label">' . $lang_module['date'] . ':</label>
                    <div class="col-sm-8">
                        <input type="date" name="date" value="' . (isset($data['date']) ? $data['date'] : date('Y-m-d')) . '" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-4 control-label">' . $lang_module['check_in'] . ':</label>
                    <div class="col-sm-8">
                        <input type="time" name="check_in" value="' . (isset($data['check_in']) ? $data['check_in'] : '08:00') . '" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-4 control-label">' . $lang_module['check_out'] . ':</label>
                    <div class="col-sm-8">
                        <input type="time" name="check_out" value="' . (isset($data['check_out']) ? $data['check_out'] : '17:00') . '" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-4 control-label">' . $lang_module['note'] . ':</label>
                    <div class="col-sm-8">
                        <textarea name="note" class="form-control" rows="3">' . (isset($data['note']) ? $data['note'] : '') . '</textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-sm-8 col-sm-offset-4">
                        <button type="submit" name="submit" value="1" class="btn btn-primary">' . $lang_module['save'] . '</button>
                        <a href="' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_info['module_theme'] . '&amp;' . NV_OP_VARIABLE . '=main" class="btn btn-default">Quay lại</a>
                    </div>
                </div>
            </form>
        </div>
    </div>';
    
    return $contents;
}

$contents = nv_add_timekeeping();

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';