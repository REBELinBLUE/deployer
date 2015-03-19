<div class="modal fade" id="command" data-resource="commands/{{ $command }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-code"></i> Add Command</h4>
            </div>
            <form role="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="command_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <input type="hidden" name="step" value="After" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="command_name">Name</label>
                        <input type="text" class="form-control" name="name" id="command_name" placeholder="Migrations" />
                    </div>
                    <div class="form-group">
                        <label for="command_user">Run As</label>
                        <input type="text" class="form-control" name="user" id="command_user" placeholder="deploy" />
                    </div>
                    <div class="form-group">
                        <label for="command_script">Script</label>
                        <textarea rows="10" id="command_script" class="form-control" name="script" placeholder="echo 'Hello world'"></textarea>
                        <h5>You can use the following tokens in your script</h5>
                        <ul>
                            <li><code>@{{ release }}</code> - Contains the release ID, e.g. <span class="label label-default">20150312154523</span></li>
                            <li><code>@{{ release_path }}</code> - Contains the full path to the release, e.g. <span class="label label-default">/var/www/releases/20150312154523/</span></li>
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
                    <button type="button" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Command</button>
                </div>
            </form>
        </div>
    </div>
</div>