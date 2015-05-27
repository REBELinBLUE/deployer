<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('checkUrls.create') }}" data-toggle="modal" data-target="#checkurl"><span class="fa fa-plus"></span> {{ Lang::get('checkUrls.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('checkUrls.label') }}</h3>
    </div>


    <div class="box-body" id="no_files">
        <p>{{ Lang::get('checkUrls.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="checkurl_list">
            <thead>
                <tr>
                    <th>{{ Lang::get('checkUrls.title') }}</th>
                    <th>{{ Lang::get('checkUrls.url') }}</th>
                    <th>{{ Lang::get('checkUrls.period') }}</th>
                    <th>{{ Lang::get('checkUrls.is_report') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>