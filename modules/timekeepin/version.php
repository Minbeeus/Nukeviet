<?php

$module_version = [
    'name' => 'timekeeping',
    'modfuncs' => 'main,checkin',
    'submenu' => '',
    'is_sysmod' => 0,
    'virtual' => 0, // 0: module thật, 1: module ảo
    'version' => '1.0.00',
    'date' => 'Saturday, October 4, 2025 22:24:00 GMT+07:00',
    'author' => 'Minh, vcm243gc@gmail.com',
    'note' => '',
    'uploads_dir' => [
        $module_name,
        $module_name . '/' . date('Y_m')
    ]
];
