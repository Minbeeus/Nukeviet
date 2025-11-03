<!-- BEGIN: main -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_NAME}/css/admin.css?v=1.8">
<script src="{NV_BASE_SITEURL}modules/{MODULE_NAME}/js/admin.js"></script>

<div class="tsm-module reports-page">
    <div class="container-fluid">
<!-- BEGIN: not_installed -->
<div class="alert alert-warning">
    <h4>{LANG.module_not_installed}</h4>
    <p>{MESSAGE}</p>
</div>
<!-- END: not_installed -->

<!-- BEGIN: view -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{LANG.tool_details}</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>{LANG.tool_code}:</strong> {TOOL.tool_code}</p>
                <p><strong>{LANG.name}:</strong> {TOOL.name}</p>
                <p><strong>{LANG.category}:</strong> {TOOL.category_name}</p>
            </div>
            <div class="col-md-6">
                <p><strong>{LANG.status}:</strong> <span class="badge bg-{TOOL.status_class}">{TOOL.status_text}</span></p>
                <p><strong>{LANG.added_date}:</strong> {TOOL.added_date}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p><strong>{LANG.description}:</strong></p>
                <div>{TOOL.description}</div>
            </div>
        </div>
        <div class="text-right mt-3">
            <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools" class="btn btn-secondary">{GLANG.back}</a>
        </div>
    </div>
</div>
<!-- END: view -->



<!-- Generic modal used for view/edit/maintenance/disposal content loaded via AJAX -->
<div class="modal fade" id="tsmActionModal" tabindex="-1" aria-labelledby="tsmActionModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
            <h5 class="modal-title w-100 text-center" id="tsmActionModalLabel"></h5>
            </div>
            <div class="modal-body">
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideModalById('tsmActionModal', null);">{LANG.close}</button>
            </div>
        </div>
    </div>
</div>

<!-- BEGIN: list -->
<div class="card shadow mb-4">
    <div class="card-header py-3" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 15px; align-items: center;" id="tsm-filters" data-base="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools">
        <input type="text" class="form-control form-control-sm" placeholder="{LANG.search}" id="search-q" value="{Q}" aria-label="Search" style="width: 220px; font-size: 16px;">
        <select class="form-control form-control-sm" id="search-category" aria-label="Category" style="width: 180px; font-size: 16px;">
        <option value="0">-- {LANG.all_categories} --</option>
        <!-- BEGIN: category_filter -->
        <option value="{CATEGORY.id}" {SELECTED}>{CATEGORY.name}</option>
        <!-- END: category_filter -->
        </select>
        <select class="form-control form-control-sm" id="search-status" aria-label="Status" style="width: 180px; font-size: 16px;">
        <option value="">-- {LANG.all_status} --</option>
        <!-- BEGIN: status_filter -->
        <option value="{STATUS_KEY}" {SELECTED}>{STATUS_VALUE}</option>
        <!-- END: status_filter -->
        </select>
    </div>
    <button type="button" class="btn btn-success btn-sm" id="btn-add-tool" aria-label="{LANG.add_tool}" style="white-space: nowrap;">
    <i class="fas fa-plus" aria-hidden="true"></i> {LANG.add_tool}
    </button>
    </div>
    <div class="card-body" style="margin-bottom: 20px;">

