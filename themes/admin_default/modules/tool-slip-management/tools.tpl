<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/tools.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <a class="btn btn-primary pull-right" href="{MODULE_URL}&action=add"><em class="fa fa-plus"></em> {LANG.add_new}</a>
        <h3 class="panel-title">{LANG.tools_manage}</h3>
    </div>
    <div class="panel-body">
        <form action="{MODULE_URL}" method="get" class="form-inline margin-bottom">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" />
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
            <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
            <div class="form-group">
                <input type="text" class="form-control" placeholder="{LANG.search_by_name_code}" name="keyword" value="{KEYWORD}">
            </div>
            <div class="form-group">
                <select name="category_id" class="form-control">
                    <option value="0">{LANG.all_categories}</option>
                                        <option value="{CAT.id}"{CAT.selected}>{CAT.name}</option>
                                    </select>
            </div>
            <button type="submit" class="btn btn-primary">{LANG.search}</button>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>{LANG.tool_code}</th>
                        <th>{LANG.tool_name}</th>
                        <th>{LANG.category}</th>
                        <th class="text-center">{LANG.status}</th>
                        <th class="text-center">{LANG.actions}</th>
                    </tr>
                </thead>
                <tbody>
                                        <tr>
                        <td class="text-center">{TOOL.id}</td>
                        <td>{TOOL.tool_code}</td>
                        <td>{TOOL.name}</td>
                        <td>{TOOL.category_name}</td>
                        <td class="text-center"><span class="label label-{TOOL.status_class}">{TOOL.status_text}</span></td>
                        <td class="text-center">
                            <a href="{TOOL.link_edit}" class="btn btn-xs btn-info" title="{LANG.edit}"><em class="fa fa-edit"></em></a>
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