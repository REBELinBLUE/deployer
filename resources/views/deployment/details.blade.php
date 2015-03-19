@extends('layout')

@section('content')
    <div class="row">
        @foreach($steps as $step)
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-terminal"></i> <span>{{ $step->command ? $step->command->name : deploy_step_label($step->stage) }}</span></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-hover" id="step_{{ $step->id }}">
                        <tbody>
                            <tr>
                                <th>Server</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Finished</th>
                                <th>Duration</th>
                                <th>&nbsp;</th>
                            </tr>
                        </tbody>
                        <tbody>
                            @foreach($step->servers as $log)
                            <tr id="log_{{ $log->id }}">
                                <td>{{ $log->server->name }}</td>
                                <td>
                                     <span class="label label-{{ server_log_css_status($log) }}"><i class="fa fa-{{ server_log_icon_status($log) }}"></i> <span>{{ $log->status }}</span></span>
                                </td>
                                <td>{{ $log->started_at ? $log->started_at->format('g:i:s A') : 'N/A' }}</td>
                                <td>{{ $log->finished_at ? $log->finished_at->format('g:i:s A') : 'N/A' }}</td>
                                <td>{{ $log->runtime() === false ? 'N/A' : human_readable_duration($log->runtime()) }}</td>
                                <td>
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-default" title="View the output" data-toggle="modal" data-log-id="{{ $log->id }}" data-backdrop="static" data-target="#log" style="{{ !empty($log->output) ? '' : 'display:none' }}"><i class="fa fa-copy"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @include('dialogs.log')

@stop