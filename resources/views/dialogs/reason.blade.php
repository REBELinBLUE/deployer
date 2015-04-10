<div class="modal fade" id="reason">
    <form method="post" action="{{ route('deploy', ['id' => $project->id]) }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><i class="fa fa-code"></i> <span>{{ Lang::get('deployments.reason') }}</span></h4>
                </div>
                <form role="form">
                    <input type="hidden" name="project_id" value="{{ $project->id }}" />
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="deployment_reason">{{ Lang::get('deployments.describe_reason') }}</label>
                            <textarea rows="10" id="deployment_reason" class="form-control" name="reason" placeholder="For example, Allows users to reset their password"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ Lang::get('projects.deploy') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </form>
</div>