<!-- BEGIN: add-form-inline -->
<div id="addToolForm" style="display: none; margin-top: 20px; padding: 20px; border: 1px solid #ddd; background: #f9f9f9; position: relative;">
    <button type="button" class="btn-close" id="add-tool-close" style="position: absolute; top: 10px; right: 10px;" aria-label="Close"></button>
    <h5>{LANG.add_tool}</h5>
    <!-- BEGIN: main.form -->
    <form id="add-tool-form" method="post" action="/nukeviet/admin/index.php?nv=tool-slip-management&op=tools">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tool_code"><i class="fas fa-hashtag"></i> {LANG.tool_code}</label>
                    <input type="text" class="form-control" id="tool_code" name="tool_code" value="{DATA.tool_code}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name"><i class="fas fa-tag"></i> {LANG.name}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{DATA.name}" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="description"><i class="fas fa-align-left"></i> {LANG.description}</label>
            <textarea class="form-control" id="description" name="description" rows="3">{DATA.description}</textarea>
        </div>
        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
        <label for="category_id"><i class="fas fa-folder"></i> {LANG.category}</label>
        <select class="form-control" id="category_id" name="category_id" required>
        <option value="0">-- {LANG.select_category} --</option>
        <!-- BEGIN: category -->
        <option value="{CATEGORY.id}" {CATEGORY_SELECTED}>{CATEGORY.name}</option>
        <!-- END: category -->
        </select>
        </div>
        </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="status"><i class="fas fa-info-circle"></i> {LANG.status}</label>
                    <select class="form-control" id="status" name="status" required>
                        <!-- BEGIN: status -->
                        <option value="{STATUS_KEY}" {STATUS_SELECTED}>{STATUS_VALUE}</option>
                        <!-- END: status -->
                    </select>
                </div>
            </div>
        </div>
        <!-- BEGIN: error -->
        <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="add-tool-error">
            <i class="fas fa-exclamation-circle"></i> <span id="add-tool-error-text"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <!-- END: error -->
        <div class="form-group text-right">
            <button type="button" class="btn btn-secondary" id="add-tool-cancel">{GLANG.cancel}</button>
            <button type="button" class="btn btn-success" id="add-tool-submit"><i class="fas fa-save"></i> {GLANG.submit}</button>
        </div>
    </form>
    <!-- END: main.form -->
</div>
<!-- END: add-form-inline -->

<div class="table-responsive">
        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
        <thead class="table-dark">
        <tr>
            <th><i class="fas fa-hashtag"></i> {LANG.tool_code}</th>
            <th><i class="fas fa-tag"></i> {LANG.name}</th>
            <th><i class="fas fa-folder"></i> {LANG.category}</th>
            <th><i class="fas fa-info-circle"></i> {LANG.status}</th>
            <th><i class="fas fa-cogs"></i> {LANG.actions}</th>
                </tr>
            </thead>
        <tbody>
            <!-- BEGIN: tool -->
        <tr>
            <td><strong>{TOOL.tool_code}</strong></td>
            <td>{TOOL.name}</td>
            <td>{TOOL.category_name}</td>
            <td class="text-center align-middle">
            <span class="badge tool-status-badge bg-{TOOL.status_class}">{TOOL.status_text}</span>
            </td>
            <td class="text-center">
            <div class="d-flex gap-2 align-items-center justify-content-center">
                        <a href="/nukeviet/admin/index.php?nv=tool-slip-management&op=tools&action=view&id={TOOL.id}&ajax=1" class="btn btn-outline-secondary btn-sm action-ajax" title="Xem thông tin chi tiết" aria-label="Xem thông tin chi tiết">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            <span class="visually-hidden">Xem thông tin chi tiết</span>
                        </a>
                        <a href="/nukeviet/admin/index.php?nv=tool-slip-management&op=tools&action=edit&id={TOOL.id}&ajax=1" class="btn btn-outline-secondary btn-sm action-ajax" title="Sửa thông tin" aria-label="Sửa thông tin">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                            <span class="visually-hidden">Sửa thông tin</span>
                        </a>
                        <a href="/nukeviet/admin/index.php?nv=tool-slip-management&op=tools&action=maintenance&tool_id={TOOL.id}&ajax=1" class="btn btn-outline-secondary btn-sm action-ajax" title="Tạo phiếu bảo trì" aria-label="Tạo phiếu bảo trì">
                            <i class="fas fa-tools" aria-hidden="true"></i>
                            <span class="visually-hidden">Tạo phiếu bảo trì</span>
                        </a>
                        <a href="/nukeviet/admin/index.php?nv=tool-slip-management&op=tools&action=disposal&tool_id={TOOL.id}&ajax=1" class="btn btn-outline-secondary btn-sm action-ajax" title="Tạo phiếu huỷ" aria-label="Tạo phiếu huỷ">
                            <i class="fas fa-trash-alt" aria-hidden="true"></i>
                            <span class="visually-hidden">Tạo phiếu huỷ</span>
                        </a>
                    </div>
            </td>
        </tr>
                    <!-- END: tool -->
                </tbody>
            </table>
        </div>

        <!-- BEGIN: generate_page -->
        <nav aria-label="Page navigation">
            {GENERATE_PAGE}
        </nav>
        <!-- END: generate_page -->
    </div>
</div>
<!-- END: list -->
    </div>
</div>
<!-- END: list -->
<!-- END: main -->

