<div class="modal fade" id="shared_server">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <span>{{ trans('servers.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="shared_server_id" name="id" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ trans('servers.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="server_name">{{ trans('servers.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" id="shared_server_name" name="name" placeholder="{{ trans('servers.web') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="server_user">{{ trans('servers.connect_as') }}</label>
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ trans('servers.template_user') }}"></i>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                            <input type="text" class="form-control" id="shared_server_user" name="user" placeholder="deploy"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="server_address">{{ trans('servers.ip_address') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-laptop"></i></div>
                            <input type="text" class="form-control" id="shared_server_address" name="ip_address" placeholder="192.168.0.1" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="server_port">{{ trans('servers.port') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-plug"></i></div>
                            <input type="number" class="form-control" id="shared_server_port" name="port" placeholder="22" value="22" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="server_path">{{ trans('servers.path') }}</label>
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ trans('servers.template_path') }}"></i>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-folder-o"></i></div>
                            <input type="text" class="form-control" id="shared_server_path" name="path" placeholder="/var/www/"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ trans('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
