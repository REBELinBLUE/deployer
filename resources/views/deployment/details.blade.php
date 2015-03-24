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
        <td><%- server.name %></td>
        <td>
             <span class="label label-<%- status_css %>"><i class="fa fa-<%- icon_css %>"></i> <span><%- status %></span></span>
        </td>
        <td><%- start_time %></td>
        <td><%- end_time %></td>
        <td><%- total_time %></td>
        <td>
            <div class="btn-group pull-right">
                <% if (output !== null) { %>
                    <button type="button" class="btn btn-default" title="View the output" data-toggle="modal" data-backdrop="static" data-target="#log"><i class="fa fa-copy"></i></button>
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