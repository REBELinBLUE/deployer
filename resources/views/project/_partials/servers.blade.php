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
                <tr>
                    <td>{{ $server->name }}</td>
                    <td>{{ $server->user }}</td>
                    <td>{{ $server->ip_address }}</td>
                    <td>
                        <span class="label label-{{ server_css_status($server) }}"><i class="fa fa-{{ server_icon_status($server) }} "></i> {{ $server->status }}</span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default" title="Test Connection"><i class="fa fa-refresh"></i></button>
                            <button type="button" class="btn btn-default" title="Edit" data-server-id="{{ $server->id }}" data-toggle="modal" data-backdrop="static" data-target="#server"><i class="fa fa-edit"></i></button>
                            <!--button type="button" class="btn btn-default" title="Public Key" data-server-id="{{ $server->id }}" data-toggle="modal" data-target="#key"><i class="fa fa-key"></i></button-->
                            <button type="button" class="btn btn-default btn-delete" title="Remove"><i class="fa fa-trash"></i></button>
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