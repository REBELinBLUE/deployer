@extends('layout')

@section('content')
    @if (!empty($deployment->reason))
        <p><strong>{{ Lang::get('deployments.reason') }}</strong>: {{ $deployment->reason }}</p>
    @endif
    <div class="row">
        <div class="col-xs-12" id="{{ $deployment->repo_failure ? '' : 'repository_error' }}">
            <div class="callout callout-danger">
                <h4><i class="icon fa fa-ban"></i> {{ Lang::get('deployments.repo_failure_head') }}</h4>
                <p>{{ Lang::get('deployments.repo_failure') }}</p>
            </div>
        </div>

        @foreach($deployment->steps as $step)
        <div class="col-xs-12">
            <div class="box deploy-step">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-terminal"></i> <span>{{ $step->name }}</span></h3>
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
        <td width="20%">
            <% if (formatted_start_time) { %>
                <%- formatted_start_time %>
            <% } else { %>
                {{ Lang::get('app.not_applicable') }}
            <% } %>
        </td>
        <td width="20%">
            <% if (formatted_end_time) { %>
                <%- formatted_end_time %>
            <% } else { %>
                {{ Lang::get('app.not_applicable') }}
            <% } %>
        </td>
        <td width="10%">
         <% if (runtime !== null) { %>
                <%- runtime %>
            <% } else { %>
                {{ Lang::get('app.not_applicable') }}
            <% } %>
            </td>
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
        app.Deployment.add({!! $output !!});

        app.project_id = {{ $deployment->project_id }};
    </script>
@stop
