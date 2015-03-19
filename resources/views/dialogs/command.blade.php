<div class="modal fade" id="command" data-resource="commands/{{ $action }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-code"></i> <span>Add After Command</span></h4>
            </div>
            <form role="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="command_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <input type="hidden" id="command_step" name="step" value="After" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> The command could not be saved, please check the form below.
                    </div>

                    <div class="form-group">
                        <label for="command_name">Name</label>
                        <input type="text" class="form-control" name="name" id="command_name" placeholder="Migrations" />
                    </div>
                    <div class="form-group">
                        <label for="command_user">Run As</label>
                        <input type="text" class="form-control" name="user" id="command_user" placeholder="deploy" />
                    </div>
                    <div class="form-group">
                        <label for="command_script">Bash Script</label>
                        <textarea rows="10" id="command_script" class="form-control" name="script" placeholder="echo 'Hello world'"></textarea>
                        <h5>You can use the following tokens in your script</h5>
                        <ul>
                            <li><code>@{{ release }}</code> - The release ID, e.g. <span class="label label-default">20150312154523</span></li>
                            <li><code>@{{ release_path }}</code> -The full release path, e.g. <span class="label label-default">/var/www/releases/20150312154523/</span></li>
                        </ul>
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
                    <button type="button" class="btn btn-danger pull-left"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Command</button>
                </div>
            </form>
        </div>
    </div>
</div>