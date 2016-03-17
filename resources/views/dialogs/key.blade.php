<div class="modal fade" id="key">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-key"></i> {{ Lang::get('projects.public_ssh_key') }}</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <p>{!! Lang::get('projects.server_keys') !!}</p>
                    <p>{!! Lang::get('projects.git_keys') !!}</p>
                </div>

                <pre>{{ $project->public_key }}</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ Lang::get('app.close') }}</button>
            </div>
        </div>
    </div>
</div>
