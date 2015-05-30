<Projects>

    @foreach ($projects as $project)

        @if (!is_null($project->latest_deployment)) 
            <Project name="{{ Lang::get('app.name') }}: {{ $project->group->name }} / {{ $project->name }}" activity="{{ $project->cc_tray_status }}" lastBuildLabel="{{ $project->latest_deployment->id }}" lastBuildStatus="{{ $project->latest_deployment->cc_tray_status }}" lastBuildTime="{{ $project->last_run->toW3cString() }}" webUrl="{{ url('deployment', $project->latest_deployment->id) }}" />
        @else
            <Project name="{{ Lang::get('app.name') }}: {{ $project->group->name }} / {{ $project->name }}" activity="{{ $project->cc_tray_status }}" lastBuildTime="" webUrl="{{ url('projects', $project->id) }}" />
        @endif

    @endforeach

</Projects>