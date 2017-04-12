<div class="modal fade" id="server_template">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <span>{{ trans('servers.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="server_template_id" name="id" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ trans('servers.warning') }}
                    </div>

                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#server_template_details" data-toggle="tab">{{ trans('servers.server_details') }}</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="server_template_details">
                                <div class="form-group">
                                    <label for="server_template_name">{{ trans('servers.name') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                                        <input type="text" class="form-control" id="server_template_name" name="name" placeholder="{{ trans('servers.web') }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_template_address">{{ trans('servers.ip_address') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-laptop"></i></div>
                                        <input type="text" class="form-control" id="server_template_address" name="ip_address" placeholder="192.168.0.1" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_template_port">{{ trans('servers.port') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-plug"></i></div>
                                        <input type="number" class="form-control" id="server_template_port" name="port" placeholder="22" value="22" />
                                    </div>
                                </div>
                            </div>
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
