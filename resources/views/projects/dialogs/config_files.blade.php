<div class="modal fade" id="configfile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-file-code-o"></i> <span>{{ Lang::get('configFiles.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="config_file_id" name="id" />
                <input type="hidden" name="target_type" value="{{ $target_type }}" />
                <input type="hidden" name="target_id" value="{{ $target_id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('configFiles.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="config-file-name">{{ Lang::get('configFiles.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" id="config-file-name" name="config-file-name" placeholder="{{ Lang::get('configFiles.config') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="config-file-path">{{ Lang::get('configFiles.path') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-file-text-o"></i></div>
                            <input type="text" class="form-control" id="config-file-path" name="path" placeholder="config/app.php" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="config-file-content">{{ Lang::get('configFiles.content') }}</label>
                        <div id="config-file-content" class="form-control"></div>
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

<div class="modal fade" id="view-configfile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-eye"></i> <span>{{ Lang::get('configFiles.view') }}</span></h4>
            </div>
            <div class="modal-body" id="preview-content">
            </div>
        </div>
    </div>
</div>
