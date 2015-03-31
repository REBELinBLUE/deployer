@extends('layout')

@section('content')
    <div class="row">
        @foreach($deployment->steps as $step)
        <div class="col-xs-12">
            <div class="box deploy-step">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-terminal"></i> <span>{{ $step->command ? $step->command->name : deploy_step_label($step->stage) }}</span></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Server</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Finished</th>
                                <th>Duration</th>
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
                    <%- output %>
                    <button type="button" class="btn btn-default" title="View the output" id="log_<%- id %>" data-toggle="modal" data-backdrop="static" data-target="#log"><i class="fa fa-copy"></i></button>
                <% } %>
            </div>
        </td>
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        new app.DeploymentView();
        app.Deployment.add({!! json_encode($output) !!});
    </script>
@stop

