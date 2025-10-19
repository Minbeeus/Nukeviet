<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/slips.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-primary pull-right" href="{MODULE_URL}&action=create"><em class="fa fa-plus"></em> {LANG.create_new_slip}</a>
        <h3 class="panel-title">{LANG.slips_manage}</h3>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">{LANG.slip_id}</th>
                        <th>{LANG.borrower}</th>
                        <th class="text-center">{LANG.borrow_date}</th>
                        <th class="text-center">{LANG.due_date}</th>
                        <th class="text-center">{LANG.status}</th>
                        <th class="text-center">{LANG.actions}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{SLIP.id}</td>
                        <td>{SLIP.full_name}</td>
                        <td class="text-center">{SLIP.borrow_date_formatted}</td>
                        <td class="text-center">{SLIP.due_date_formatted}</td>
                        <td class="text-center"><span class="label label-{SLIP.status_class}">{SLIP.status_text}</span></td>
                        <td class="text-center">
                            <a href="{SLIP.link_detail}" class="btn btn-xs btn-default"><em class="fa fa-eye"></em> Xem chi tiết</a>
                        </td>
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