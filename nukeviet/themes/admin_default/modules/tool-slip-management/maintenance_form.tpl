<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/maintenance.css">
<link href="{NV_BASE_SITEURL}assets/select2/select2.min.css" rel="stylesheet" />
<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-default pull-right" href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=maintenance"><em class="fa fa-arrow-left"></em> {LANG.back_to_list}</a>
        <h3 class="panel-title">{PAGE_TITLE}</h3>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" action="{FORM_ACTION}" method="post">

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.tool}</label>
                <div class="col-sm-9">
                    <select id="tool_id" name="tool_id" class="form-control" required="required">
                        <option value="">{LANG.select_tool}</option>
                        <option value="{TOOL.id}">{TOOL.name} ({TOOL.tool_code})</option>
                        </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.slip_type}</label>
                <div class="col-sm-9">
                    <div class="radio">
                        <label>
                            <input type="radio" name="type" value="1" checked="checked"> {LANG.maintenance}
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="type" value="2"> {LANG.disposal}
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.reason}</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="reason" rows="5" required="required" placeholder="Nêu rõ lý do bảo trì hoặc lý do hủy..."></textarea>
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

<script src="{NV_BASE_SITEURL}assets/select2/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#tool_id').select2();
});
</script>