<div class="modal fade" id="sharedfile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-folder"></i> <span>{{ trans('sharedFiles.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="sharedfile_id" name="id" />
                <input type="hidden" name="target_type" value="{{ $target_type }}" />
                <input type="hidden" name="target_id" value="{{ $target_id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ trans('sharedFiles.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="name">{{ trans('sharedFiles.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" id="sharedfile_name" name="name" placeholder="{{ trans('sharedFiles.cache') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file">{{ trans('sharedFiles.file') }}</label>
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ trans('sharedFiles.example') }}"></i>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-files-o"></i></div>
                            <input type="text" class="form-control" id="sharedfile_file" name="file" placeholder="storage/" />
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
