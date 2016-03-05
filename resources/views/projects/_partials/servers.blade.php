<div class="box" id="manage-servers">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('servers.create') }}" data-toggle="modal" data-backdrop="static" data-target="#server"><span class="fa fa-plus"></span> {{ Lang::get('servers.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('servers.label') }}</h3>
    </div>

    <div class="box-body" v-if="!hasServers">
        <p>{{ Lang::get('servers.none') }}</p>
    </div>

    <div class="box-body table-responsive" v-else>
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
                <tr v-for="server in servers" track-by="id">
                    <td>@{{ server.name }}</td>
                    <td>@{{ server.user }}</td>
                    <td>@{{ server.ip_address }}</td>
                    <td>@{{ server.port }}</td>
                    <td>
                        <template v-if="server.deploy_code">{{ Lang::get('app.yes') }}</template>
                        <template v-else>{{ Lang::get('app.no') }}</template>
                    </td>
                    <td>
                        <span class="label label-@{{ server.status_css }}"><i class="fa fa-@{{ server.icon_css }}"></i> @{{ server.status_label }}
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default btn-test" title="{{ Lang::get('servers.test') }}" v-bind:disabled="server.isTesting"><i class="fa fa-refresh" v-bind:class="server.isTesting ? 'fa-spin' : ''"></i></button>
                            <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('servers.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#server" v-bind:disabled="server.isTesting"><i class="fa fa-edit"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
