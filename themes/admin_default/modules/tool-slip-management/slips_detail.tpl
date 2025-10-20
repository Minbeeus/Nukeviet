<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/slips.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-default pull-right" href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=slips"><em class="fa fa-arrow-left"></em> {LANG.back_to_list}</a>
        <h3 class="panel-title">{PAGE_TITLE}</h3>
    </div>
    <div class="panel-body">
        <h4 class="margin-top margin-bottom">Danh sách dụng cụ đã mượn</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <tbody>
                    <tr>
                        <td class="text-center">{TOOL.ROWNUM}</td>
                        <td>{TOOL.tool_code}</td>
                        <td>{TOOL.name}</td>
                        <td>{TOOL.description}</td>
                    </tr>
                    </tbody>
            </table>
        </div>

        <div class="text-center margin-top-lg">
             <form action="{RETURN_ACTION}" method="post" onsubmit="return confirm('{LANG.confirm_return}?');">
                 <button type="submit" class="btn btn-primary btn-lg"><em class="fa fa-check-square-o"></em> {LANG.confirm_return}</button>
             </form>
        </div>
        </div>
</div>
<!-- END: main -->