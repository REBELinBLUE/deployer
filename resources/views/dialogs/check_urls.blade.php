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
                        <label for="title">{{ Lang::get('checkUrls.title') }}</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="{{ Lang::get('checkUrls.title') }}" />
                    </div>
                    <div class="form-group">
                        <label for="url">{{ Lang::get('checkUrls.url') }}</label> 
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('checkUrls.urlExample') }}"></i>

                        <input type="text" class="form-control" id="url" name="url" placeholder="" />
                    </div>
                    <div class="form-group">
                        <label for="period">{{ Lang::get('checkUrls.period') }}</label>
                        <select name="period" id="period" class="form-control">
                            <option value="5"> 5 {{ Lang::get('checkUrls.length') }}</option>
                            <option value="15"> 15 {{ Lang::get('checkUrls.length') }}</option>
                            <option value="30"> 30 {{ Lang::get('checkUrls.length') }}</option>
                            <option value="60"> 60 {{ Lang::get('checkUrls.length') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_report">
                            {{ Lang::get('checkUrls.is_report') }}
                            <br>
                            <input type="checkbox" id="is_report" name="is_report" value="{{ \App\CheckUrl::REPORT }}" />
                        </label>
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
