<div class="modal fade" id="checkurl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-folder"></i> <span>{{ Lang::get('checkUrls.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="url_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('checkUrls.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="name">{{ Lang::get('checkUrls.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ Lang::get('checkUrls.cache') }}" />
                    </div>
                    <div class="form-group">
                        <label for="file">{{ Lang::get('checkUrls.file') }}</label> 
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('checkUrls.example') }}"></i>

                        <input type="text" class="form-control" id="file" name="file" placeholder="" />
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
