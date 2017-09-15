<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ trans('servers.create') }}" data-toggle="modal" data-backdrop="static" data-target="#server"><span class="fa fa-plus"></span> {{ trans('servers.create') }}</button>
        </div>
        <h3 class="box-title">{{ trans('servers.label') }}</h3>
    </div>

    <div class="box-body" id="no_servers">
        <p>{{ trans('servers.none') }}</p>
    </div>

    <div class="box-body table-responsive" id="server_list">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans('servers.name') }}</th>
                    <th>{{ trans('servers.type') }}</th>
                    <th>{{ trans('servers.connect_as') }}</th>
                    <th>{{ trans('servers.ip_address') }}</th>
                    <th>{{ trans('servers.port') }}</th>
                    <th>{{ trans('servers.runs_code') }}</th>
                    <th>{{ trans('servers.status') }}</th>
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
        <td><%- type %></td>
        <td><%- user %></td>
        <td><%- ip_address %></td>
        <td><%- port %></td>
        <td>
            <% if (deploy_code) { %>
                {{ trans('app.yes') }}
            <% } else { %>
                {{ trans('app.no') }}
            <% } %>
        </td>
        <td>
             <span class="label label-<%- status_css %>"><i class="fa fa-<%-icon_css %>"></i> <%- status %></span>
        </td>
        <td>
            <div class="btn-group pull-right">

                <% if (status === 'Testing') { %>
                    <button type="button" class="btn btn-default btn-test" title="{{ trans('servers.test') }}" disabled><i class="fa fa-refresh fa-spin"></i></button>
                    <button type="button" class="btn btn-default btn-edit" title="{{ trans('servers.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#server" disabled><i class="fa fa-edit"></i></button>
                <% } else { %>
                    <% if (has_log) { %>
                        <button type="button" class="btn btn-default btn-view" title="{{ trans('servers.log') }}" data-toggle="modal" data-backdrop="static" data-target="#result"><i class="fa fa-eye"></i></button>
                    <% } %>
                    <button type="button" class="btn btn-default btn-test" title="{{ trans('servers.test') }}"><i class="fa fa-refresh"></i></button>
                    <button type="button" class="btn btn-default btn-edit" title="{{ trans('servers.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#server"><i class="fa fa-edit"></i></button>
                <% } %>
            </div>
        </td>
    </script>
@endpush
