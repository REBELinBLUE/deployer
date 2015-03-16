<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="Add a new server" data-toggle="modal" data-target="#server"><span class="fa fa-plus"></span> Add Server</button>
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
                <tr>
                    <td>Web1</td>
                    <td>root</td>
                    <td>192.168.0.1</td>
                    <td>
                        <span class="label label-success"><i class="fa fa-check"></i> Successful</span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default" title="Test Connection"><i class="fa fa-refresh"></i></button>
                            <button type="button" class="btn btn-default" title="Edit" data-toggle="modal" data-target="#server"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-default" title="Public Key" data-toggle="modal" data-target="#key"><i class="fa fa-key"></i></button>
                            <button type="button" class="btn btn-default" title="Remove"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Web2</td>
                    <td>root</td>
                    <td>192.168.0.2</td>
                    <td>
                        <span class="label label-primary"><i class="fa fa-question"></i> Unknown</span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default" title="Test Connection"><i class="fa fa-refresh"></i></button>
                            <button type="button" class="btn btn-default" title="Edit" data-toggle="modal" data-target="#server"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-default" title="Public Key" data-toggle="modal" data-target="#key"><i class="fa fa-key"></i></button>
                            <button type="button" class="btn btn-default" title="Remove"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Cron</td>
                    <td>root</td>
                    <td>192.168.0.3</td>
                    <td>
                        <span class="label label-warning"><i class="fa fa-spinner"></i> Testing...</span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default" title="Test Connection"><i class="fa fa-refresh"></i></button>
                            <button type="button" class="btn btn-default" title="Edit" data-toggle="modal" data-target="#server"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-default" title="Public Key" data-toggle="modal" data-target="#key"><i class="fa fa-key"></i></button>
                            <button type="button" class="btn btn-default" title="Remove"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>DB</td>
                    <td>root</td>
                    <td>192.168.0.4</td>
                    <td>
                        <span class="label label-danger"><i class="fa fa-warning"></i> Failed</span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default" title="Test Connection"><i class="fa fa-refresh"></i></button>
                            <button type="button" class="btn btn-default" title="Edit" data-toggle="modal" data-target="#server"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-default" title="Public Key" data-toggle="modal" data-target="#key"><i class="fa fa-key"></i></button>
                            <button type="button" class="btn btn-default" title="Remove"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>