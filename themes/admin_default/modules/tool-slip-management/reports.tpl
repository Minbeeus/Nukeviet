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
    

<!-- Filter Section -->
<div class="card shadow-sm mb-4">
<div class="card-header bg-light">
<h6 class="card-title mb-0"><i class="fas fa-filter mr-2"></i>Lọc dữ liệu theo thời gian</h6>
    </div>
    <div class="card-body">
        <form id="report-filter" method="get" action="{NV_BASE_ADMINURL}index.php?{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=reports">
            <div class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label fw-semibold">
                    <i class="fas fa-calendar mr-1 text-primary"></i>Ngày bắt đầu
                    </label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{START_DATE}">
                </div>

        <div class="col-md-4">
                <label for="end_date" class="form-label fw-semibold">
                    <i class="fas fa-calendar mr-1 text-primary"></i>Ngày kết thúc
                    </label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{END_DATE}">
                </div>

                <div class="col-md-4">
                            <label class="form-label invisible">Action</label>
                    <button type="button" class="btn btn-primary w-100" id="filter-btn">
                    Lọc
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng phiếu mượn</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{TOTAL_BORROWINGS}</div>
                        </div>
                    <div class="col-auto">
                <i class="fas fa-book fa-2x text-primary"></i>
                </div>
                </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
    <div class="col mr-2">
    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã trả</div>
    <div class="h5 mb-0 font-weight-bold text-gray-800">{RETURNED_COUNT}</div>
    </div>
                <div class="col-auto">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đang mượn</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{ACTIVE_COUNT}</div>
                </div>
                        <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
    </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Quá hạn</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{OVERDUE_COUNT}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Main Content -->
    <div class="row">
        <!-- Borrowing History -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history mr-2"></i>{LANG.borrowing_history}
        </h6>
    </div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered table-hover" id="borrowing-history-table">
<thead class="table-light">
<tr>
<th><i class="fas fa-hashtag mr-1"></i>{LANG.slip_id}</th>
<th><i class="fas fa-user mr-1"></i>{LANG.student}</th>
<th><i class="fas fa-calendar-alt mr-1"></i>{LANG.borrow_date}</th>
<th><i class="fas fa-clock mr-1"></i>{LANG.due_date}</th>
<th><i class="fas fa-undo mr-1"></i>{LANG.return_date}</th>
<th><i class="fas fa-info-circle mr-1"></i>{LANG.status}</th>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-pie mr-2"></i>Thống kê theo danh mục
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-folder mr-1"></i>Danh mục</th>
                                    <th class="text-center"><i class="fas fa-hashtag mr-1"></i>Số lần</th>
                                </tr>
                            </thead>
                            <tbody>
                            <!-- BEGIN: category_stats -->
                            <tr>
                                <td>{STAT.category_name}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">{STAT.borrow_count}</span>
                                </td>
                            </tr>
                            <!-- END: category_stats -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Overdue Slips -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Phiếu quá hạn
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag mr-1"></i>{LANG.slip_id}</th>
                                    <th><i class="fas fa-user mr-1"></i>Học sinh</th>
                                    <th><i class="fas fa-clock mr-1"></i>Hạn trả</th>
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
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-tools mr-2"></i>Lịch sử bảo trì & thanh lý
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="maintenance-table">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-wrench mr-1"></i>Công cụ</th>
                                    <th><i class="fas fa-cog mr-1"></i>Loại</th>
                                    <th><i class="fas fa-comment mr-1"></i>Lý do</th>
                                    <th><i class="fas fa-calendar-alt mr-1"></i>Ngày thực hiện</th>
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
