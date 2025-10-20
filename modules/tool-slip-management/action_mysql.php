<?php

if (!defined('NV_IS_FILE_MODULES'))
    die('Stop!!!');

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_maintenance";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_slip_details";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_slips";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tools";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_students";

$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    description text,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tools (
    id int(11) NOT NULL AUTO_INCREMENT,
    category_id int(11) NOT NULL,
    name varchar(255) NOT NULL,
    code varchar(100) DEFAULT NULL,
    description text,
    status tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_slips (
    id int(11) NOT NULL AUTO_INCREMENT,
    student_id int(11) NOT NULL,
    admin_id int(11) NOT NULL,
    borrow_date int(11) unsigned NOT NULL,
    due_date int(11) unsigned NOT NULL,
    return_date int(11) unsigned DEFAULT NULL,
    notes text,
    status tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_slip_details (
    id int(11) NOT NULL AUTO_INCREMENT,
    slip_id int(11) NOT NULL,
    tool_id int(11) NOT NULL,
    quantity int(5) NOT NULL DEFAULT '1',
    note varchar(255) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_maintenance (
    id int(11) NOT NULL AUTO_INCREMENT,
    tool_id int(11) NOT NULL,
    type varchar(50) NOT NULL COMMENT 'maintenance or disposal',
    reason text,
    created_date int(11) unsigned NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_students (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    student_code varchar(50) NOT NULL COMMENT 'Mã học sinh, sinh viên',
    full_name varchar(255) NOT NULL COMMENT 'Họ tên đầy đủ',
    class varchar(100) NOT NULL COMMENT 'Lớp',
    phone_number varchar(10) NOT NULL COMMENT 'Số điện thoại',
    status tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: Hoạt động, 0: Khóa',
    PRIMARY KEY (id),
    UNIQUE KEY student_code (student_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh sách người mượn dụng cụ';";
