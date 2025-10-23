<!-- BEGIN: main -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_NAME}/css/main.css">
<div class="tsm-module">
<!-- BEGIN: not_installed -->
<div class="alert alert-warning">
    <h4>{LANG.module_not_installed}</h4>
    <p>{MESSAGE}</p>
</div>
<!-- END: not_installed -->
<h2>{LANG.reports}</h2>

<div class="form-group">
    <label>{LANG.date_range}</label>
    <div class="row">
        <div class="col-md-3">
            <input type="date" class="form-control" id="start_date" value="{START_DATE}">
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" id="end_date" value="{END_DATE}">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" id="filter-btn">{LANG.filter}</button>
        </div>
    </div>
</div>

<h3>{LANG.borrowing_history}</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>{LANG.slip_id}</th>
            <th>{LANG.student}</th>
            <th>{LANG.borrow_date}</th>
            <th>{LANG.due_date}</th>
            <th>{LANG.return_date}</th>
            <th>{LANG.status}</th>
        </tr>
    </thead>
    <tbody>
        <!-- BEGIN: borrowing_history -->
        <tr>
            <td>{BORROWING.id}</td>
            <td>{BORROWING.full_name} ({BORROWING.student_code})</td>
            <td>{BORROWING.borrow_date}</td>
            <td>{BORROWING.due_date}</td>
            <td>{BORROWING.return_date}</td>
            <td>{BORROWING.status_text}</td>
        </tr>
        <!-- END: borrowing_history -->
    </tbody>
</table>

<h3>{LANG.category_borrow_stats}</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>{LANG.category}</th>
            <th>{LANG.borrow_count}</th>
        </tr>
    </thead>
    <tbody>
        <!-- BEGIN: category_stats -->
        <tr>
            <td>{STAT.category_name}</td>
            <td>{STAT.borrow_count}</td>
        </tr>
        <!-- END: category_stats -->
    </tbody>
</table>

<h3>{LANG.overdue_slips}</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>{LANG.slip_id}</th>
            <th>{LANG.student}</th>
            <th>{LANG.due_date}</th>
        </tr>
    </thead>
    <tbody>
        <!-- BEGIN: overdue_slips -->
        <tr>
            <td>{SLIP.id}</td>
            <td>{SLIP.full_name} ({SLIP.student_code})</td>
            <td>{SLIP.due_date}</td>
        </tr>
        <!-- END: overdue_slips -->
    </tbody>
</table>

<h3>{LANG.maintenance_history}</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>{LANG.tool}</th>
            <th>{LANG.type}</th>
            <th>{LANG.reason}</th>
            <th>{LANG.create_date}</th>
        </tr>
    </thead>
    <tbody>
        <!-- BEGIN: maintenance_history -->
        <tr>
            <td>{SLIP.tool_name} ({SLIP.tool_code})</td>
            <td>{SLIP.type_text}</td>
            <td>{SLIP.reason}</td>
            <td>{SLIP.create_date}</td>
        </tr>
        <!-- END: maintenance_history -->
    </tbody>
</table>

<script src="{NV_BASE_SITEURL}modules/{MODULE_NAME}/js/admin.js"></script>
</div>
<!-- END: main -->
</div>
<!-- END: main -->
