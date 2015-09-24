<div class="modal fade" id="reason">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-comment-o"></i> {{ Lang::get('deployments.reason') }}</h4>
            </div>
            <form role="form" method="post" action="{{ route('deploy', ['id' => $project->id]) }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('deployments.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="deployment_source">{{ Lang::get('deployments.source') }}</label>
                        <ul class="list-unstyled">
                            <li>
                                <div class="radio">
                                    <label for="deployment_source_default">
                                        <input type="radio" class="deployment-source" name="source" id="deployment_source_default" value="{{ $project->branch }}" checked /> {{ Lang::get('deployments.default', [ 'branch' => $project->branch ]) }}
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="radio">
                                    <label for="deployment_source_branch">
                                        <input type="radio" class="deployment-source" name="source" id="deployment_source_branch" value="branch" /> {{ Lang::get('deployments.branch') }}
                                        <input type="text" class="form-control deployment-source" name="source_branch" id="deployment_branch" placeholder="master" />
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="radio">
                                    <label for="deployment_source_tag">
                                        <input type="radio" class="deployment-source" name="source" id="deployment_source_tag" value="tag" /> {{ Lang::get('deployments.tag') }}
                                        <input type="text" class="form-control deployment-source" name="source_tag" id="deployment_tag" placeholder="1.0.0" />
                                    </label>
                                </div>
                            </li>
                        </ul>
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
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ Lang::get('projects.deploy') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
