@extends('layout')

@section('content')
    <div class="row">
        @include('commands._partials.list', [ 'step' => 'Before', 'action' => 1 ])
        @include('commands._partials.list', [ 'step' => 'After', 'action' => 3 ])
    </div>

    @include('dialogs.command')

    <script type="text/template" id="command-template">
        <td data-command-id="<%- id %>"><%- name %></td>
        <td><%- user %></td>
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
    </script>
@stop
