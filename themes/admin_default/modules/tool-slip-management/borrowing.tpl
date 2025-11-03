<!-- BEGIN: main -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_NAME}/css/admin.css?v=1.8">
<div class="tsm-module reports-page">
    <div class="container-fluid">
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
<!-- BEGIN: form -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
    <h5 class="card-title mb-0"><i class="fas fa-plus-circle mr-3"></i>{LANG.create_borrowing_slip}</h5>
</div>
<div class="card-body">
<form method="post" action="">
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
                <label class="font-weight-bold"><i class="fas fa-user mr-2"></i>{LANG.student}</label>
                    <select class="form-control" name="student_id" required>
                        <option value="0">-- {LANG.select_student} --</option>
                    <!-- BEGIN: student -->
                    <option value="{STUDENT.id}">{STUDENT.full_name} ({STUDENT.student_code})</option>
                        <!-- END: student -->
                    </select>
            </div>
        </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i>{LANG.borrow_date}</label>
                    <input type="date" class="form-control" name="borrow_date" value="{DATA.borrow_date}" required>
                </div>
            </div>
            <div class="col-md-3">
            <div class="form-group">
                <label class="font-weight-bold"><i class="fas fa-clock mr-2"></i>{LANG.due_date}</label>
                    <input type="date" class="form-control" name="due_date" value="{DATA.due_date}" required>
                </div>
        </div>
    </div>
        <div class="form-group">
        <label class="font-weight-bold"><i class="fas fa-tools mr-2"></i>{LANG.tools}</label>
        <div class="input-group mb-2">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="tool-search" placeholder="{LANG.search} {LANG.tools}">
            </div>
            <select multiple class="form-control" id="tool-select" name="tool_ids[]" required style="min-height: 120px;">
            <!-- BEGIN: tool -->
            <option value="{TOOL.id}">{TOOL.name} ({TOOL.tool_code})</option>
                    <!-- END: tool -->
                    </select>
            </div>
            <div class="form-group">
                <label class="font-weight-bold"><i class="fas fa-sticky-note mr-2"></i>{LANG.note}</label>
                <textarea class="form-control" name="note" rows="3" placeholder="{LANG.note_placeholder}">{DATA.note}</textarea>
            </div>
            <!-- BEGIN: error -->
            <div class="alert alert-danger fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>{ERROR}
            </div>
            <!-- END: error -->
            <div class="form-group text-right">
                <button type="submit" name="submit" class="btn btn-success btn-lg"><i class="fas fa-save mr-2"></i>{GLANG.submit}</button>
                <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing" class="btn btn-outline-secondary btn-lg ml-2"><i class="fas fa-times mr-2"></i>{GLANG.cancel}</a>
            </div>
        </form>
    </div>
</div>
<!-- END: form -->

<!-- BEGIN: list -->
<div class="card shadow-sm mb-4">
<div class="card-header py-3" style="display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; gap: 15px; align-items: center;">
        <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Tìm kiếm theo mã sinh viên, tên..." style="width: 220px; font-size: 16px;" />
        <form method="get" class="d-inline-block">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" />
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
            <input type="hidden" name="{NV_OP_VARIABLE}" value="borrowing" />
            <select name="filter" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 180px; font-size: 16px;">
        <option value="">Tất cả trạng thái</option>
    <option value="borrowing" {FILTER_BORROWING}>Đang mượn</option>
    <option value="returned" {FILTER_RETURNED}>Đã trả</option>
