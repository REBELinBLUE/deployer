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
                        <input type="text" class="form-control" id="project_name" value="{{ $project->name }}" placeholder="My awesome webapp" />
                    </div>
                    <div class="form-group">
                        <label for="project_repository">Repository</label>
                        <input type="text" class="form-control" id="project_repository" value="{{ $project->repository }}" placeholder="git@git.example.com:repositories/project.git" />
                    </div>
                    <div class="form-group">
                        <label for="project_branch">Branch</label>
                        <input type="text" class="form-control" id="project_branch" value="{{ $project->branch }}" placeholder="master" />
                    </div>
                    <div class="form-group">
                        <label for="project_builds_to_keep">Builds to Keep</label>
                        <input type="number" class="form-control" min="1" max="20" id="project_builds_to_keep" value="{{ $project->builds_to_keep }}" placeholder="10" />
                    </div>
                    <div class="form-group">
                        <label for="project_url">URL</label>
                        <input type="text" class="form-control" id="project_url" value="{{ $project->url }}"  placeholder="http://www.example.com" />
                    </div>
                    <div class="form-group">
                        <label for="project_build_url">Build Image</label>
                        <input type="text" class="form-control" id="project_build_url" value="{{ $project->build_url }}"  placeholder="http://ci.example.com/status.png?project=1" />
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