<div class="modal fade" id="command">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-tasks"></i> Add Command</h4>
            </div>
            <form role="form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="command_name">Name</label>
                        <input type="text" class="form-control" id="command_name" placeholder="Migrations" />
                    </div>
                    <div class="form-group">
                        <label for="command_user">Run As</label>
                        <input type="text" class="form-control" id="command_user" placeholder="deploy" />
                    </div>
                    <div class="form-group">
                        <label for="command_script">Script</label>
                        <textarea id="command_script" class="form-control" placeholder="echo 'Hello world'"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="command_servers">Servers</label>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" /> Web 1 (192.168.0.1)
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" /> Web 2 (192.168.0.2)
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" /> Cron (192.168.0.3)
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" /> DB (192.168.0.4)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-right" data-dismiss="modal"><i class="fa fa-save"></i> Save Command</button>
                </div>
            </form>
        </div>
    </div>
</div>