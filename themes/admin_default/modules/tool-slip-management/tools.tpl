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
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="post" action="">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status"><i class="fas fa-info-circle"></i> {LANG.status}</label>
                        <select class="form-control" id="status" name="status">
                            <!-- BEGIN: status -->
                            <option value="{STATUS_KEY}" {STATUS_SELECTED}>{STATUS_VALUE}</option>
                            <!-- END: status -->
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="added_date"><i class="fas fa-calendar"></i> {LANG.added_date}</label>
                        <input type="date" class="form-control" id="added_date" name="added_date" value="{DATA.added_date}" required>
                    </div>
                </div>
            </div>
            <!-- BEGIN: error -->
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {ERROR}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <!-- END: error -->
            <div class="form-group text-right">
                <button type="submit" name="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> {GLANG.submit}
                </button>
                <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> {GLANG.cancel}
                </a>
            </div>
        </form>
    </div>
</div>
<!-- END: form -->

<!-- BEGIN: list -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tools"></i> {LANG.tools_management}</h6>
        <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools&action=add" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> {LANG.add_tool}
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="{LANG.search}" id="search-q" value="{Q}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="search-category">
                    <option value="0">-- {LANG.all_categories} --</option>
                    <!-- BEGIN: category_filter -->
                    <option value="{CATEGORY.id}" {SELECTED}>{CATEGORY.name}</option>
                    <!-- END: category_filter -->
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="search-status">
                    <option value="">-- {LANG.all_status} --</option>
                    <!-- BEGIN: status_filter -->
                    <option value="{STATUS_KEY}" {SELECTED}>{STATUS_VALUE}</option>
                    <!-- END: status_filter -->
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary" id="search-btn" data-base="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools">
                    <i class="fas fa-filter"></i> {LANG.search}
                </button>
            </div>
        </div>

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
            <td>
            <span class="badge tool-status-badge bg-{TOOL.status_class}">{TOOL.status_text}</span>
        </td>
            <td>
                    <div class="btn-group" role="group">
                    <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools&action=edit&id={TOOL.id}" class="btn btn-outline-warning btn-sm" title="{GLANG.edit}">
                    <i class="fas fa-edit"></i> {GLANG.edit}
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-exchange-alt"></i> {LANG.change_status}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- BEGIN: status_option -->
                            <li><a class="dropdown-item" data-change-status="1" href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=tools&action=change_status&id={TOOL.id}&status={STATUS_KEY}">{STATUS_VALUE}</a></li>
                            <!-- END: status_option -->
                        </ul>
                    </div>
                    <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=maintenance&tool_id={TOOL.id}" class="btn btn-outline-primary btn-sm" title="{LANG.create_maintenance}">
                        <i class="fas fa-tools"></i> {LANG.create_maintenance}
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

<script src="{NV_BASE_SITEURL}modules/{MODULE_NAME}/js/admin.js"></script>
</div>
<!-- END: list -->
<!-- END: main -->
