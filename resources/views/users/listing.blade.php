@extends('layout')

@section('content')
    <div class="box">
        <div class="box-body table-responsive" id="user_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('dialogs.user')

    <script type="text/template" id="user-template">
        <td><%- name %></td>
        <td><%- email %></td>
        <td><%- created %></td>
        <td>
            <div class="btn-group pull-right">
                <button class="btn btn-default btn-edit" title="Edit" data-toggle="modal" data-target="#user" data-user-id="<%- id %>"><i class="fa fa-edit"></i></button>
            </div>
        </td>
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
        <button type="button" class="btn btn-default" title="Add a new user" data-toggle="modal" data-target="#user"><span class="fa fa-plus"></span> Add a user</button>
    </div>
@stop