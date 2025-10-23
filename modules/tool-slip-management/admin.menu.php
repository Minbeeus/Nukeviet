<?php

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

$submenu['main'] = 'Dashboard';
$submenu['tools'] = 'Quản lý công cụ';
$submenu['borrowing'] = 'Quản lý mượn/trả';
$submenu['reports'] = 'Báo cáo & Thống kê';

$allow_func = ['main', 'tools', 'borrowing', 'reports'];
