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
<!-- BEGIN: form -->
<h2>{LANG.create_borrowing_slip}</h2>
<form method="post" action="">
    <div class="form-group">
        <label>{LANG.student}</label>
        <select class="form-control" name="student_id" required>
            <option value="0">-- {LANG.select_student} --</option>
            <!-- BEGIN: student -->
            <option value="{STUDENT.id}">{STUDENT.full_name} ({STUDENT.student_code})</option>
            <!-- END: student -->
        </select>
    </div>
    <div class="form-group">
        <label>{LANG.borrow_date}</label>
        <input type="date" class="form-control" name="borrow_date" value="{DATA.borrow_date}" required>
    </div>
    <div class="form-group">
        <label>{LANG.due_date}</label>
        <input type="date" class="form-control" name="due_date" value="{DATA.due_date}" required>
    </div>
    <div class="form-group">
    <label>{LANG.tools}</label>
    <input type="text" class="form-control mb-2" id="tool-search" placeholder="{LANG.search} {LANG.tools}">
    <select multiple class="form-control" id="tool-select" name="tool_ids[]" required>
    <!-- BEGIN: tool -->
    <option value="{TOOL.id}">{TOOL.name} ({TOOL.tool_code})</option>
        <!-- END: tool -->
        </select>
    </div>
    <div class="form-group">
        <label>{LANG.note}</label>
        <textarea class="form-control" name="note" rows="3">{DATA.note}</textarea>
    </div>
    <!-- BEGIN: error -->
    <div class="alert alert-danger">{ERROR}</div>
    <!-- END: error -->
    <button type="submit" name="submit" class="btn btn-primary">{GLANG.submit}</button>
    <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing" class="btn btn-secondary">{GLANG.cancel}</a>
</form>
<!-- END: form -->

<!-- BEGIN: list -->
<script>console.log('Borrowing list loaded');</script>
<h2>{LANG.borrowing_management}</h2>
<div class="mb-3">
<button id="btn-add-slip" class="btn btn-primary">{LANG.create_borrowing_slip}</button>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>{LANG.slip_id}</th>
            <th>{LANG.student}</th>
            <th>{LANG.borrow_date}</th>
            <th>{LANG.due_date}</th>
            <th>{LANG.tool_count}</th>
            <th>{LANG.status}</th>
            <th>{LANG.actions}</th>
        </tr>
    </thead>
    <tbody>
        <!-- BEGIN: slip -->
        <tr>
            <td>{SLIP.id}</td>
            <td>{SLIP.full_name} ({SLIP.student_code})</td>
            <td>{SLIP.borrow_date}</td>
            <td>{SLIP.due_date}</td>
            <td>{SLIP.tool_count}</td>
            <td>{SLIP.status_text}</td>
            <td>
            <!-- BEGIN: view_btn -->
            <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing&action=view&id={SLIP.id}" class="btn btn-sm btn-info">{LANG.view}</a>
            <!-- END: view_btn -->
            <!-- BEGIN: return_btn -->
            <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="return">
                <input type="hidden" name="slip_id" value="{SLIP.id}">
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{LANG.confirm_return}')">{LANG.return_tools}</button>
                 </form>
                 <!-- END: return_btn -->
             </td>
        </tr>
        <!-- END: slip -->
    </tbody>
</table>

<!-- BEGIN: generate_page -->
<div class="text-center">{GENERATE_PAGE}</div>
<!-- END: generate_page -->
</div>
<!-- END: list -->

<!-- BEGIN: view -->
<h2>{LANG.view_borrowing_slip}</h2>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{LANG.slip_details}</h5>
        <p><strong>{LANG.slip_id}:</strong> {SLIP.id}</p>
        <p><strong>{LANG.student}:</strong> {SLIP.full_name} ({SLIP.student_code})</p>
        <p><strong>{LANG.borrow_date}:</strong> {SLIP.borrow_date}</p>
        <p><strong>{LANG.due_date}:</strong> {SLIP.due_date}</p>
        <p><strong>{LANG.return_date}:</strong> {SLIP.return_date}</p>
        <p><strong>{LANG.status}:</strong> {SLIP.status_text}</p>
        <p><strong>{LANG.note}:</strong> {SLIP.note}</p>
        <h6>{LANG.tools}:</h6>
        <ul>
            <!-- BEGIN: tool -->
            <li>{TOOL.name} ({TOOL.code}) - {TOOL.category_name}</li>
            <!-- END: tool -->
        </ul>
    </div>
</div>
<a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing" class="btn btn-secondary">{GLANG.back}</a>
<!-- END: view -->

<!-- Modal -->
<div id="tsmActionModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<!-- END: main -->
<script>console.log('Inline script executed');</script>
<script src="/nukeviet/modules/tool-slip-management/js/admin.js"></script>
