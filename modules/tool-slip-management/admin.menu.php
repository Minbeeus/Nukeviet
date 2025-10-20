<?php

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

global $lang_module, $global_config, $module_file;

// Define the menu with default English values first.
$submenu = [
    'main'        => 'Dashboard',
    'tools'       => 'Tool Management',
    'slips'       => 'Slip Management',
    'maintenance' => 'Maintenance',
    'reports'     => 'Reports',
];

// Try to load the language file and overwrite the defaults.
if (empty($lang_module)) {
    $current_lang = !empty($global_config['admin_language']) ? $global_config['admin_language'] : 'vi';
    $lang_path = str_replace(['\\', '//'], '/', NV_ROOTDIR . '/language/' . $current_lang . '/' . $module_file . '.php');

    if (file_exists($lang_path)) {
        require_once $lang_path;
    } else {
        $default_lang_path = str_replace(['\\', '//'], '/', NV_ROOTDIR . '/language/en/' . $module_file . '.php');
        if (file_exists($default_lang_path)) {
            require_once $default_lang_path;
        }
    }
}

// If the language module was loaded successfully, overwrite the defaults.
if (!empty($lang_module)) {
    $submenu['main']        = isset($lang_module['main']) ? $lang_module['main'] : $submenu['main'];
    $submenu['tools']       = isset($lang_module['tools_manage']) ? $lang_module['tools_manage'] : $submenu['tools'];
    $submenu['slips']       = isset($lang_module['slips_manage']) ? $lang_module['slips_manage'] : $submenu['slips'];
    $submenu['maintenance'] = isset($lang_module['maintenance_manage']) ? $lang_module['maintenance_manage'] : $submenu['maintenance'];
    $submenu['reports']     = isset($lang_module['reports']) ? $lang_module['reports'] : $submenu['reports'];
}

$allow_func = ['main', 'tools', 'slips', 'maintenance', 'reports'];
