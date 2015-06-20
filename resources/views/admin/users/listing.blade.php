@extends('layout')

@section('content')
    <div class="box">
        <div class="box-body table-responsive" id="user_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ Lang::get('users.name') }}</th>
                        <th>{{ Lang::get('users.email') }}</th>
                        <th>{{ Lang::get('app.created') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('admin.dialogs.user')

    <script type="text/template" id="user-template">
        <td><%- name %></td>
        <td><%- email %></td>
        <td><%- created %></td>
        <td>
            <div class="btn-group pull-right">
                <button class="btn btn-default btn-edit" title="{{ Lang::get('app.edit') }}" data-toggle="modal" data-target="#user" data-user-id="<%- id %>"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>

    <script type="text/javascript">
        Lang.create = '{{ Lang::get('users.create') }}';
        Lang.edit = '{{ Lang::get('users.edit') }}';
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        var users = {!! $users->toJson() !!};

        new app.UsersTab();
        app.Users.add(users);
    </script>
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="{{ Lang::get('users.create') }}" data-toggle="modal" data-target="#user"><span class="fa fa-plus"></span> {{ Lang::get('users.create') }}</button>
    </div>
@stop