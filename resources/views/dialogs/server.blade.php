<div class="modal fade" id="server">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-tasks"></i> <span>{{ Lang::get('servers.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="server_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger" v-show="warning">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('servers.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="server_name">{{ Lang::get('servers.name') }}</label>
                        <input type="text" class="form-control" id="server_name" name="name" placeholder="{{ Lang::get('servers.web') }}" v-model="current.name" />
                    </div>
                    <div class="form-group">
                        <label for="server_user">{{ Lang::get('servers.connect_as') }}</label>
                        <input type="text" class="form-control" id="server_user" name="user" placeholder="deploy" v-model="current.user" />
                    </div>
                    <div class="form-group">
                        <label for="server_address">{{ Lang::get('servers.ip_address') }}</label>
                        <input type="text" class="form-control" id="server_address" name="ip_address" placeholder="192.168.0.1" v-model="current.ip_address" />
                    </div>
                    <div class="form-group">
                        <label for="server_port">{{ Lang::get('servers.port') }}</label>
                        <input type="number" class="form-control" id="server_port" name="port" placeholder="22" v-model="current.port" number />
                    </div>
                    <div class="form-group">
                        <label for="server_path">{{ Lang::get('servers.path') }}</label>
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('servers.example') }}"></i>
                        <input type="text" class="form-control" id="server_path" name="path" placeholder="/var/www/project" v-model="current.path" />
                    </div>
                    <div class="form-group">
                        <label>{{ Lang::get('servers.options') }}</label>
                        <div class="checkbox">
                            <label for="server_deploy_code">
                                <input type="checkbox" value="1" name="deploy_code" id="server_deploy_code" v-model="current.deploy_code" />
                                {{ Lang::get('servers.deploy_code') }}
                            </label>
                        </div>
                        @if ($project->commands->count() > 0)
                        <div class="checkbox" id="add-server-command" v-if="is_new">
                            <label for="server_commands">
                                <input type="checkbox" value="1" name="commands" id="server_commands" checked />
                                {{ Lang::get('servers.add_command') }}
                            </label>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete" v-if="!is_new"><i class="fa fa-trash"></i> {{ Lang::get('app.delete') }}</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ Lang::get('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
