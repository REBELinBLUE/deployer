<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('channels.create') }}" data-toggle="modal" data-target="#notification"><span class="fa fa-plus"></span> {{ Lang::get('channels.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('channels.label') }}</h3>
    </div>

    <div class="box-body" id="no_notifications">
        <p>{{ Lang::get('channels.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="notification_list">
            <thead>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <th colspan="2" class="text-center">{{ Lang::get('channels.deployments') }}</th>
                    <th colspan="3" class="text-center">{{ Lang::get('channels.urls') }}</th>
                    <th colspan="3" class="text-center">{{ Lang::get('channels.heartbeats') }}</th>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <th>{{ Lang::get('channels.name') }}</th>
                    <th>{{ Lang::get('channels.type') }}</th>
                    <th class="text-center">{{ Lang::get('channels.succeeded') }}</th>
                    <th class="text-center">{{ Lang::get('channels.failed') }}</th>
                    <th class="text-center">{{ Lang::get('channels.down') }}</th>
                    <th class="text-center">{{ Lang::get('channels.still_down') }}</th>
                    <th class="text-center">{{ Lang::get('channels.recovered') }}</th>
                    <th class="text-center">{{ Lang::get('channels.missing') }}</th>
                    <th class="text-center">{{ Lang::get('channels.still_missing') }}</th>
                    <th class="text-center">{{ Lang::get('channels.recovered') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@push('templates')
    <script type="text/template" id="notification-template">
        <td><%- name %></td>
        <td><span class="fa fa-<%- icon %>"></span> <%- label %></td>
        <td class="text-center"><% if (on_deployment_success) { %><i class="fa fa-check"></i><% } %></td>
        <td class="text-center"><% if (on_deployment_failure) { %><i class="fa fa-check"></i><% } %></td>
        <td class="text-center"><% if (on_link_down) { %><i class="fa fa-check"></i><% } %></td>
        <td class="text-center"><% if (on_link_still_down) { %><i class="fa fa-check"></i><% } %></td>
        <td class="text-center"><% if (on_link_recovered) { %><i class="fa fa-check"></i><% } %></td>
        <td class="text-center"><% if (on_heartbeat_missing) { %><i class="fa fa-check"></i><% } %></td>
        <td class="text-center"><% if (on_heartbeat_still_missing) { %><i class="fa fa-check"></i><% } %></td>
        <td class="text-center"><% if (on_heartbeat_recovered) { %><i class="fa fa-check"></i><% } %></td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('channels.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#notification"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
