@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#hooks" data-toggle="tab"><span class="fa fa-terminal"></span> {{ Lang::get('commands.label') }}</a></li>
                    <li><a href="#shared-files" data-toggle="tab"><span class="fa fa-folder"></span> {{ Lang::get('sharedFiles.label') }}</a></li>
                    <li><a href="#project-files" data-toggle="tab"><span class="fa fa-file-code-o"></span> {{ Lang::get('projectFiles.label') }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="hooks">
                        @include('projects._partials.commands')
                    </div>
                    <div class="tab-pane" id="shared-files">
                        @include('projects._partials.shared_files')
                    </div>
                    <div class="tab-pane" id="project-files">
                        @include('projects._partials.project_files')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dialogs.server')
    @include('dialogs.shared_files')
    @include('dialogs.project_files')
@stop

@section('javascript')
    <script type="text/javascript">
        new app.SharedFilesTab();
        new app.ProjectFilesTab();

        app.SharedFiles.add({!! $sharedFiles->toJson() !!});
        app.ProjectFiles.add({!! $projectFiles->toJson() !!});

        app.project_id = {{ $project->id }};
    </script>
@stop