@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('projects.details') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="{{ $project->repository_url }}" target="_blank">{{ trans('projects.repository') }} <span class="pull-right" title="{{ $project->repository }}"><i class="fa {{ $project->type_icon }}"></i> {{ $project->repository_path }}</span></a></li>
                        <li><a href="{{ $project->branch_url }}" target="_blank">{{ trans('projects.branch') }} <span class="pull-right label label-default">{{ $project->branch }}</span></a></li>
                        @if(!empty($project->url))
                        <li><a href="{{ $project->url }}" target="_blank">{{ trans('projects.url') }} <span class="pull-right text-blue">{{ $project->url }}</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('projects.deployments') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="#">{{ trans('projects.today') }} <span class="pull-right">{{ number_format($today) }}</span></a></li>
                        <li><a href="#">{{ trans('projects.last_week') }} <span class="pull-right">{{ number_format($last_week) }}</span></a></li>
                        <li><a href="#">{{ trans('projects.latest_duration') }}<span class="pull-right">{{ (count($deployments) == 0 OR !$deployments[0]->finished_at) ? trans('app.not_applicable') : $deployments[0]->readable_runtime }} </span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('projects.health') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        @if(!empty($project->build_url))
                        <li><a href="#">{{ trans('projects.build_status') }} <span class="pull-right"><img src="{{ $project->build_url }}" /></span></a></li>
                        @endif
                        <li><a href="#">{{ trans('projects.app_status') }} <span class="pull-right label label-{{ $project->app_status_css }}">{{ $project->app_status }}</span></a></li>
                        <li><a href="#">{{ trans('projects.heartbeats_status') }} <span class="pull-right label label-{{ $project->heart_beat_status_css }}">{{ $project->heart_beat_status }}</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row project-status">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#deployments" data-toggle="tab"><span class="fa fa-hdd-o"></span> {{ trans('deployments.label') }}</a></li>
                    <li><a href="#servers" data-toggle="tab"><span class="fa fa-tasks"></span> {{ trans('servers.label') }}</a></li>
                    <li><a href="#hooks" data-toggle="tab"><span class="fa fa-terminal"></span> {{ trans('commands.label') }}</a></li>
                    <li><a href="#files" data-toggle="tab"><span class="fa fa-file-code-o"></span> {{ trans('sharedFiles.tab_label') }}</a></li>
                    <li><a href="#notifications" data-toggle="tab"><span class="fa fa-bullhorn"></span> {{ trans('channels.label') }}</a></li>
                    <li><a href="#health" data-toggle="tab"><span class="fa fa-heartbeat"></span> {{ trans('heartbeats.tab_label') }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="deployments">
                        @include('projects._partials.deployments')
                    </div>
                    <div class="tab-pane" id="servers">
                        @include('projects._partials.servers')
                    </div>
                    <div class="tab-pane" id="hooks">
                        @include('projects._partials.commands')
                        @include('projects._partials.variables')
                    </div>
                    <div class="tab-pane" id="files">
                        @include('projects._partials.shared_files')
                        @include('projects._partials.config_files')
                    </div>
                    <div class="tab-pane" id="notifications">
                        @include('projects._partials.notifications')
                    </div>
                    <div class="tab-pane" id="health">
                        @include('projects._partials.heartbeats')
                        @include('projects._partials.check_urls')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('projects.dialogs.server')
    @include('projects.dialogs.shared_files')
    @include('projects.dialogs.config_files')
    @include('projects.dialogs.channel')
    @include('projects.dialogs.webhook')
    @include('projects.dialogs.variable')
    @include('projects.dialogs.heartbeat')
    @include('projects.dialogs.check_urls')
    @include('projects.dialogs.key')
    @include('projects.dialogs.reason')
    @include('projects.dialogs.redeploy')
    @include('projects.dialogs.log')
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="{{ trans('projects.view_ssh_key') }}" data-toggle="modal" data-target="#key"><span class="fa fa-key"></span> {{ trans('projects.ssh_key') }}</button>
        <button id="deploy_project" data-toggle="modal" data-backdrop="static" data-target="#reason" type="button" class="btn btn-danger" title="{{ trans('projects.deploy_project') }}" {{ ($project->isDeploying() OR !count($project->servers)) ? 'disabled' : '' }}><span class="fa fa-cloud-upload"></span> {{ trans('projects.deploy') }}</button>
    </div>
@stop

@push('javascript')
    <script type="text/javascript">
        app.views.Project();

        new app.views.Servers();
        new app.views.Variables();
        new app.views.SharedFiles();
        new app.views.ConfigFiles();
        new app.views.Notifications();
        new app.views.Heartbeats();
        new app.views.CheckUrls();

        app.collections.Servers.add({!! $servers->toJson() !!});
        app.collections.Variables.add({!! $variables->toJson() !!});
        app.collections.SharedFiles.add({!! $sharedFiles->toJson() !!});
        app.collections.ConfigFiles.add({!! $configFiles->toJson() !!});
        app.collections.Notifications.add({!! $channels->toJson() !!});
        app.collections.Heartbeats.add({!! $heartbeats->toJson() !!});
        app.collections.CheckUrls.add({!! $checkUrls->toJson() !!});

        app.setProjectId({{ $project->id }});
    </script>
@endpush
