@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12"> 
            <h2>Clone</h2>
        </div>
        @include('commands._partials.list', [ 'step' => 'Before', 'action' => 1 ])
        @include('commands._partials.list', [ 'step' => 'After', 'action' => 3 ])
    </div>

    <div class="row">
        <div class="col-md-12"> 
            <h2>Install</h2>
        </div>
        @include('commands._partials.list', [ 'step' => 'Before', 'action' => 4 ])
        @include('commands._partials.list', [ 'step' => 'After', 'action' => 6 ])
    </div>

    <div class="row">
        <div class="col-md-12"> 
            <h2>Activate</h2>
        </div>
        @include('commands._partials.list', [ 'step' => 'Before', 'action' => 7 ])
        @include('commands._partials.list', [ 'step' => 'After', 'action' => 9 ])
    </div>

    <div class="row">
        <div class="col-md-12"> 
            <h2>Purge</h2>
        </div>
        @include('commands._partials.list', [ 'step' => 'Before', 'action' => 10 ])
        @include('commands._partials.list', [ 'step' => 'After', 'action' => 12 ])
    </div>


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
