<?php

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);

/**
 * Khai báo các function được phép truy cập trong admin
 */
$allow_func = array(
    'main',         // Trang chính, dashboard
    'tools',        // Quản lý công cụ, dụng cụ
    'slips',        // Quản lý phiếu mượn/trả
    'maintenance', // Quản lý bảo trì, sửa chữa
    'reports',      // Xem báo cáo
    'ajax'          // Xử lý các yêu cầu ajax
);

/**
 * Định nghĩa các trạng thái của phiếu
 */
define('TOOL_SLIP_STATUS_BORROWING', 0); // Đang mượn
define('TOOL_SLIP_STATUS_RETURNED', 1);  // Đã trả
define('TOOL_SLIP_STATUS_LATE', 2);      // Trễ hẹn
define('TOOL_SLIP_STATUS_LOST', 3);      // Báo mất

/**
 * Định nghĩa các trạng thái của công cụ
 */
define('TOOL_STATUS_AVAILABLE', 1);      // Sẵn sàng cho mượn
define('TOOL_STATUS_BORROWED', 2);       // Đã được mượn
define('TOOL_STATUS_MAINTENANCE', 3);    // Đang bảo trì/sửa chữa
define('TOOL_STATUS_LOST', 4);           // Báo mất
define('TOOL_STATUS_DISPOSED', 0);       // Đã thanh lý

/**
 * Function kiểm tra quyền hạn của admin trong module
 *
 * @param int $user_id ID của người dùng cần kiểm tra.
 * @return bool True nếu có quyền, False nếu không có quyền.
 */
function nv_check_tool_slip_admin_permission($user_id = 0)
{
    global $admin_info;

    // Admin tối cao có tất cả các quyền
    if (defined('NV_IS_SPADMIN')) {
        return true;
    }

    // Kiểm tra quyền theo người dùng cụ thể nếu có
    if (!empty($user_id)) {
        // TODO: Thêm logic kiểm tra quyền hạn cho người dùng cụ thể.
        // Ví dụ: Dựa vào nhóm thành viên hoặc một bảng phân quyền riêng của module.
    }

    // Mặc định từ chối nếu không phải admin tối cao.
    // Cần phát triển logic phân quyền chi tiết hơn theo yêu cầu thực tế.
    return false;
}
