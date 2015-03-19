<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="Add a new server" data-toggle="modal" data-backdrop="static" data-target="#server"><span class="fa fa-plus"></span> Add Server</button>
        </div>
        <h3 class="box-title">Servers</h3>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Connect As</th>
                    <th>IP Address</th>
                    <th>Connection Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servers as $server)
                <tr id="server_{{ $server->id }}">
                    <td>{{ $server->name }}</td>
                    <td>{{ $server->user }}</td>
                    <td>{{ $server->ip_address }}</td>
                    <td>
                        <span class="label label-{{ server_css_status($server) }}"><i class="fa fa-{{ server_icon_status($server) }} "></i> <span>{{ $server->status }}</span></span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default btn-test" data-server-id="{{ $server->id }}" title="Test the server connection" {{ $server->isTesting() ? ' disabled' : '' }}><i class="fa fa-refresh {{ $server->isTesting() ? 'fa-spin' : '' }}"></i></button>
                            <button type="button" class="btn btn-default" title="Edit the server" data-server-id="{{ $server->id }}" data-toggle="modal" data-backdrop="static" data-target="#server"{{ $server->isTesting() ? ' disabled' : '' }}><i class="fa fa-edit"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    var servers = {!! $servers->toJson() !!};
</script>