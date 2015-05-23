<div class="modal fade" id="projectfile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-bullhorn"></i> <span>{{ Lang::get('projectFiles.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="project_file_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('projectFiles.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="project-file-name">{{ Lang::get('projectFiles.name') }}</label>
                        <input type="text" class="form-control" id="project-file-name" name="project-file-name" placeholder="Laravel Storage" />
                    </div>
                    <div class="form-group">
                        <label for="project-file-path">{{ Lang::get('projectFiles.path') }}</label>
                        <input type="text" class="form-control" id="project-file-path" name="path" placeholder="Path like '/app/config/deployer.php'" />
                    </div>
                    <div class="form-group">
                        <label for="project-file-content">{{ Lang::get('projectFiles.content') }}</label>
                        <textarea class="form-control" id="project-file-content" name="content" placeholder="File content may be ini or json or php array"></textarea>
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
<div class="modal fade" id="view-projectfile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-bullhorn"></i> <span>{{ Lang::get('projectFiles.view') }}</span></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>