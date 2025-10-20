<?php

if (!defined('NV_IS_MOD_TIMEKEEPING')) die('Stop!!!');

$page_title = $module_info['site_title'];
$page_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$result = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_timesheet ORDER BY id DESC");

$array_data = [];
while ($row = $result->fetch()) {
    $array_data[] = $row;
}

$contents = nv_list_post($array_data);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
