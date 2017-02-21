<div class="modal fade" id="sharefile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-folder"></i> <span>{{ Lang::get('sharedFiles.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="file_id" name="id" />
                <input type="hidden" name="target_type" value="{{ $target_type }}" />
                <input type="hidden" name="target_id" value="{{ $target_id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('sharedFiles.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="name">{{ Lang::get('sharedFiles.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ Lang::get('sharedFiles.cache') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file">{{ Lang::get('sharedFiles.file') }}</label>
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('sharedFiles.example') }}"></i>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-files-o"></i></div>
                            <input type="text" class="form-control" id="file" name="file" placeholder="storage/" />
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
