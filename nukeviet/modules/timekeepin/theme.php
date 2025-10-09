<?php

if (!defined('NV_IS_MOD_TIMEKEEPING')) {
    die('Stop!!!');
}

function nv_list_post($array_data)
{
    global $lang_module, $module_info;
    $xtpl = new XTemplate("main.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
    $xtpl->assign('TITLE', "Demo module");

    if (!empty($array_data)) {
        foreach ($array_data as $item) {
            $xtpl->assign('ROW', $item);
            $xtpl->parse('main.loop');
        }
    } else {
        $xtpl->parse('main.nodata');
    }
    $xtpl->parse('main');
    return $xtpl->text('main');
}
