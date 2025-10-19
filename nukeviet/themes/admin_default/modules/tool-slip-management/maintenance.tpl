<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/maintenance.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-primary pull-right" href="{MODULE_URL}&action=add"><em class="fa fa-plus"></em> {LANG.add_new_maintenance}</a>
        <h3 class="panel-title">{LANG.maintenance_manage}</h3>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>{LANG.tool}</th>
                        <th class="text-center">{LANG.slip_type}</th>
                        <th>{LANG.reason}</th>
                        <th class="text-center">{LANG.created_date}</th>
                        </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{SLIP.id}</td>
                        <td>{SLIP.tool_name} ({SLIP.tool_code})</td>
                        <td class="text-center"><span class="label label-{SLIP.type_class}">{SLIP.type_text}</span></td>
                        <td>{SLIP.reason}</td>
                        <td class="text-center">{SLIP.created_date_formated}</td>
                        </tr>
                    </tbody>
            </table>
        </div>

        <div class="text-center">
            {PAGINATION}
        </div>
        </div>
</div>
<!-- END: main -->