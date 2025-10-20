<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Gọi file model tương ứng
require_once NV_ROOTDIR . '/modules/' . $module_file . '/models/reports.php';

$page_title = isset($lang_module['reports']) ? $lang_module['reports'] : 'Reports & Statistics';

// Lấy giá trị ngày tháng từ URL, nếu không có thì đặt giá trị mặc định (từ đầu tháng đến ngày hiện tại)
$from_date_str = $nv_Request->get_string('from_date', 'get', date('01/m/Y'));
$to_date_str = $nv_Request->get_string('to_date', 'get', date('d/m/Y'));

// Chuyển đổi ngày tháng sang dạng timestamp để truy vấn CSDL
$from_date_timestamp = 0;
if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $from_date_str, $m)) {
    $from_date_timestamp = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
}

$to_date_timestamp = 0;
if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $to_date_str, $m)) {
    $to_date_timestamp = mktime(23, 59, 59, $m[2], $m[1], $m[3]);
}

// Khởi tạo giao diện
$xtpl = new XTemplate('reports.tpl', str_replace(array('\\', '//'), '/', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file));
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
$xtpl->assign('FROM_DATE', $from_date_str);
$xtpl->assign('TO_DATE', $to_date_str);

// Nếu có ngày tháng hợp lệ, thực hiện lấy báo cáo
if ($from_date_timestamp > 0 && $to_date_timestamp > 0) {
    // Lấy dữ liệu báo cáo từ model
    $report_data = get_borrowing_history_report($from_date_timestamp, $to_date_timestamp);
    
    $status_map = [
        1 => ['text' => 'Đang mượn', 'class' => 'warning'],
        2 => ['text' => 'Đã trả', 'class' => 'success'],
        3 => ['text' => 'Quá hạn', 'class' => 'danger']
    ];

    if (empty($report_data)) {
        $xtpl->parse('main.no_result');
    } else {
        $stt = 0;
        foreach ($report_data as $row) {
            $stt++;
            $row['stt'] = $stt;
            $row['borrow_date_formatted'] = nv_date('d/m/Y H:i', $row['borrow_date']);
            $row['due_date_formatted'] = nv_date('d/m/Y', $row['due_date']);
            $row['return_date_formatted'] = ($row['return_date'] > 0) ? nv_date('d/m/Y H:i', $row['return_date']) : '---';
            $row['status_text'] = $status_map[$row['status']]['text'];
            $row['status_class'] = $status_map[$row['status']]['class'];
            $xtpl->assign('ROW', $row);
            $xtpl->parse('main.result.loop');
        }
        $xtpl->parse('main.result');
    }
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';