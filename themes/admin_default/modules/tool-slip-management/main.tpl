<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/main.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/css/utilities.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{LANG.main}</h3>
    </div>
    <div class="panel-body">
        <div class="row">

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><em class="fa fa-wrench"></em></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tổng số dụng cụ</span>
                        <span class="info-box-number">{STATS.total}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><em class="fa fa-check-circle"></em></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sẵn có</span>
                        <span class="info-box-number">{STATS.available}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><em class="fa fa-hourglass-half"></em></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Đang mượn</span>
                        <span class="info-box-number">{STATS.borrowed}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><em class="fa fa-exclamation-triangle"></em></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Phiếu quá hạn</span>
                        <span class="info-box-number">{OVERDUE_SLIPS}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: main -->