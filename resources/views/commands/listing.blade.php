@extends('layout')

@section('content')
    <div class="row">
        @include('commands._partials.list', [ 'step' => 'Before', 'action' => $action - 1 ])
        @include('commands._partials.list', [ 'step' => 'After', 'action' => $action + 1 ])
    </div>

    @include('commands.dialog')
@stop

@push('javascript')
    <script type="text/javascript">
        new app.views.Commands({{ $action }});

        app.collections.Commands.add({!! $commands->toJson() !!});

        app.setProjectId({{ $project->id }});
    </script>
@endpush

@push('templates')
    <script type="text/template" id="command-template">
        <td data-command-id="<%- id %>"><%- name %></td>
        <td>
            <%= user ? user : '{{ trans('commands.default') }}' %>
        </td>
        <td>
            <% if (optional) { %>
                {{ trans('app.yes') }}
            <% } else { %>
                {{ trans('app.no') }}
            <% } %>
        </td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-edit" title="{{ trans('commands.edit') }}" data-toggle="modal" data-target="#command"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
