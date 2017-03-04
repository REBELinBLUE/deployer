@if (!count($latest))
    <p>{{ trans('dashboard.no_deployments') }}</p>
@else
<ul class="timeline">
    @foreach ($latest as $date => $deployments)
        <li class="time-label">
            <span class="bg-gray">{{ date('jS M Y', strtotime($date)) }}</span>
        </li>

        @foreach ($deployments as $deployment)
        <li>
            <i class="fa fa-{{ $deployment->icon }} bg-{{ $deployment->timeline_css_class }}"></i>
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> {{ $deployment->started_at->format('g:i:s A') }}</span>
                <h3 class="timeline-header"><a href="{{ route('projects', ['id' => $deployment->project_id]) }}">{{ $deployment->project->name }} </a> - <a href="{{ route('deployments', ['id' => $deployment->id]) }}">{{ trans('dashboard.deployment_number', ['id' => $deployment->id]) }}</a> - {{ $deployment->readable_status }}</h3>

                @if (!empty($deployment->reason))
                <div class="timeline-body">
                     {{ $deployment->reason }}
                </div>
                @endif
            </div>
        </li>
        @endforeach
    @endforeach
    <li>
        <i class="fa fa-clock-o bg-gray"></i>
    </li>
</ul>
@endif
