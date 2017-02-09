<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('servers.create') }}" data-toggle="modal" data-backdrop="static" data-target="#server"><span class="fa fa-plus"></span> {{ Lang::get('servers.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('servers.label') }}</h3>
    </div>

    <div class="box-body" id="no_servers">
        <p>{{ Lang::get('servers.none') }}</p>
    </div>

    <div class="col-xs-12" id="fpm_error">
        <div class="callout callout-danger">
            <h4><i class="icon fa fa-ban"></i> {{ Lang::get('servers.fpm_failure_head') }}</h4>
            <p>{!! Lang::get('servers.fpm_failure') !!}</p>
            <code><span id="server_user_name">deploy</span> ALL=NOPASSWD: /usr/sbin/service php5-fpm restart</code>
        </div>
    </div>

    <div class="box-body table-responsive" id="server_list">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ Lang::get('servers.name') }}</th>
                    <th>{{ Lang::get('servers.connect_as') }}</th>
                    <th>{{ Lang::get('servers.ip_address') }}</th>
                    <th>{{ Lang::get('servers.port') }}</th>
                    <th>{{ Lang::get('servers.runs_code') }}</th>
                    <th>{{ Lang::get('servers.status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@push('templates')
    <script type="text/template" id="server-template">
        <td data-server-id="<%- id %>"><%- name %></td>
        <td><%- user %></td>
        <td><%- ip_address %></td>
        <td><%- port %></td>
        <td>
            <% if (deploy_code) { %>
                {{ Lang::get('app.yes') }}
            <% } else { %>
                {{ Lang::get('app.no') }}
            <% } %>
        </td>
        <td>
             <span class="label label-<%- status_css %>"><i class="fa fa-<%-icon_css %>"></i> <%- status %></span>
        </td>
        <td>
            <div class="btn-group pull-right">
                <% if (status === 'Testing') { %>
                    <button type="button" class="btn btn-default btn-test" title="{{ Lang::get('servers.test') }}" disabled><i class="fa fa-refresh fa-spin"></i></button>
                    <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('servers.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#server" disabled><i class="fa fa-edit"></i></button>
                <% } else { %>
                    <button type="button" class="btn btn-default btn-test" title="{{ Lang::get('servers.test') }}"><i class="fa fa-refresh"></i></button>
                    <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('servers.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#server"><i class="fa fa-edit"></i></button>
                <% } %>
            </div>
        </td>
    </script>
@endpush
