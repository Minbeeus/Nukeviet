<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/tools.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-default pull-right" href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=tools"><em class="fa fa-arrow-left"></em> {LANG.back_to_list}</a>
        <h3 class="panel-title">{PAGE_TITLE}</h3>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" action="{FORM_ACTION}" method="post">
            <input type="hidden" name="id" value="{TOOL.id}">

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.tool_name}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{TOOL.name}" required="required" placeholder="{LANG.tool_name}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.tool_code}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="tool_code" value="{TOOL.tool_code}" required="required" placeholder="{LANG.tool_code}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.category}</label>
                <div class="col-sm-9">
                    <select name="category_id" class="form-control" required="required">
                        <option value="">{LANG.select_category}</option>
                                                <option value="{CAT.id}"{CAT.selected}>{CAT.name}</option>
                                            </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.description}</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="description" rows="5" placeholder="{LANG.description}">{TOOL.description}</textarea>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary">{LANG.save}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END: main -->