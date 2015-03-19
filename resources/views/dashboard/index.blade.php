@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Projects</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Repository</th>
                                <th>Latest Deployment</th>
                                <th>Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                            <tr id="project_{{ $project->id }}">
                                <td><a href="{{ route('project', ['id' => $project->id]) }}" title="View Details">{{ $project->name }}</a></td>
                                <td>{{ $project->repository }}</td>
                                <td>{{ $project->last_run ? $project->last_run->format('jS F Y g:i:s A') : 'Never' }}</td>
                                <td>
                                    <span class="label label-{{ project_css_status($project) }}"><i class="fa fa-{{ project_icon_status($project) }}"></i> {{ $project->status }}</span>
                                </td>
                                <td>
                                    <div class="btn-group pull-right">
                                        <a href="{{ $project->url }}" class="btn btn-default" title="View the site" target="_blank"><i class="fa fa-globe"></i></a>
                                        <a href="{{ route('project', ['id' => $project->id]) }}" class="btn btn-default" title="View the deployment details"><i class="fa fa-info-circle"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-5 pull-left">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Latest Deployments</h3>
                </div>
                <div class="box-body">
                    @if (!count($latest))
                        <p>There have not been any deployments yet.</p>
                    @else
                    <ul class="timeline">
                        @foreach ($latest as $date => $deployments)
                            <li class="time-label">
                                <span class="bg-gray">{{ date('jS M Y', strtotime($date)) }}</span>
                            </li>

                            @foreach ($deployments as $deployment)
                            <li>
                                <i class="fa fa-{{ deployment_icon_status($deployment, false) }} bg-{{ timeline_css_status($deployment) }}"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{ $deployment->started_at->format('H:i') }}</span>
                                    <h3 class="timeline-header"><a href="{{ route('project', $deployment->project_id) }}">{{ $deployment->project->name }} </a> - <a href="{{ route('deployment', $deployment->id) }}">Deployment #{{ $deployment->id }}</a> {{ $deployment->status }}</h3>
                                </div>
                            </li>
                            @endforeach
                        @endforeach
                        <li>
                            <i class="fa fa-clock-o bg-gray"></i>
                        </li>
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop