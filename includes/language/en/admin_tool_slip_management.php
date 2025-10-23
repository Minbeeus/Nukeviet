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
$lang_module['save'] = 'Save';
$lang_module['add_new'] = 'Add New';
$lang_module['edit'] = 'Edit';
$lang_module['delete'] = 'Delete';
$lang_module['status'] = 'Status';
$lang_module['actions'] = 'Actions';
$lang_module['description'] = 'Description';
$lang_module['search'] = 'Search';
$lang_module['confirm_delete'] = 'Are you sure you want to delete this item?';
$lang_module['back_to_list'] = 'Back to list';
$lang_module['no_data'] = 'No data to display';
$lang_module['stt'] = 'No.';

// -- Menu --
$lang_module['main'] = 'Dashboard';
$lang_module['tools_manage'] = 'Tool Management';
$lang_module['slips_manage'] = 'Slip Management';
$lang_module['maintenance_manage'] = 'Maintenance/Disposal Management';
$lang_module['reports'] = 'Reports & Statistics';

// -- Tool Management --
$lang_module['tool_name'] = 'Tool Name';
$lang_module['tool_code'] = 'Tool Code';
$lang_module['category'] = 'Category';
$lang_module['search_by_name_code'] = 'Search by name or code';
$lang_module['all_categories'] = 'All Categories';
$lang_module['add_new_tool'] = 'Add New Tool';
$lang_module['edit_tool'] = 'Edit Tool';
$lang_module['select_category'] = '--- Select Category ---';

// -- Slips Management --
$lang_module['create_new_slip'] = 'Create New Borrowing Slip';
$lang_module['slip_detail'] = 'Slip Detail';
$lang_module['borrower'] = 'Borrower';
$lang_module['borrow_date'] = 'Borrow Date';
$lang_module['due_date'] = 'Due Date';
$lang_module['return_date'] = 'Return Date';
$lang_module['select_borrower'] = '--- Select Borrower ---';
$lang_module['select_tools'] = 'Select Tools to Borrow';
$lang_module['notes'] = 'Notes';
$lang_module['slip_id'] = 'Slip ID';
$lang_module['confirm_return'] = 'Confirm Receipt of All Items';

// -- Maintenance Management --
$lang_module['add_new_maintenance'] = 'Create New Maintenance/Disposal Slip';
$lang_module['select_tool'] = '--- Select Tool ---';
$lang_module['slip_type'] = 'Slip Type';
$lang_module['maintenance'] = 'Maintenance';
$lang_module['disposal'] = 'Disposal';
$lang_module['reason'] = 'Reason';
$lang_module['created_date'] = 'Created Date';
$lang_module['tool'] = 'Tool';

// -- Reports --
$lang_module['from_date'] = 'From Date';
$lang_module['to_date'] = 'To Date';
$lang_module['view_report'] = 'View Report';
$lang_module['borrowing_history'] = 'Borrowing History';

// -- Statuses --
$lang_module['slip_status_borrowing'] = 'Borrowing';
$lang_module['slip_status_returned'] = 'Returned';
$lang_module['slip_status_late'] = 'Late';
$lang_module['slip_status_lost'] = 'Lost';

$lang_module['tool_status_available'] = 'Available';
$lang_module['tool_status_borrowed'] = 'Borrowed';
$lang_module['tool_status_maintenance'] = 'Maintenance';
$lang_module['tool_status_lost'] = 'Lost';
$lang_module['tool_status_disposed'] = 'Disposed';

// Dashboard labels
$lang_module['total_tools'] = 'Total Tools';
$lang_module['available_tools'] = 'Available';
$lang_module['borrowed_tools'] = 'Borrowed';
$lang_module['overdue_slips'] = 'Overdue Slips';
