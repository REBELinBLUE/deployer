<div class="modal fade" id="server">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-tasks"></i> <span>{{ trans('servers.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="server_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <input type="hidden" id="shared_server_id" name="shared_server_id" />
                <div class="modal-body">
                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ trans('servers.warning') }}
                    </div>
                    <div class="nav-tabs-custom">
                        @if ($shared_servers->count() > 0)
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#server_details" data-toggle="tab">{{ trans('servers.server_details') }}</a></li>
                                <li><a href="#shared_servers" data-toggle="tab">{{ trans('servers.shared_servers') }}</a></li>
                            </ul>
                        @endif
                        <div class="tab-content">
                            <div class="tab-pane active" id="server_details">
                                <div class="form-group">
                                    <label for="server_name">{{ trans('servers.name') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                                        <input type="text" class="form-control" id="server_name" name="name" placeholder="{{ trans('servers.web') }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_user">{{ trans('servers.connect_as') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                        <input type="text" class="form-control" id="server_user" name="user" placeholder="deploy" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_address">{{ trans('servers.ip_address') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-laptop"></i></div>
                                        <input type="text" class="form-control" id="server_address" name="ip_address" placeholder="192.168.0.1" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_port">{{ trans('servers.port') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-plug"></i></div>
                                        <input type="number" class="form-control" id="server_port" name="port" placeholder="22" value="22" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_path">{{ trans('servers.path') }}</label>
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ trans('servers.example') }}"></i>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-folder-o"></i></div>
                                        <input type="text" class="form-control" id="server_path" name="path" placeholder="/var/www/project" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('servers.options') }}</label>
                                    <div class="checkbox">
                                        <label for="server_deploy_code">
                                            <input type="checkbox" value="1" name="deploy_code" id="server_deploy_code" />
                                            {{ trans('servers.deploy_code') }}
                                        </label>
                                    </div>
                                    @if ($project->commands->count() > 0)
                                        <div class="checkbox" id="add-server-command">
                                            <label for="server_commands">
                                                <input type="checkbox" value="1" name="commands" id="server_commands" checked />
                                                {{ trans('servers.add_command') }}
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($shared_servers->count() > 0)
                                <div class="tab-pane" id="shared_servers">
                                    <table class="table table-condensed table-stripped">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('servers.name') }}</th>
                                            <th>{{ trans('servers.connect_as') }}</th>
                                            <th>{{ trans('servers.ip_address') }}</th>
                                            <th>{{ trans('servers.port') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($shared_servers as $server)
                                            <tr>
                                                <td>{{ $server->name }}</td>
                                                <td>{{ empty($server->user) ? trans('servers.unspecified') : $server->user }}</td>
                                                <td>{{ $server->ip_address }}</td>
                                                <td>{{ $server->port }}</td>
                                                <td>
                                                    <button type="button" data-shared-server-id="{{ $server->id }}" class="btn btn-default btn-edit pull-right" title="{{ trans('servers.use_shared') }}"><i class="fa fa-upload"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> {{ trans('app.delete') }}</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ trans('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
