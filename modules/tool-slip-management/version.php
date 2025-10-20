<?php
if (!defined('NV_MAINFILE')) die('Stop!!!');
$module_name = 'tool_slip_management';

$module_version = array(
    'name' => 'Tool Slip Management',
    'modfuncs' => 'main,tools,slips,maintenance,reports',
    'submenu' => 'main,tools,slips,maintenance,reports',
    'is_sysmod' => 0,
    'virtual' => 0,
    'version' => '1.0.00',
    'date' => 'Tue, 14 Oct 2025 09:00:00 GMT',
    'author' => 'Văn Công Minh (vcm243gc@gmail.com)',
    'note' => 'Module to manage borrowing and returning tools.',
    'uploads_dir' => array(
        $module_name,
        $module_name . '/' . date('Y_m')
    )
);