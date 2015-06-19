@extends('layout')

@section('content')
    <div class="row">
        @include('commands._partials.list', [ 'step' => 'Before', 'action' => $action - 1 ])
        @include('commands._partials.list', [ 'step' => 'After', 'action' => $action + 1 ])
    </div>

    @include('dialogs.command')

    <script type="text/template" id="command-template">
        <td data-command-id="<%- id %>"><%- name %></td>
        <td><%- user %></td>
        <td>
            <% if (optional) { %>
                {{ Lang::get('app.yes') }}
            <% } else { %>
                {{ Lang::get('app.no') }}
            <% } %>
        </td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('commands.edit') }}" data-toggle="modal" data-target="#command"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>

    <script type="text/javascript">
        Lang.create = '{{ Lang::get('commands.create') }}';
        Lang.edit = '{{ Lang::get('commands.edit') }}';
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        new app.CommandsTab();
        app.Commands.add({!! $commands->toJson() !!});

        app.project_id = {{ $project->id }};
        app.command_action = {{ $action }};
    </script>
@stop
