@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- DIRECT CHAT -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Project Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="#">Repository <span class="pull-right">....</span></a></li>
                        <li><a href="#">Branch <span class="pull-right">....</span></a></li>
                        <li><a href="#">URL <span class="pull-right">....</span></a></li>
                    </ul>
                </div><!-- /.box-body -->
            </div><!--/.direct-chat -->
        </div><!-- /.col -->

        <div class="col-md-4">
            <!-- USERS LIST -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Deployments</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="#">Today <span class="pull-right">1</span></a></li>
                        <li><a href="#">Last Week <span class="pull-right">6</span></a></li>
                        <li><a href="#">Latest Duration <span class="pull-right text-green">6 minutes</span></a></li>
                    </ul>
                </div><!-- /.box-body -->
            </div><!--/.box -->
        </div><!-- /.col -->

        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Health Check</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <p>blah</p>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->

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
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="servers">
                        @include('project._partials.servers')
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="hooks">
                        @include('project._partials.commands')
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="notifications">
                        @include('project._partials.notifications')
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
            </div><!-- nav-tabs-custom -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop