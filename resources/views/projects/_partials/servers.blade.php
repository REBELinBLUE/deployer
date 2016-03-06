<div class="box" id="manage-servers">
    <div class="box-header">
        <div class="pull-right">
            <button v-on:click="newItem" type="button" class="btn btn-default" title="{{ Lang::get('servers.create') }}" data-toggle="modal" data-backdrop="static" data-target="#server"><span class="fa fa-plus"></span> {{ Lang::get('servers.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('servers.label') }}</h3>
    </div>

    <div class="box-body table-responsive" v-if="hasServers">
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
                <tr v-for="server in servers | orderBy 'order' | orderBy 'id'"
                    is="Server"
                    track-by="id"
                    :server="server"
                ></tr>
            </tbody>
        </table>
    </div>

    <div class="box-body" v-else>
        <p>{{ Lang::get('servers.none') }}</p>
    </div>
</div>

@push('templates')
    <template id="server-template">
        <tr>
            <td>@{{ server.name }}</td>
            <td>@{{ server.user }}</td>
            <td>@{{ server.ip_address }}</td>
            <td>@{{ server.port }}</td>
            <td>
                <template v-if="server.deploy_code">{{ Lang::get('app.yes') }}</template>
                <template v-else>{{ Lang::get('app.no') }}</template>
            </td>
            <td>
                <span class="label label-@{{ state }}"><i class="fa fa-@{{ icon }}"></i> @{{ label }}
            </td>
            <td>
                <div class="btn-group pull-right">
                    <button v-on:click="testServer" type="button" class="btn btn-default btn-test" title="{{ Lang::get('servers.test') }}" v-bind:disabled="isTesting"><i class="fa fa-refresh" v-bind:class="isTesting ? 'fa-spin' : ''"></i></button>
                    <button v-on:click="editServer(server)" type="button" class="btn btn-default btn-edit" title="{{ Lang::get('servers.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#server" v-bind:disabled="isTesting"><i class="fa fa-edit"></i></button>
                </div>
            </td>
        </tr>
    </template>
@endpush
