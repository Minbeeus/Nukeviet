<?php

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$lang_translator['author'] = 'Văn Công Minh (vcm243gc@gmail.com)';
$lang_translator['createdate'] = '14/10/2025, 02:43:42';
$lang_translator['copyright'] = '@Copyright (C) 2025 Văn Công Minh. All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

// -- General --
$lang_module['save'] = 'Lưu lại';
$lang_module['add_new'] = 'Thêm mới';
$lang_module['edit'] = 'Sửa';
$lang_module['delete'] = 'Xóa';
$lang_module['status'] = 'Trạng thái';
$lang_module['actions'] = 'Chức năng';
$lang_module['description'] = 'Mô tả';
$lang_module['search'] = 'Tìm kiếm';
$lang_module['confirm_delete'] = 'Bạn có chắc chắn muốn xóa mục này?';
$lang_module['back_to_list'] = 'Quay lại danh sách';
$lang_module['no_data'] = 'Không có dữ liệu để hiển thị';
$lang_module['stt'] = 'STT';

// -- Menu --
$lang_module['main'] = 'Bảng điều khiển';
$lang_module['tools_manage'] = 'Quản lý Dụng cụ';
$lang_module['slips_manage'] = 'Quản lý Mượn/Trả';
$lang_module['maintenance_manage'] = 'Quản lý Bảo trì/Hủy';
$lang_module['reports'] = 'Báo cáo & Thống kê';

// -- Tool Management --
$lang_module['tool_name'] = 'Tên dụng cụ';
$lang_module['tool_code'] = 'Mã dụng cụ';
$lang_module['category'] = 'Danh mục';
$lang_module['search_by_name_code'] = 'Tìm theo tên hoặc mã';
$lang_module['all_categories'] = 'Tất cả danh mục';
$lang_module['add_new_tool'] = 'Thêm dụng cụ mới';
$lang_module['edit_tool'] = 'Chỉnh sửa dụng cụ';
$lang_module['select_category'] = '--- Chọn danh mục ---';

// -- Slips Management --
$lang_module['create_new_slip'] = 'Tạo phiếu mượn mới';
$lang_module['slip_detail'] = 'Chi tiết phiếu mượn';
$lang_module['borrower'] = 'Người mượn';
$lang_module['borrow_date'] = 'Ngày mượn';
$lang_module['due_date'] = 'Ngày hẹn trả';
$lang_module['return_date'] = 'Ngày trả thực tế';
$lang_module['select_borrower'] = '--- Chọn người mượn ---';
$lang_module['select_tools'] = 'Chọn các dụng cụ cần mượn';
$lang_module['notes'] = 'Ghi chú';
$lang_module['slip_id'] = 'Mã phiếu';
$lang_module['confirm_return'] = 'Xác nhận đã nhận lại đủ đồ';

// -- Maintenance Management --
$lang_module['add_new_maintenance'] = 'Tạo phiếu Bảo trì/Hủy';
$lang_module['select_tool'] = '--- Chọn dụng cụ ---';
$lang_module['slip_type'] = 'Loại phiếu';
$lang_module['maintenance'] = 'Bảo trì';
$lang_module['disposal'] = 'Hủy';
$lang_module['reason'] = 'Lý do';
$lang_module['created_date'] = 'Ngày tạo';
$lang_module['tool'] = 'Dụng cụ';

// -- Reports --
$lang_module['from_date'] = 'Từ ngày';
$lang_module['to_date'] = 'Đến ngày';
$lang_module['view_report'] = 'Xem báo cáo';
$lang_module['borrowing_history'] = 'Lịch sử mượn trả';

// -- Statuses --
$lang_module['slip_status_borrowing'] = 'Đang mượn';
$lang_module['slip_status_returned'] = 'Đã trả';
$lang_module['slip_status_late'] = 'Trễ hẹn';
$lang_module['slip_status_lost'] = 'Báo mất';

$lang_module['tool_status_available'] = 'Sẵn sàng';
$lang_module['tool_status_borrowed'] = 'Đã mượn';
$lang_module['tool_status_maintenance'] = 'Bảo trì';
$lang_module['tool_status_lost'] = 'Báo mất';
$lang_module['tool_status_disposed'] = 'Thanh lý';

// Dashboard labels
$lang_module['total_tools'] = 'Tổng số dụng cụ';
$lang_module['available_tools'] = 'Sẵn có';
$lang_module['borrowed_tools'] = 'Đang mượn';
$lang_module['overdue_slips'] = 'Phiếu quá hạn';
