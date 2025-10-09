<?php

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

$allow_func = array('main', 'add_timekeeping');

$submenu = array();
$submenu['main'] = "Danh sách chấm công";
$submenu['add_timekeeping'] = "Thêm chấm công";