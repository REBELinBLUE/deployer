<div class="modal fade" id="server">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-tasks"></i> <span>Add Server</span></h4>
            </div>
            <form role="form" class="box">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="server_name">Name</label>
                        <input type="text" class="form-control" id="server_name" placeholder="Web server" />
                    </div>
                    <div class="form-group">
                        <label for="server_user">Connect As</label>
                        <input type="text" class="form-control" id="server_user" placeholder="deploy" />
                    </div>
                    <div class="form-group">
                        <label for="server_address">IP Address</label>
                        <input type="text" class="form-control" id="server_address" placeholder="192.168.0.1" />
                    </div>
                    <div class="form-group">
                        <label for="server_path">Project Path</label>
                        <input type="text" class="form-control" id="server_path" placeholder="/var/www/project" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Server</button>
                </div>
                <div class="overlay" style="display: none">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </form>
        </div>

    </div>

</div>