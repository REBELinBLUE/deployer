@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Project Details</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="{{ $project->repositoryURL() }}" target="_blank">Repository <span class="pull-right" title="{{ $project->repository }}">{{ $project->repositoryPath() }}</span></a></li>
                        <li><a href="{{ $project->branchURL() }}" target="_blank">Branch <span class="pull-right label label-default">{{ $project->branch }}</span></a></li>
                        @if(!empty($project->url))
                        <li><a href="{{ $project->url }}" target="_blank">URL <span class="pull-right text-blue">{{ $project->url }}</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Deployments</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="#">Today <span class="pull-right">{{ number_format($today) }}</span></a></li>
                        <li><a href="#">Last Week <span class="pull-right">{{ number_format($last_week) }}</span></a></li>
                        <li><a href="#">Latest Duration <span class="pull-right">{{ (count($deployments) == 0 OR !$deployments[0]->finished_at) ? 'N/A' : human_readable_duration($deployments[0]->runtime()) }} </span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Health Check</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        @if(!empty($project->build_url))
                        <li><a href="#">Build Status <span class="pull-right"><img src="{{ $project->build_url }}" /></span></a></li>
                        @endif
                        <li><a href="#">Application Status <span class="pull-right text-green">????</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#deployments" data-toggle="tab"><span class="fa fa-hdd-o"></span> Deployments</a></li>
                    <li><a href="#servers" data-toggle="tab"><span class="fa fa-tasks"></span> Servers</a></li>
                    <li><a href="#hooks" data-toggle="tab"><span class="fa fa-terminal"></span> Commands</a></li>
                    <li><a href="#notifications" data-toggle="tab"><span class="fa fa-bullhorn"></span> Notifications</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="deployments">
                        @include('project._partials.deployments')
                    </div>
                    <div class="tab-pane" id="servers">
                        @include('project._partials.servers')
                    </div>
                    <div class="tab-pane" id="hooks">
                        @include('project._partials.commands')
                    </div>
                    <div class="tab-pane" id="notifications">
                        @include('project._partials.notifications')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dialogs.server')
    @include('dialogs.key')
    @include('dialogs.channel')
    @include('dialogs.project')
@stop

@section('javascript')
    <script type="text/javascript">
        var projects = [{!! $project->toJson() !!}];
        
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
            <button type="button" class="btn btn-default" title="View the public SSH sey" data-toggle="modal" data-target="#key"><span class="fa fa-key"></span> SSH key</button>
            <button type="button" class="btn btn-default" title="Edit the project settings" data-project-id="{{ $project->id }}" data-toggle="modal" data-target="#project"><span class="fa fa-cogs"></span> Settings</button>
            <button type="submit" class="btn btn-danger" title="Deploy the project" {{ ($project->isDeploying() OR !count($project->servers)) ? 'disabled' : '' }}><span class="fa fa-cloud-upload"></i> Deploy</button>
        </form>
    </div>
@stop
