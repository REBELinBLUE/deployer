@extends('layout')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Projects</h3>
        </div><!-- /.box-header -->
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
                                <a href="{{ route('project', ['id' => $project->id]) }}" class="btn btn-default" title="View Details"><i class="fa fa-info-circle"></i></a> <!-- details -->
                            </div><!-- /.btn-group -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop