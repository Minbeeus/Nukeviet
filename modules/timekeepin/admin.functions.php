<?php

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN'))
    die('Stop!!!');

define('NV_IS_FILE_ADMIN', true);

/**
 * Khai báo các function được phép truy cập trong admin
 */
$allow_func = array(
    'main',          // Quản lý danh sách chấm công
    'add_timekeeping',       // Thêm/Sửa chấm công
    'employee',      // Quản lý nhân viên
    'config',        // Cấu hình module
    'ajax'          // Xử lý ajax
);

/**
 * Định nghĩa các trạng thái chấm công
 */
define('TIMESHEET_STATUS_NORMAL', 1);
define('TIMESHEET_STATUS_LATE', 0);

/**
 * Function kiểm tra quyền thực thi
 */
function nv_check_timesheet_admin_permission($employee_id = 0)
{
    global $admin_info;
    
    // Admin tối cao có tất cả quyền
    if (defined('NV_IS_SPADMIN')) {
        return true;
    }

    // Kiểm tra quyền theo nhân viên nếu có
    if (!empty($employee_id)) {
        // TODO: Thêm logic kiểm tra quyền theo nhân viên
    }

    return false;
}