@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-7">

            @if (!count($projects))
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('dashboard.projects') }}</h3>
                    </div>
                    <div class="box-body">
                        <p>{{ trans('dashboard.no_projects') }}</p>
                    </div>
                </div>
            @else
                @foreach ($projects as $group => $group_projects)
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{ $group }}</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('projects.name') }}</th>
                                    <th>{{ trans_choice('dashboard.latest', 1) }}</th>
                                    <th>{{ trans('dashboard.status') }}</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group_projects as $group_project)
                                <tr id="project_{{ $group_project->id }}">
                                    <td><a href="{{ route('projects', ['id' => $group_project->id]) }}" title="View Details">{{ $group_project->name }}</a></td>
                                    <td>{{ $group_project->last_run ? $group_project->last_run->format('jS F Y g:i:s A') : 'Never' }}</td>
                                    <td>
                                        <span class="label label-{{ $group_project->css_class }}"><i class="fa fa-{{ $group_project->icon }}"></i> <span>{{ $group_project->readable_status }}</span></span>
                                    </td>
                                    <td>
                                        <div class="btn-group pull-right">
                                            @if(isset($project->url))
                                            <a href="{{ $group_project->url }}" class="btn btn-default" title="{{ trans('dashboard.site') }}" target="_blank"><i class="fa fa-globe"></i></a>
                                            @endif
                                            <a href="{{ route('projects', ['id' => $group_project->id]) }}" class="btn btn-default" title="{{ trans('dashboard.view') }}"><i class="fa fa-info-circle"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <div class="col-md-5">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ trans_choice('dashboard.latest', 2) }}</h3>
                </div>
                <div class="box-body" id="timeline">
                    @include('dashboard.timeline')
                </div>
            </div>
        </div>
    </div>
@stop

@push('javascript')
    <script type="text/javascript">
        app.views.Dashboard();
    </script>
@endpush
