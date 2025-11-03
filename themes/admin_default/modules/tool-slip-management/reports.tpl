<!-- BEGIN: main -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_NAME}/css/admin.css?v=1.8">
<div class="tsm-module reports-page" data-date="<?php echo date('d/m/Y'); ?>">
<!-- BEGIN: not_installed -->
<div class="alert alert-warning fade show" role="alert">
<div class="d-flex align-items-center">
<i class="fas fa-exclamation-triangle text-warning mr-2"></i>
<div>
<h5 class="alert-heading mb-1">{LANG.module_not_installed}</h5>
<p class="mb-0">{MESSAGE}</p>
</div>
</div>
</div>
<!-- END: not_installed -->

<div class="container-fluid">
    

<div class="card shadow-sm mb-3" style="padding: 10px; padding-bottom: 20px">
    <form id="report-filter" method="get" action="{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=reports" style="display: flex; justify-content: space-between; align-items: baseline; gap: 15px;">
    <div style="display: flex; align-items: baseline; gap: 20px; flex-wrap: wrap;">
    <div style="display: flex; align-items: baseline; gap: 8px;">
        <label for="start_date" style="font-weight: bold; margin: 0; white-space: nowrap;">Ngày bắt đầu</label>
        <input type="date" class="form-control" id="start_date" name="start_date" value="{START_DATE}" style="width: 160px;">
            </div>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <label for="end_date" style="font-weight: bold; margin: 0; white-space: nowrap;">Ngày kết thúc</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{END_DATE}" style="width: 160px;">
               </div>
        </div>
<button type="button" class="btn btn-primary" id="filter-btn" style="white-space: nowrap; font-size: 16px; font-weight: bold;">Lọc</button>
</form>
</div>

    <!-- Statistics Cards -->
    <div class="row mb-3">
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card stat-card border-left-orange h-100" style="min-height: 100px;">
        <div class="card-body d-flex align-items-center">
        <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
    <i class="fas fa-book fa-2x tsm-icon-total"></i><div class="text-muted small text-uppercase" style="margin-left: 15px; font-weight: bold; font-size: 18px;">Tổng phiếu mượn</div>
    </div>
    <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 24px; margin-top: 20px;">{TOTAL_BORROWINGS}</div>
    </div>
    </div>
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
    <div class="card stat-card border-left-success h-100" style="min-height: 100px;">
    <div class="card-body d-flex align-items-center">
            <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
                    <i class="fas fa-check-circle fa-2x tsm-icon-available"></i><div class="text-muted small text-uppercase" style="margin-left: 15px; font-weight: bold; font-size: 18px;">Đã trả</div>
                </div>
            <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 24px; margin-top: 20px;">{RETURNED_COUNT}</div>
    </div>
    </div>
        </div>
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card stat-card border-left-warning h-100" style="min-height: 100px;">
                <div class="card-body d-flex align-items-center">
        <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
        <i class="fas fa-clock fa-2x tsm-icon-borrowed"></i><div class="text-muted small text-uppercase" style="margin-left: 15px; font-weight: bold; font-size: 18px;">Đang mượn</div>
        </div>
    <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 24px; margin-top: 20px;">{ACTIVE_COUNT}</div>
    </div>
    </div>
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
    <div class="card stat-card border-left-danger h-100" style="min-height: 100px;">
    <div class="card-body d-flex align-items-center">
        <div class="me-3 text-start" style="display: inline-flex; align-items: center;">
            <i class="fas fa-exclamation-triangle fa-2x tsm-icon-overdue"></i><div class="text-muted small text-uppercase" style="margin-left: 15px; font-weight: bold; font-size: 18px;">Quá hạn</div>
        </div>
        <div class="stat-number d-flex align-items-center justify-content-end ms-auto" style="font-weight: bold; font-size: 24px; margin-top: 20px;">{OVERDUE_COUNT}</div>
    </div>
    </div>
    </div>
    </div>

<!-- Main Content -->
<div class="row">
<!-- Borrowing History -->
<div class="col-lg-8 mb-1">
            <div class="card shadow">
                <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 18px;">
                <i class="fas fa-history mr-2"></i>{LANG.borrowing_history}
                </h6>
    </div>
<div class="card-body" style="padding-top: 10px;">
<div class="table-responsive">
<table class="table table-bordered table-hover" id="borrowing-history-table">
<thead class="table-light">
<tr>
<th style="font-size: 16px; font-weight: bold;"><i class="fas fa-hashtag mr-1"></i>{LANG.slip_id}</th>
<th style="font-size: 16px; font-weight: bold;"><i class="fas fa-user mr-1"></i>{LANG.student}</th>
<th style="font-size: 16px; font-weight: bold;"><i class="fas fa-calendar-alt mr-1"></i>{LANG.borrow_date}</th>
<th style="font-size: 16px; font-weight: bold;"><i class="fas fa-clock mr-1"></i>{LANG.due_date}</th>
<th style="font-size: 16px; font-weight: bold;"><i class="fas fa-undo mr-1"></i>{LANG.return_date}</th>
<th style="font-size: 16px; font-weight: bold;"><i class="fas fa-info-circle mr-1"></i>{LANG.status}</th>
</tr>
</thead>
<tbody>
<!-- BEGIN: no_borrowing_data -->
<tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                                        <p>Không có dữ liệu mượn/trả trong khoảng thời gian đã chọn.</p>
                                        <small>Hãy thử chọn khoảng thời gian khác hoặc kiểm tra lại dữ liệu.</small>
                                    </div>
                                </td>
                            </tr>
                            <!-- END: no_borrowing_data -->
                            <!-- BEGIN: borrowing_history -->
                            <tr>
<td><strong>#{BORROWING.id}</strong></td>
<td>
<div class="d-flex align-items-center">
<div>
<div class="fw-bold">{BORROWING.full_name}</div>
<small class="text-muted">{BORROWING.student_code}</small>
</div>
</div>
</div>
</td>
<td>{BORROWING.borrow_date}</td>
<td class="{BORROWING.status == 2 ? 'text-danger fw-bold' : ''}">{BORROWING.due_date}</td>
<td>{BORROWING.return_date}</td>
<td>
<span class="badge bg-{BORROWING.status_class}">{BORROWING.status_text}</span>
</td>
</tr>
<!-- END: borrowing_history -->
</tbody>
</table>
</div>
</div>
</div>
</div>

<!-- Sidebar -->
<div class="col-lg-4">
<!-- Category Statistics -->
<div class="card shadow mb-1">
<div class="card-header py-2">
<h6 class="m-0 font-weight-bold text-success" style="font-size: 18px;">
<i class="fas fa-chart-pie mr-2"></i>Thống kê theo danh mục
</h6>
</div>
                <div class="card-body" style="padding-top: 10px;">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-folder mr-1"></i>Danh mục</th>
                                    <th class="text-center" style="font-size: 16px; font-weight: bold;"><i class="fas fa-hashtag mr-1"></i>Số lần</th>
                                </tr>
                            </thead>
                            <tbody>
                            <!-- BEGIN: category_stats -->
                            <tr>
                                <td>{STAT.category_name}</td>
                                <td class="text-center" style="font-weight: bold; font-size: 16px;">
                                {STAT.borrow_count}
                                </td>
                            </tr>
                            <!-- END: category_stats -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Overdue Slips -->
            <div class="card shadow mb-1">
            <div class="card-header py-2">
            <h6 class="m-0 font-weight-bold text-danger" style="font-size: 18px;">
            <i class="fas fa-exclamation-triangle mr-2"></i>Phiếu quá hạn
            </h6>
            </div>
                <div class="card-body" style="padding-top: 10px;">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-hashtag mr-1"></i>{LANG.slip_id}</th>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-user mr-1"></i>Học sinh</th>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-clock mr-1"></i>Hạn trả</th>
                                </tr>
                            </thead>
                            <tbody>
                            <!-- BEGIN: overdue_slips -->
                            <tr>
                                <td><strong>#{SLIP.id}</strong></td>
                                <td>
                                    <div class="fw-bold">{SLIP.full_name}</div>
                                    <small class="text-muted">{SLIP.student_code}</small>
                                </td>
                                <td class="text-danger fw-bold">{SLIP.due_date}</td>
                            </tr>
                            <!-- END: overdue_slips -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance History -->
    <div class="row" style="margin-top: 5px;">
    <div class="col-12">
    <div class="card shadow">
    <div class="card-header py-2">
    <h6 class="m-0 font-weight-bold text-info" style="font-size: 18px;">
    <i class="fas fa-tools mr-2"></i>Lịch sử bảo trì & thanh lý
    </h6>
    </div>
                <div class="card-body" style="padding-top: 10px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="maintenance-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-wrench mr-1"></i>Công cụ</th>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-cog mr-1"></i>Loại</th>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-comment mr-1"></i>Lý do</th>
                                    <th style="font-size: 16px; font-weight: bold;"><i class="fas fa-calendar-alt mr-1"></i>Ngày thực hiện</th>
                                </tr>
                            </thead>
                            <tbody>
                            <!-- BEGIN: maintenance_history -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold">{SLIP.tool_name}</div>
                                            <small class="text-muted">{SLIP.tool_code}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{SLIP.type == 'maintenance' ? 'warning' : 'danger'}">
                                        {SLIP.type_text}
                                    </span>
                                </td>
                                <td>{SLIP.reason}</td>
                                <td>{SLIP.create_date}</td>
                            </tr>
                            <!-- END: maintenance_history -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{NV_BASE_SITEURL}modules/{MODULE_NAME}/js/admin.js"></script>
<script>
// Initialize DataTables if available
if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $(document).ready(function() {
        $('#borrowing-history-table').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Tìm kiếm:",
                "lengthMenu": "Hiển thị _MENU_ mục",
                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                "paginate": {
                    "first": "Đầu",
                    "last": "Cuối",
                    "next": "Tiếp",
                    "previous": "Trước"
                }
            }
        });

        $('#maintenance-table').DataTable({
            "pageLength": 5,
            "language": {
                "search": "Tìm kiếm:",
                "lengthMenu": "Hiển thị _MENU_ mục",
                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                "paginate": {
                    "first": "Đầu",
                    "last": "Cuối",
                    "next": "Tiếp",
                    "previous": "Trước"
                }
            }
        });
    });
}

// Filter functionality
document.getElementById('filter-btn').addEventListener('click', function() {
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;

    // Build URL manually
    var finalUrl = '/nukeviet/admin/index.php?language=vi&nv=tool-slip-management&op=reports';
    var params = [];

    if (startDate) {
        params.push('start_date=' + encodeURIComponent(startDate));
    }
    if (endDate) {
        params.push('end_date=' + encodeURIComponent(endDate));
    }

    if (params.length > 0) {
        finalUrl += '&' + params.join('&');
    }

    console.log('Redirecting to:', finalUrl);
    window.location.href = finalUrl;
});



</script>
</div>
<!-- END: main -->
