<?php

if (!defined('NV_IS_FILE_MODULES')) {
    die('Stop!!!');
}

/**
 * Class xử lý cài đặt / gỡ bỏ module Tool Slip Management
 */
class nv_tool_slip_management_action_mysql extends nv_db_action_module
{
    /**
     * Hàm cài đặt module (tạo bảng)
     *
     * @return string Chuỗi SQL để tạo các bảng
     */
    public function sql_install()
    {
        $sql = [];

        // Bảng categories
        $sql[] = "CREATE TABLE " . $this->db_prefix . "_" . $this->module_data . "_categories (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // Bảng tools
        $sql[] = "CREATE TABLE " . $this->db_prefix . "_" . $this->module_data . "_tools (
            id INT(11) NOT NULL AUTO_INCREMENT,
            category_id INT(11) NOT NULL,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(100) DEFAULT NULL,
            description TEXT,
            status TINYINT(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (id),
            KEY category_id (category_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // Bảng slips
        $sql[] = "CREATE TABLE " . $this->db_prefix . "_" . $this->module_data . "_slips (
            id INT(11) NOT NULL AUTO_INCREMENT,
            borrower_info VARCHAR(255) NOT NULL COMMENT 'Thông tin người mượn',
            borrow_date INT(11) UNSIGNED NOT NULL,
            due_date INT(11) UNSIGNED NOT NULL,
            return_date INT(11) UNSIGNED DEFAULT NULL,
            notes TEXT,
            status TINYINT(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // Bảng slip_details
        $sql[] = "CREATE TABLE " . $this->db_prefix . "_" . $this->module_data . "_slip_details (
            id INT(11) NOT NULL AUTO_INCREMENT,
            slip_id INT(11) NOT NULL,
            tool_id INT(11) NOT NULL,
            quantity INT(5) NOT NULL DEFAULT '1',
            note VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY slip_id (slip_id),
            KEY tool_id (tool_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // Bảng maintenance
        $sql[] = "CREATE TABLE " . $this->db_prefix . "_" . $this->module_data . "_maintenance (
            id INT(11) NOT NULL AUTO_INCREMENT,
            tool_id INT(11) NOT NULL,
            type VARCHAR(50) NOT NULL COMMENT 'maintenance or disposal',
            reason TEXT,
            created_date INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (id),
            KEY tool_id (tool_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // Bảng timesheet (bổ sung mới)
        $sql[] = "CREATE TABLE " . $this->db_prefix . "_" . $this->module_data . "_timesheet (
            id INT(11) NOT NULL AUTO_INCREMENT,
            employee_id INT(11) NOT NULL,
            check_in INT(11) DEFAULT NULL,
            check_out INT(11) DEFAULT NULL,
            date DATE NOT NULL,
            status TINYINT(1) DEFAULT 1 COMMENT '1: Normal, 0: Late',
            note TEXT DEFAULT NULL,
            created_at INT(11) NOT NULL,
            updated_at INT(11) NOT NULL,
            PRIMARY KEY (id),
            KEY employee_id (employee_id),
            KEY date (date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return implode("\n", $sql);
    }

    /**
     * Hàm gỡ bỏ module (xóa toàn bộ bảng)
     *
     * @return string Chuỗi SQL để xóa bảng
     */
    public function sql_uninstall()
    {
        $sql = [];
        $prefix = $this->db_prefix . "_" . $this->module_data;

        $sql[] = "DROP TABLE IF EXISTS {$prefix}_timesheet;";
        $sql[] = "DROP TABLE IF EXISTS {$prefix}_maintenance;";
        $sql[] = "DROP TABLE IF EXISTS {$prefix}_slip_details;";
        $sql[] = "DROP TABLE IF EXISTS {$prefix}_slips;";
        $sql[] = "DROP TABLE IF EXISTS {$prefix}_tools;";
        $sql[] = "DROP TABLE IF EXISTS {$prefix}_categories;";

        return implode("\n", $sql);
    }
}