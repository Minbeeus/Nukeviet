<!-- BEGIN: main -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_NAME}/css/admin.css?v=1.8">
<div class="tsm-module reports-page">
    <div class="container-fluid">

<!-- BEGIN: not_installed -->
<div class="alert alert-warning">
    <h4>{LANG.module_not_installed}</h4>
    <p>{MESSAGE}</p>
</div>
<!-- END: not_installed -->

<!-- BEGIN: stats -->

<div class="row g-3">
    <div class="col-lg-6 col-md-6">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools" class="text-reset text-decoration-none">
        <div class="card stat-card border-left-orange h-100" style="min-height: 100px;">
        <div class="card-body d-flex align-items-center">
        <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
        <i class="fas fa-tools fa-2x tsm-icon-total"></i><div class="text-muted small text-uppercase" style="margin-left: 10px; font-weight: bold; font-size: 16px;">{LANG.total_tools}</div>
        </div>
        <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 26px; margin-top: 20px;">{TOTAL_TOOLS}</div>
        </div>
        </div>
        </a>
    </div>

    <div class="col-lg-6 col-md-6">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools&status=available" class="text-reset text-decoration-none">
        <div class="card stat-card border-left-success h-100" style="min-height: 100px;">
        <div class="card-body d-flex align-items-center">
        <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
        <i class="fas fa-check-circle fa-2x tsm-icon-available"></i><div class="text-muted small text-uppercase" style="margin-left: 10px; font-weight: bold; font-size: 16px;">{LANG.available_tools}</div>
        </div>
        <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 26px; margin-top: 20px;">{AVAILABLE_TOOLS}</div>
        </div>
        </div>
        </a>
    </div>

    <div class="col-lg-6 col-md-6">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing" class="text-reset text-decoration-none">
        <div class="card stat-card border-left-warning h-100" style="min-height: 100px;">
        <div class="card-body d-flex align-items-center">
        <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
        <i class="fas fa-hand-holding fa-2x tsm-icon-borrowed"></i><div class="text-muted small text-uppercase" style="margin-left: 10px; font-weight: bold; font-size: 16px;">{LANG.borrowed_tools}</div>
        </div>
        <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 26px; margin-top: 20px;">{BORROWED_TOOLS}</div>
        </div>
        </div>
        </a>
    </div>

    <div class="col-lg-6 col-md-6">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing&filter=overdue" class="text-reset text-decoration-none">
        <div class="card stat-card border-left-danger h-100" style="min-height: 100px;">
        <div class="card-body d-flex align-items-center">
        <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
        <i class="fas fa-exclamation-triangle fa-2x tsm-icon-overdue"></i><div class="text-muted small text-uppercase" style="margin-left: 10px; font-weight: bold; font-size: 16px;">{LANG.overdue_slips}</div>
        </div>
        <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 26px; margin-top: 20px;">{OVERDUE_SLIPS}</div>
        </div>
        </div>
        </a>
    </div>
</div>

<div class="row" style="margin-top: 40px;">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="font-size: 22px; font-weight: bold;">
                <strong>{LANG.most_borrowed_tools}</strong>
            </div>
            <div class="card-body">
                <!-- BEGIN: most_borrowed -->
                <div class="most-borrowed-item mb-3" style="margin-left: 10px; margin-right: 10px; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; display: flex; align-items: center; justify-content: space-between;">
                    <div class="text-truncate" style="font-size: 18px;">{TOOL_NAME}</div>
                    <span style="font-weight: bold; font-size: 16px;">{BORROW_COUNT}</span>
                </div>
                <!-- END: most_borrowed -->
                <!-- BEGIN: no_data -->
                <div class="text-center text-muted py-3">Chưa có dữ liệu</div>
                <!-- END: no_data -->
            </div>
        </div>
    </div>
</div>

<!-- END: stats -->
    </div>
</div>
<!-- END: main -->
<script src="{NV_BASE_SITEURL}modules/{MODULE_NAME}/js/admin.js"></script>
