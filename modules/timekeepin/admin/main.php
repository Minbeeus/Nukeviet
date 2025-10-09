<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    exit("Stop");
}

$page_title = $lang_module['list_timekeeping'];

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

// Get list of timekeeping records
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_timesheet ORDER BY date DESC, check_in DESC";
$result = $db->query($sql);

while ($row = $result->fetch()) {
    $row['date'] = date('d/m/Y', $row['date']);
    $row['check_in'] = date('H:i', $row['check_in']);
    $row['check_out'] = $row['check_out'] ? date('H:i', $row['check_out']) : '--:--';
    $xtpl->assign('ROW', $row);
    $xtpl->parse('main.loop');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';