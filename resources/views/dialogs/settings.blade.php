<div class="modal fade" id="project">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> Project Settings</h4>
            </div>
            <form role="form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="project_name">Name</label>
                        <input type="text" class="form-control" id="project_name" placeholder="My new project" />
                    </div>
                    <div class="form-group">
                        <label for="project_repository">Repository</label>
                        <input type="text" class="form-control" id="project_repository" placeholder="git@locahost:repositories/project.git" />
                    </div>
                    <div class="form-group">
                        <label for="project_branch">Branch</label>
                        <input type="text" class="form-control" id="project_branch" placeholder="master" />
                    </div>
                    <div class="form-group">
                        <label for="project_url">URL</label>
                        <input type="text" class="form-control" id="project_url" placeholder="http://localhost" />
                    </div>
                    <div class="form-group">
                        <label for="project_image">Build Image</label>
                        <input type="text" class="form-control" id="project_image" placeholder="http://ci.myserver.com/status.png" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-primary pull-right" data-dismiss="modal"><i class="fa fa-save"></i> Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>