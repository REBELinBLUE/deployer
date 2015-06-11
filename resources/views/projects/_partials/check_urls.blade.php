<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('checkUrls.create') }}" data-toggle="modal" data-target="#checkurl"><span class="fa fa-plus"></span> {{ Lang::get('checkUrls.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('checkUrls.label') }}</h3>
    </div>


    <div class="box-body" id="no_checkurls">
        <p>{{ Lang::get('checkUrls.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="checkurl_list">
            <thead>
                <tr>
                    <th>{{ Lang::get('checkUrls.title') }}</th>
                    <th>{{ Lang::get('checkUrls.url') }}</th>
                    <th>{{ Lang::get('checkUrls.frequency') }}</th>
                    <th>{{ Lang::get('checkUrls.last_status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script type="text/template" id="checkUrls-template">
    <td><%- title %></td>
    <td><%- url %></td>
    <td><%- interval_label %></td>
    <td>
        <span class="label label-<%- status_css %>">
            <i class="fa fa-<%-icon_css %>"></i>
            <%- status %>
        </span>
    </td>
    <td>
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('checkUrls.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#checkurl"><i class="fa fa-edit"></i></button>
        </div>
    </td>
</script>

<script type="text/javascript">
    Lang.CheckUrls = {
        create: '{{ Lang::get('checkUrls.create') }}',
        edit: '{{ Lang::get('checkUrls.edit') }}',
        success: '{{ Lang::get('checkUrls.successful') }}',
        failure: '{{ Lang::get('checkUrls.failed') }}',
        yes: '{{ Lang::get('app.yes') }}',
        no: '{{ Lang::get('app.no') }}',
        length: '{{ strtolower(Lang::get('checkUrls.length')) }}'
    };
</script>