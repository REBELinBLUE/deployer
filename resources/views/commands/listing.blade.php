@extends('layout')

@section('content')
    <div class="row">
        @include('commands._partials.list', [ 'step' => 'Before' ])
        @include('commands._partials.list', [ 'step' => 'After' ])
    </div>

    @include('dialogs.command')

    <script type="text/template" id="command-template">
        <td>
            <div class="btn-group">
                <% if (!first) { %>
                    <button type="button" class="btn btn-xs btn-link pull-left"><i class="fa fa-long-arrow-up"></i></button>
                <% } %>

                <% if (!last) { %>
                    <button type="button" class="btn btn-xs btn-link pull-right"><i class="fa fa-long-arrow-down"></i></button>
                <% } %>
            </div>
        </td>
        <td><%- name %></td>
        <td><%- user %></td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-edit" title="Edit the command" data-toggle="modal" data-target="#command"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        new app.CommandsTab();
        app.Commands.add({!! $commands->toJson() !!});
    </script>
@stop
