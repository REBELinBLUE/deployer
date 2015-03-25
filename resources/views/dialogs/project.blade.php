<div class="modal fade" id="project" data-resource="projects">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <span>Add a new project</span></h4>
            </div>
            <form role="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="project_id" name="id" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> The project could not be saved, please check the form below.
                    </div>

                    <div class="form-group">
                        <label for="project_name">Name</label>
                        <input type="text" class="form-control" name="name" id="project_name" placeholder="My awesome webapp" />
                    </div>
                    <div class="form-group">
                        <label for="project_repository">Repository</label>
                        <input type="text" class="form-control" name="repository" id="project_repository"  placeholder="git@git.example.com:repositories/project.git" />
                    </div>
                    <div class="form-group">
                        <label for="project_branch">Branch</label>
                        <input type="text" class="form-control" name="branch" id="project_branch"  placeholder="master" />
                    </div>
                    <div class="form-group">
                        <label for="project_builds_to_keep">Builds to Keep</label>
                        <input type="number" class="form-control" name="builds_to_keep" min="1" max="20" id="project_builds_to_keep" placeholder="10" />
                    </div>
                    <div class="form-group">
                        <label for="project_url">URL</label>
                        <input type="text" class="form-control" name="url" id="project_url" placeholder="http://www.example.com" />
                    </div>
                    <div class="form-group">
                        <label for="project_build_url">Build Image</label>
                        <input type="text" class="form-control" name="build_url" id="project_build_url" placeholder="http://ci.example.com/status.png?project=1" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>