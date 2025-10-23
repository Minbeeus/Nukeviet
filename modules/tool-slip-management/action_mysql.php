<?php

if (!defined('NV_IS_FILE_MODULES'))
    die('Stop!!!');

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_maintainance_disposal_slips";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_borrowing_slip_details";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_borrowing_slips";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tools";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_students";

$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tools (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    added_date DATE NOT NULL,
    FOREIGN KEY (category_id) REFERENCES " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_code VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    class VARCHAR(100) NOT NULL,
    phone_number VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_borrowing_slips (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('borrowing', 'returned', 'overdue') NOT NULL DEFAULT 'borrowing',
    note TEXT,
    FOREIGN KEY (student_id) REFERENCES " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_students(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_borrowing_slip_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slip_id INT NOT NULL,
    tool_id INT NOT NULL,
    FOREIGN KEY (slip_id) REFERENCES " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_borrowing_slips(id),
    FOREIGN KEY (tool_id) REFERENCES " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tools(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_maintainance_disposal_slips (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tool_id INT NOT NULL,
    type ENUM('maintainance', 'disposal') NOT NULL,
    reason TEXT,
    create_date DATE NOT NULL,
    FOREIGN KEY (tool_id) REFERENCES " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tools(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
