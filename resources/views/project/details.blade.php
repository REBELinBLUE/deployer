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
                        <li><a href="#">Repository <span class="pull-right">{{ $project->repository }}</span></a></li>
                        <li><a href="#">Branch <span class="pull-right label label-default">{{ $project->branch }}</span></a></li>
                        @if(isset($project->url))
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
                        <li><a href="#">Today <span class="pull-right">x</span></a></li>
                        <li><a href="#">Last Week <span class="pull-right">x</span></a></li>
                        <li><a href="#">Latest Duration <span class="pull-right">{{ count($deployments) == 0 ? 'N/A' : human_readable_duration(10) }} </span></a></li>
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
                        @if(isset($project->build_url))
                        <li><a href="#">Build Status <span class="pull-right"><img src="{{ $project->build_url }}" /></span></a></li>
                        @endif
                        <li><a href="#">Application Status <span class="pull-right text-green">OK</span></a></li>
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
    @include('dialogs.settings')
@stop