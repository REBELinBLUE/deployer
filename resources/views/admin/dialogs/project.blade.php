<div class="modal fade" id="project">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <span>{{ Lang::get('projects.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="project_id" name="id" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('projects.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="project_name">{{ Lang::get('projects.name') }}</label>
                        <input type="text" class="form-control" name="name" id="project_name" placeholder="{{ Lang::get('projects.awesome') }}" />
                    </div>
                    <div class="form-group">
                        <label for="project_group_id">{{ Lang::get('projects.group') }}</label>
                        <select id="project_group_id" name="group_id" class="form-control">
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (count($templates) > 0)
                    <div class="form-group" id="template-list">
                        <label for="project_template_id">{{ Lang::get('templates.type') }}</label>
                        <select id="project_template_id" name="template_id" class="form-control">
                            <option value="">{{ Lang::get('templates.custom') }}</option>
                            @foreach ($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="project_repository">{{ Lang::get('projects.repository') }}</label>
                        <input type="text" class="form-control" name="repository" id="project_repository"  placeholder="git@git.example.com:repositories/project.git" />
                    </div>
                    <div class="form-group">
                        <label for="project_branch">{{ Lang::get('projects.branch') }}</label>
                        <input type="text" class="form-control" name="branch" id="project_branch"  placeholder="master" />
                    </div>
                    <div class="form-group">
                        <label for="project_builds_to_keep">{{ Lang::get('projects.builds') }}</label>
                        <input type="number" class="form-control" name="builds_to_keep" min="1" max="20" id="project_builds_to_keep" placeholder="10" />
                    </div>
                    <div class="form-group">
                        <label for="project_url">{{ Lang::get('projects.url') }}</label>
                        <input type="text" class="form-control" name="url" id="project_url" placeholder="http://www.example.com" />
                    </div>
                    <div class="form-group">
                        <label for="project_build_url">{{ Lang::get('projects.image') }}</label>
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('projects.ci_image') }}"></i>
                        <input type="text" class="form-control" name="build_url" id="project_build_url" placeholder="http://ci.example.com/status.png?project=1" />
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