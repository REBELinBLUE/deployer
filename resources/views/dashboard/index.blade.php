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
                                        <a href="{{ $project->url }}" class="btn btn-default" title="View site" target="_blank"><i class="fa fa-globe"></i></a>
                                        <a href="{{ route('project', ['id' => $project->id]) }}" class="btn btn-default" title="View Details"><i class="fa fa-info-circle"></i></a>
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
                    <ul class="timeline">
                        <li class="time-label">
                            <span class="bg-gray">10th Feb 2014</span>
                        </li>
                        <li>
                            <i class="fa fa-question bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>
                                <h3 class="timeline-header"><a href="#">Project</a> - <a href="#">Deployment #</a> pending</h3>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-spinner bg-yellow"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>
                                <h3 class="timeline-header"><a href="#">Project</a> - <a href="#">Deployment #</a> running</h3>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-warning bg-red"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>
                                <h3 class="timeline-header"><a href="#">Project</a> - <a href="#">Deployment #</a> failed</h3>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-check bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>
                                <h3 class="timeline-header"><a href="#">Project</a> - <a href="#">Deployment #</a> completed</h3>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-clock-o bg-gray"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@stop