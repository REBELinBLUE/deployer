<div class="modal fade" id="heartbeat">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-tasks"></i> <span>{{ Lang::get('heartbeats.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="heartbeat_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('heartbeat.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="heartbeat_name">{{ Lang::get('heartbeats.name') }}</label>
                        <input type="text" class="form-control" id="heartbeat_name" name="name" placeholder="My Cronjob" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> {{ Lang::get('app.delete') }}</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ Lang::get('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>