<option value="overdue" {FILTER_OVERDUE}>Quá hạn</option>
</select>
</form>
</div>
<button id="btn-add-slip" class="btn btn-success btn-sm" style="white-space: nowrap;">{LANG.create_borrowing_slip}</button>
</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table id="borrowing-table" class="table table-hover mb-0">
        <thead class="thead-light">
        <tr>
            <th><i class="fas fa-hashtag mr-1"></i>{LANG.slip_id}</th>
            <th><i class="fas fa-user mr-1"></i>{LANG.student}</th>
            <th><i class="fas fa-calendar-alt mr-1"></i>{LANG.borrow_date}</th>
            <th><i class="fas fa-clock mr-1"></i>{LANG.due_date}</th>
            <th><i class="fas fa-tools mr-1"></i>{LANG.tool_count}</th>
            <th><i class="fas fa-info-circle mr-1"></i>{LANG.status}</th>
                <th><i class="fas fa-cogs mr-1"></i>{LANG.actions}</th>
                </tr>
            </thead>
        <tbody>
            <!-- BEGIN: slip -->
        <tr>
            <td><strong>#{SLIP.id}</strong></td>
            <td>{SLIP.full_name}<br><small class="text-muted">{SLIP.student_code}</small></td>
            <td>{SLIP.borrow_date}</td>
            <td class="{SLIP.status == 2 ? 'text-danger' : ''}">{SLIP.due_date}</td>
            <td><span class="badge badge-pill badge-secondary">{SLIP.tool_count}</span></td>
            <td>
            <span class="label label-{SLIP.status_class}">{SLIP.status_text}</span>
            </td>
           <td>
              <!-- BEGIN: view_btn -->
           <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing&action=view&id={SLIP.id}" class="btn btn-link btn-sm text-primary" title="{LANG.view}"><i class="fas fa-eye"></i> {LANG.view}</a>
           <!-- END: view_btn -->
           <!-- BEGIN: return_btn -->
           <form method="post" style="display:inline;" onsubmit="return confirm('{LANG.confirm_return}')">
           <input type="hidden" name="action" value="return">
           <input type="hidden" name="slip_id" value="{SLIP.id}">
           <button type="submit" class="btn btn-link btn-sm text-success" title="{LANG.return_tools}"><i class="fas fa-undo"></i> {LANG.return_tools}</button>
           </form>
           <!-- END: return_btn -->
           </td>
                    </tr>
                    <!-- END: slip -->
                </tbody>
            </table>
        </div>
        <!-- BEGIN: generate_page -->
        <div class="card-footer text-center bg-light">
            {GENERATE_PAGE}
        </div>
        <!-- END: generate_page -->
    </div>
</div>
<!-- END: list -->

<!-- BEGIN: view -->
<div class="card shadow-sm mb-4 view-slip">
    <div class="card-header bg-primary text-white">
    <h5 class="card-title mb-0" style="font-size: 18px; font-weight: bold;"><i class="fas fa-eye mr-3"></i>{LANG.view_borrowing_slip} #{SLIP.id}</h5>
</div>
<div class="card-body">
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="font-weight-bold text-muted" style="font-size: 16px;"><i class="fas fa-user mr-2"></i>{LANG.student}</label>
            <p class="mb-0" style="font-size: 16px;">{SLIP.full_name} <small class="text-muted">({SLIP.student_code})</small><br><small class="text-muted">SĐT: {SLIP.phone_number}</small></p>
        </div>
    </div>
    <div class="col-md-6">
    <div class="mb-3">
        <label class="font-weight-bold text-muted" style="font-size: 16px;"><i class="fas fa-info-circle mr-2"></i>{LANG.status}</label>
        <p class="mb-0" style="font-size: 16px;">
        <span class="label label-{SLIP.status_class}">{SLIP.status_text}</span>
        </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="font-weight-bold text-muted" style="font-size: 16px;"><i class="fas fa-calendar-alt mr-2"></i>{LANG.borrow_date}</label>
                    <p class="mb-0" style="font-size: 16px;">{SLIP.borrow_date}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="font-weight-bold text-muted" style="font-size: 16px;"><i class="fas fa-clock mr-2"></i>{LANG.due_date}</label>
                    <p class="mb-0 {SLIP.status == 2 ? 'text-danger' : ''}" style="font-size: 16px;">{SLIP.due_date}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="font-weight-bold text-muted" style="font-size: 16px;"><i class="fas fa-undo mr-2"></i>{LANG.return_date}</label>
                    <p class="mb-0" style="font-size: 16px;">{SLIP.return_date}</p>
                </div>
            </div>
        </div>
        <!-- IF SLIP.notes -->
        <div class="mb-3">
        <label class="font-weight-bold text-muted" style="font-size: 16px;"><i class="fas fa-sticky-note mr-2"></i>{LANG.note}</label>
        <p class="mb-0" style="font-size: 16px;">{SLIP.notes}</p>
        </div>
        <!-- ENDIF -->
        <div class="mb-3">
            <label class="font-weight-bold text-muted" style="font-size: 16px;"><i class="fas fa-tools mr-2"></i>{LANG.tools}</label>
            <div class="list-group">
                <!-- BEGIN: tool -->
                <div class="list-group-item d-flex justify-content-between align-items-center" style="font-size: 16px;">
                <span>{TOOL.name} <small class="text-muted">({TOOL.code})</small></span>
                <span class="badge badge-primary badge-pill">{TOOL.category_name}</span>
                </div>
                <!-- END: tool -->
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=borrowing" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-2"></i>Quay lại</a>
    </div>
</div>
<!-- END: view -->

<!-- Modal -->
<div id="tsmActionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tsmActionModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
<div class="modal-content shadow">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title" id="tsmActionModalLabel"></h5>
<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
</div>
</div>
</div>
    </div>
</div>

<!-- END: main -->
