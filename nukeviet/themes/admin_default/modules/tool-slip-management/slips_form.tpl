<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/slips.css">
<link href="{NV_BASE_SITEURL}assets/select2/select2.min.css" rel="stylesheet" />
<div class="panel panel-default">
    <div class="panel-body">
        <form class="form-horizontal" action="{FORM_ACTION}" method="post">

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.borrower}</label>
                <div class="col-sm-9">
                    <select id="student_id" name="student_id" class="form-control" required>
                        <option value="">{LANG.select_borrower}</option>
                        <option value="{STUDENT.id}">{STUDENT.full_name} ({STUDENT.student_code})</option>
                        </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.select_tools}</label>
                <div class="col-sm-9">
                    <select id="tool_ids" name="tool_ids[]" class="form-control" multiple="multiple" required>
                        <option value="{TOOL.id}">{TOOL.name} ({TOOL.tool_code})</option>
                        </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.due_date}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control datepicker" name="due_date" value="" placeholder="Chọn ngày hẹn trả" readonly="readonly" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">{LANG.notes}</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="notes" rows="5"></textarea>
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

<script src="{NV_BASE_SITEURL}assets/select2/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#student_id').select2();
    $('#tool_ids').select2({
        placeholder: "{LANG.select_tools}",
        allowClear: true
    });
});
</script>
<!-- END: main -->