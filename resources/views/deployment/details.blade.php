@extends('layout')

@section('content')
    @if (!empty($deployment->reason))
        <p><strong>{{ Lang::get('deployments.reason') }}</strong>: {{ $deployment->reason }}</p>
    @endif
    <div class="row">
        @foreach($deployment->steps as $step)
        <div class="col-xs-12">
            <div class="box deploy-step">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-terminal"></i> <span>{{ $step->command ? $step->command->name : $step->name }}</span></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ Lang::get('deployments.server') }}</th>
                                <th>{{ Lang::get('deployments.status') }}</th>
                                <th>{{ Lang::get('deployments.started') }}</th>
                                <th>{{ Lang::get('deployments.finished') }}</th>
                                <th>{{ Lang::get('deployments.duration') }}</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="step_{{ $step->id }}">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @include('dialogs.log')

    <script type="text/template" id="log-template">
        <td width="30%"><%- server.name %></td>
        <td width="10%">
             <span class="label label-<%- status_css %>"><i class="fa fa-<%- icon_css %>"></i> <span><%- status %></span></span>
        </td>
        <td width="20%"><%- start_time %></td>
        <td width="20%"><%- end_time %></td>
        <td width="10%"><%- total_time %></td>
        <td width="10%">
            <div class="btn-group pull-right">
                <% if (output !== null) { %>
                    <button type="button" class="btn btn-default" title="{{ Lang::get('deployments.output') }}" id="log_<%- id %>" data-toggle="modal" data-backdrop="static" data-target="#log"><i class="fa fa-copy"></i></button>
                <% } %>
            </div>
        </td>
    </script>

    <script type="text/javascript">
        Lang.status = {
            pending: '{{ Lang::get('deployments.pending') }}',
            running: '{{ Lang::get('deployments.running') }}',
            failed: '{{ Lang::get('servers.failed') }}',
            cancelled: '{{ Lang::get('deployments.cancelled') }}',
            completed: '{{ Lang::get('deployments.completed') }}'
        };
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        new app.DeploymentView();
        app.Deployment.add({!! json_encode($output) !!});
    </script>
@stop
