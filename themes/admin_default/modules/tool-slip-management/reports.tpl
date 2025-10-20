<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/reports.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{LANG.reports}</h3>
    </div>
    <div class="panel-body">
        <form action="{FORM_ACTION}" method="get" class="form-inline margin-bottom well">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" />
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
            <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
            <div class="form-group">
                <label for="from_date">{LANG.from_date}</label>
                <input type="text" class="form-control datepicker" readonly="readonly" placeholder="{LANG.from_date}" name="from_date" id="from_date" value="{FROM_DATE}">
            </div>
            <div class="form-group">
                <label for="to_date">{LANG.to_date}</label>
                <input type="text" class="form-control datepicker" readonly="readonly" placeholder="{LANG.to_date}" name="to_date" id="to_date" value="{TO_DATE}">
            </div>
            <button type="submit" class="btn btn-primary">{LANG.view_report}</button>
        </form>

        <h4 class="margin-bottom">{LANG.borrowing_history} ({FROM_DATE} - {TO_DATE})</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">{LANG.stt}</th>
                        <th class="text-center">{LANG.slip_id}</th>
                        <th>{LANG.borrower}</th>
                        <th>{LANG.tool}</th>
                        <th class="text-center">{LANG.borrow_date}</th>
                        <th class="text-center">{LANG.due_date}</th>
                        <th class="text-center">{LANG.return_date}</th>
                        <th class="text-center">{LANG.status}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{ROW.stt}</td>
                        <td class="text-center">{ROW.slip_id}</td>
                        <td>{ROW.student_name} ({ROW.student_code})</td>
                        <td>{ROW.tool_name} ({ROW.tool_code})</td>
                        <td class="text-center">{ROW.borrow_date_formatted}</td>
                        <td class="text-center">{ROW.due_date_formatted}</td>
                        <td class="text-center">{ROW.return_date_formatted}</td>
                        <td class="text-center"><span class="label label-{ROW.status_class}">{ROW.status_text}</span></td>
                    </tr>
                    </tbody>
            </table>
        </div>
        <div class="alert alert-warning">
            {LANG.no_data}
        </div>
        </div>
</div>
<!-- END: main -->