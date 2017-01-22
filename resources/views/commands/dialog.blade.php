<div class="modal fade" id="command">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-code"></i> <span>{{ Lang::get('commands.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="command_id" name="id" />
                <input type="hidden" name="target_type" value="{{ $target_type }}" />
                <input type="hidden" name="target_id" value="{{ $target_id }}" />
                <input type="hidden" id="command_step" name="step" value="After" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('commands.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="command_name">{{ Lang::get('commands.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" name="name" id="command_name" placeholder="{{ Lang::get('commands.migrations') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="command_user">{{ Lang::get('commands.run_as') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                            <input type="text" class="form-control" name="user" id="command_user" placeholder="{{ Lang::get('commands.default') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="command_script">{{ Lang::get('commands.bash') }}</label>
                        <div id="command_script" class="form-control"></div>
                        <h5><a data-toggle="collapse" data-parent="#accordion" href="#tokens">{{ Lang::get('commands.options') }}</a></h5>

                        <div class="panel-collapse collapse" id="tokens">
                            <ul class="list-unstyled">
                                <li><code>@{{ project_path }}</code> - {{ Lang::get('commands.project_path') }}, {{ Lang::get('commands.example') }} <span class="label label-default">/var/www</span></li>
                                <li><code>@{{ release }}</code> - {{ Lang::get('commands.release_id') }}, {{ Lang::get('commands.example') }} <span class="label label-default">{{ date('YmdHis') }}</span></li>
                                <li><code>@{{ release_path }}</code> - {{ Lang::get('commands.release_path') }}, {{ Lang::get('commands.example') }} <span class="label label-default">/var/www/releases/{{ date('YmdHis') }}</span></li>
                                <li><code>@{{ branch }}</code> - {{ Lang::get('commands.branch') }}, {{ Lang::get('commands.example') }} <span class="label label-default">master</span></li>
                                <li><code>@{{ sha }}</code> - {{ Lang::get('commands.sha') }}, {{ Lang::get('commands.example') }} <span class="label label-default">1def37e6f6fd15c50efe53e090308861ec8a8288</span></li>
                                <li><code>@{{ short_sha }}</code> - {{ Lang::get('commands.short_sha') }}, {{ Lang::get('commands.example') }} <span class="label label-default">1def37e</span></li>
                                <li><code>@{{ deployer_email }}</code> - {{ Lang::get('commands.deployer_email') }}, {{ Lang::get('commands.example') }} <span class="label label-default">{{ $logged_in_user->email }}</span></li>
                                <li><code>@{{ deployer_name }}</code> - {{ Lang::get('commands.deployer_name') }}, {{ Lang::get('commands.example') }} <span class="label label-default">{{ $logged_in_user->name }}</span></li>
                                <li><code>@{{ committer_email }}</code> - {{ Lang::get('commands.committer_email') }}, {{ Lang::get('commands.example') }} <span class="label label-default">joe.bloggs@example.com</span></li>
                                <li><code>@{{ committer_name }}</code> - {{ Lang::get('commands.committer_name') }}, {{ Lang::get('commands.example') }} <span class="label label-default">Joe Bloggs</span></li>
                            </ul>
                        </div>
                    </div>
                    @if (count($project->servers))
                    <div class="form-group">
                        <label for="command_servers">{{ Lang::get('commands.servers') }}</label>
                        <ul class="list-unstyled">
                            @foreach ($project->servers as $server)
                            <li>
                                <div class="checkbox">
                                    <label for="command_server_{{ $server->id }}">
                                        <input type="checkbox" class="command-server" name="servers[]" id="command_server_{{ $server->id }}" value="{{ $server->id }}" /> {{ $server->name }} ({{ $server->user }}&commat;{{ $server->ip_address }})
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

                        <div class="checkbox hide" id="command_default_on_row">
                            <label for="command_default_on">
                                <input type="checkbox" value="1" name="default_on" id="command_default_on" />
                                {{ Lang::get('commands.default_description') }}
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
