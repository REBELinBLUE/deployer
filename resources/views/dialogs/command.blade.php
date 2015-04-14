<div class="modal fade" id="command" data-resource="commands" data-action="{{ $action }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-code"></i> <span>{{ Lang::get('commands.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="command_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <input type="hidden" id="command_step" name="step" value="After" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('commands.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="command_name">{{ Lang::get('commands.name') }}</label>
                        <input type="text" class="form-control" name="name" id="command_name" placeholder="Migrations" />
                    </div>
                    <div class="form-group">
                        <label for="command_user">{{ Lang::get('commands.run_as') }}</label>
                        <input type="text" class="form-control" name="user" id="command_user" placeholder="deploy" />
                    </div>
                    <div class="form-group">
                        <label for="command_script">{{ Lang::get('commands.bash') }}</label>
                        <textarea rows="10" id="command_script" class="form-control" name="script" placeholder="echo 'Hello world'"></textarea>
                        <h5>{{ Lang::get('commands.options') }}</h5>
                        <ul class="list-unstyled">
                            <li><code>@{{ release }}</code> - {{ Lang::get('commands.release_id') }}, e.g. <span class="label label-default">{{ date('YmdHis') }}</span></li>
                            <li><code>@{{ release_path }}</code> - {{ Lang::get('commands.release_path') }}, e.g. <span class="label label-default">/var/www/releases/{{ date('YmdHis') }}/</span></li>
                        </ul>
                    </div>
                    @if (count($project->servers))
                    <div class="form-group">
                        <label for="command_servers">{{ Lang::get('commands.servers') }}</label>
                        <ul class="list-unstyled">
                            @foreach ($project->servers as $server)
                            <li>
                                <div class="checkbox">
                                    <label for="command_server_{{ $server->id }}">
                                        <input type="checkbox" class="command-server" name="servers[]" id="command_server_{{ $server->id }}" value="{{ $server->id }}" /> {{ $server->name }} ({{ $server->ip_address }})
                                    </label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>{{ Lang::get('commands.optional') }}</label>
                        <div class="checkbox">
                            <label for="command_optional">
                                <input type="checkbox" value="1" name="optional" id="command_optional" /> 
                                {{ Lang::get('commands.optional_description') }}
                            </label>
                        </div>
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