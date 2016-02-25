<div class="modal modal-default fade" id="redeploy">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cloud-upload"></i> {{ Lang::get('deployments.rollback_title') }}</h4>
            </div>
            <form role="form" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="modal-body">

                    <div class="alert alert-danger">
                        <h4>{{ Lang::get('deployments.caution') }}</h4>
                        <p>{{ Lang::get('deployments.expert') }}</p>
                        <br />
                        <p>{{ Lang::get('deployments.rollback_warning') }}</p>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label for="deployment_reason">{{ Lang::get('deployments.describe_reason') }}</label>
                        <textarea rows="10" id="deployment_reason" class="form-control" name="reason" placeholder="For example, Allows users to reset their password"></textarea>
                    </div>
                    @if (count($optional))
                    <div class="form-group">
                        <label for="command_servers">{{ Lang::get('deployments.optional') }}</label>
                        <ul class="list-unstyled">
                            @foreach ($optional as $command)
                            <li>
                                <div class="checkbox">
                                    <label for="deployment_command_{{ $command->id }}">
                                        <input type="checkbox" class="deployment-command" name="optional[]" id="deployment_command_{{ $command->id }}" value="{{ $command->id }}" @if ($command->default_on === true) checked @endif/> {{ $command->name }}
                                    </label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <div class="callout callout-info">
                            <p>NOTE: The commands which were included in the previous deployment have already been selected</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> {{ Lang::get('projects.redeploy') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
