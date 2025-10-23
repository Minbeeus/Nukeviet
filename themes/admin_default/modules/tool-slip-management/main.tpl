<!-- BEGIN: main -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_NAME}/css/main.css">
<div class="tsm-module container-fluid px-3">

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
        <div class="card stat-card border-left-orange h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-left d-flex align-items-center">
                    <div class="me-3 text-start">
                        <div class="text-muted small text-uppercase">{LANG.total_tools}</div>
                        <div class="d-block"><i class="fas fa-tools fa-2x tsm-icon-total"></i></div>
                    </div>
                </div>
                <div class="stat-number ms-auto d-flex align-items-center justify-content-end">{TOTAL_TOOLS}</div>
            </div>
        </div>
        </a>
    </div>

    <div class="col-lg-6 col-md-6">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools&status=available" class="text-reset text-decoration-none">
        <div class="card stat-card border-left-success h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-left d-flex align-items-center">
                    <div class="me-3 text-start">
                        <div class="text-muted small text-uppercase">{LANG.available_tools}</div>
                        <div class="d-block"><i class="fas fa-check-circle fa-2x tsm-icon-available"></i></div>
                    </div>
                </div>
                <div class="stat-number ms-auto d-flex align-items-center justify-content-end">{AVAILABLE_TOOLS}</div>
            </div>
        </div>
        </a>
    </div>

    <div class="col-lg-6 col-md-6">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing" class="text-reset text-decoration-none">
        <div class="card stat-card border-left-warning h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-left d-flex align-items-center">
                    <div class="me-3 text-start">
                        <div class="text-muted small text-uppercase">{LANG.borrowed_tools}</div>
                        <div class="d-block"><i class="fas fa-hand-holding fa-2x tsm-icon-borrowed"></i></div>
                    </div>
                </div>
                <div class="stat-number ms-auto d-flex align-items-center justify-content-end">{BORROWED_TOOLS}</div>
            </div>
        </div>
        </a>
    </div>

    <div class="col-lg-6 col-md-6">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing&filter=overdue" class="text-reset text-decoration-none">
        <div class="card stat-card border-left-danger h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stat-left d-flex align-items-center">
                    <div class="me-3 text-start">
                        <div class="text-muted small text-uppercase">{LANG.overdue_slips}</div>
                        <div class="d-block"><i class="fas fa-exclamation-triangle fa-2x tsm-icon-overdue"></i></div>
                    </div>
                </div>
                <div class="stat-number ms-auto d-flex align-items-center justify-content-end">{OVERDUE_SLIPS}</div>
            </div>
        </div>
        </a>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <strong>{LANG.most_borrowed_tools}</strong>
            </div>
            <div class="card-body">
                <!-- BEGIN: most_borrowed -->
                <div class="most-borrowed-item d-flex align-items-center justify-content-between">
                    <div class="text-truncate"><strong>{TOOL_NAME}</strong></div>
                    <span class="badge badge-primary">{BORROW_COUNT}</span>
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
<!-- END: main -->
<script src="{NV_BASE_SITEURL}modules/{MODULE_NAME}/js/admin.js"></script>
