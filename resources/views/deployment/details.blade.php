@extends('layout')

@section('content')
    @if (!empty($deployment->reason))
        <p><strong>{{ trans('deployments.reason') }}</strong>: {{ $deployment->reason }}</p>
    @endif
    <div class="row">
        <div class="col-xs-12" id="{{ $deployment->repo_failure ? '' : 'repository_error' }}">
            <div class="callout callout-danger">
                <h4><i class="icon fa fa-ban"></i> {{ trans('deployments.repo_failure_head') }}</h4>
                <p>{{ trans('deployments.repo_failure') }}</p>
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
                                <th>{{ trans('deployments.server') }}</th>
                                <th>{{ trans('deployments.status') }}</th>
                                <th>{{ trans('deployments.started') }}</th>
                                <th>{{ trans('deployments.finished') }}</th>
                                <th>{{ trans('deployments.duration') }}</th>
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

    @include('deployment.log')
@stop

@push('javascript')
    <script type="text/javascript">
        new app.views.Logs();
        app.collections.Logs.add({!! $output !!});

        app.setProjectId({{ $project->id }});
    </script>
@endpush

@push('templates')
    <script type="text/template" id="log-template">
        <td width="30%"><%- server.name %></td>
        <td width="15%">
             <span class="label label-<%- status_css %>"><i class="fa fa-<%- icon_css %>"></i> <span><%- status %></span></span>
        </td>
        <td width="15%">
            <% if (formatted_start_time) { %>
                <%- formatted_start_time %>
            <% } else { %>
                {{ trans('app.not_applicable') }}
            <% } %>
        </td>
        <td width="15%">
            <% if (formatted_end_time) { %>
                <%- formatted_end_time %>
            <% } else { %>
                {{ trans('app.not_applicable') }}
            <% } %>
        </td>
        <td width="15%">
         <% if (runtime !== null) { %>
                <%- runtime %>
            <% } else { %>
                {{ trans('app.not_applicable') }}
            <% } %>
            </td>
        <td width="10%">
            <div class="btn-group pull-right">
                <% if (output !== null) { %>
                    <button type="button" class="btn btn-default" title="{{ trans('deployments.output') }}" id="log_<%- id %>" data-toggle="modal" data-backdrop="static" data-target="#log"><i class="fa fa-eye"></i></button>
                <% } %>
            </div>
        </td>
    </script>
@endpush
