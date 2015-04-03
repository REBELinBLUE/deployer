@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ Lang::get('projects.details') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="{{ $project->repositoryURL() }}" target="_blank">{{ Lang::get('projects.repository') }} <span class="pull-right" title="{{ $project->repository }}">{{ $project->repositoryPath() }}</span></a></li>
                        <li><a href="{{ $project->branchURL() }}" target="_blank">{{ Lang::get('projects.branch') }} <span class="pull-right label label-default">{{ $project->branch }}</span></a></li>
                        @if(!empty($project->url))
                        <li><a href="{{ $project->url }}" target="_blank">{{ Lang::get('projects.url') }} <span class="pull-right text-blue">{{ $project->url }}</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ Lang::get('projects.deployments') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="#">{{ Lang::get('projects.today') }} <span class="pull-right">{{ number_format($today) }}</span></a></li>
                        <li><a href="#">{{ Lang::get('projects.last_week') }} <span class="pull-right">{{ number_format($last_week) }}</span></a></li>
                        <li><a href="#">{{ Lang::get('projects.latest_duration') }}<span class="pull-right">{{ (count($deployments) == 0 OR !$deployments[0]->finished_at) ? Lang::get('app.not_applicable') : human_readable_duration($deployments[0]->runtime()) }} </span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ Lang::get('projects.health') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        @if(!empty($project->build_url))
                        <li><a href="#">{{ Lang::get('projects.build_status') }} <span class="pull-right"><img src="{{ $project->build_url }}" /></span></a></li>
                        @endif
                        <li><a href="#">{{ Lang::get('projects.app_status') }} <span class="pull-right text-green">????</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#deployments" data-toggle="tab"><span class="fa fa-hdd-o"></span> {{ Lang::get('deployments.label') }}</a></li>
                    <li><a href="#servers" data-toggle="tab"><span class="fa fa-tasks"></span> {{ Lang::get('servers.label') }}</a></li>
                    <li><a href="#hooks" data-toggle="tab"><span class="fa fa-terminal"></span> {{ Lang::get('commands.label') }}</a></li>
                    <li><a href="#notifications" data-toggle="tab"><span class="fa fa-bullhorn"></span> {{ Lang::get('notifications.label') }}</a></li>
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
                    </div>
                    <div class="tab-pane" id="notifications">
                        @include('projects._partials.notifications')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dialogs.server')
    @include('dialogs.key')
    @include('dialogs.channel')
@stop

@section('javascript')
    <script type="text/javascript">
        new app.ServersTab();
        new app.NotificationsTab();

        app.Servers.add({!! $servers->toJson() !!});
        app.Notifications.add({!! $notifications->toJson() !!});
    </script>
@stop

@section('right-buttons')
    <div class="pull-right">
        <form method="post" action="{{ route('deploy', ['id' => $project->id]) }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <button type="button" class="btn btn-default" title="{{ Lang::get('projects.view_ssh_key') }}" data-toggle="modal" data-target="#key"><span class="fa fa-key"></span> {{ Lang::get('projects.ssh_key') }}</button>
            <button type="submit" class="btn btn-danger" title="{{ Lang::get('projects.deploy_project') }}" {{ ($project->isDeploying() OR !count($project->servers)) ? 'disabled' : '' }}><span class="fa fa-cloud-upload"></i> {{ Lang::get('projects.deploy') }}</button>
        </form>
    </div>
@stop
