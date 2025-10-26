<?php

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Load language
$langfile = NV_ROOTDIR . '/modules/' . $module_file . '/language/vi.php';
include $langfile;

$page_title = $lang_module['dashboard'];

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
// Assign common NV variables used by templates
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

// CSS loaded in template

// Khởi tạo biến
$most_borrowed = array();

// Lấy thống kê với xử lý lỗi
try {
    $db->sqlreset()
        ->select('COUNT(*) as total_tools')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_tools');
    $total_tools = (int)$db->query($db->sql())->fetchColumn();
} catch (PDOException $e) {
    $total_tools = 0;
}

try {
    $db->sqlreset()
        ->select('COUNT(*) as available_tools')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_tools')
        ->where('status = 1');
    $available_tools = (int)$db->query($db->sql())->fetchColumn();
} catch (PDOException $e) {
    $available_tools = 0;
}

try {
    $db->sqlreset()
        ->select('COUNT(*) as borrowed_tools')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_tools')
        ->where('status = 2');
    $borrowed_tools = (int)$db->query($db->sql())->fetchColumn();
} catch (PDOException $e) {
    $borrowed_tools = 0;
}

// Update overdue slips first
$db->query("UPDATE " . NV_PREFIXLANG . '_' . $module_data . "_slips SET status = 2 WHERE status = 0 AND due_date < " . time());

try {
$db->sqlreset()
    ->select('COUNT(*) as overdue_slips')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_slips')
        ->where('status = 2');
$overdue_slips = (int)$db->query($db->sql())->fetchColumn();
} catch (PDOException $e) {
     $overdue_slips = 0;
 }

// Count tools currently under maintenance
try {
    $db->sqlreset()
        ->select('COUNT(*) as maintenance_tools')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_tools')
        ->where('status = 3');
    $maintenance_tools = (int)$db->query($db->sql())->fetchColumn();
} catch (PDOException $e) {
    $maintenance_tools = 0;
}

// Thống kê công cụ được mượn nhiều nhất
try {
$db->sqlreset()
->select('t.name, COUNT(bd.id) as borrow_count')
->from(NV_PREFIXLANG . '_' . $module_data . '_slip_details bd')
->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tools t ON bd.tool_id = t.id')
->group('bd.tool_id')
    ->order('borrow_count DESC')
    ->limit(10);
$most_borrowed = $db->query($db->sql())->fetchAll();
} catch (PDOException $e) {
$most_borrowed = array();
}

// Kiểm tra nếu module chưa cài đặt (tất cả thống kê = 0)
if ($total_tools == 0 && $available_tools == 0 && $borrowed_tools == 0 && $maintenance_tools == 0 && empty($most_borrowed)) {
    $xtpl->assign('MESSAGE', $lang_module['module_not_installed_message']);
    $xtpl->parse('main.not_installed');
} else {
    // Gán biến
    $xtpl->assign('TOTAL_TOOLS', $total_tools);
    $xtpl->assign('AVAILABLE_TOOLS', $available_tools);
    $xtpl->assign('BORROWED_TOOLS', $borrowed_tools);
    $xtpl->assign('MAINTENANCE_TOOLS', $maintenance_tools);
    // Assign overdue slips count for dashboard
    $xtpl->assign('OVERDUE_SLIPS', $overdue_slips);
    if (empty($most_borrowed)) {
        // No data block inside stats
        $xtpl->parse('main.stats.no_data');
    } else {
        foreach ($most_borrowed as $row) {
            $xtpl->assign('TOOL_NAME', $row['name']);
            $xtpl->assign('BORROW_COUNT', $row['borrow_count']);
            // parse into stats block
            $xtpl->parse('main.stats.most_borrowed');
        }
    }

    $xtpl->parse('main.stats');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
