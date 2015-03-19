<div class="modal fade" id="server" data-resource="servers">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-tasks"></i> <span>Add a Server</span></h4>
            </div>
            <form role="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="server_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> The server could not be saved, please check the form below.
                    </div>

                    <div class="form-group">
                        <label for="server_name">Name</label>
                        <input type="text" class="form-control" id="server_name" name="name" placeholder="Web Server" />
                    </div>
                    <div class="form-group">
                        <label for="server_user">Connect As</label>
                        <input type="text" class="form-control" id="server_user" name="user" placeholder="deploy" />
                    </div>
                    <div class="form-group">
                        <label for="server_address">IP Address</label>
                        <input type="text" class="form-control" id="server_address" name="ip_address" placeholder="192.168.0.1" />
                    </div>
                    <div class="form-group">
                        <label for="server_path">Project Path</label>
                        <input type="text" class="form-control" id="server_path" name="path" placeholder="/var/www/project" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Server</button>
                </div>
            </form>
        </div>
    </div>
</div>