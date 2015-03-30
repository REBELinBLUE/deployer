@extends('layout')

@section('content')
<div class="box">
    <div class="box-body table-responsive" id="server_list">
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
                @foreach ($users as $user)
                <tr id="user_{{ $user->id }}">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('jS F Y g:i:s A') }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <button class="btn btn-default" title="Edit" data-toggle="modal" data-target="#user" data-user-id="{{ $user->id }}"><i class="fa fa-edit"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('dialogs.user')

@stop

@section('javascript')
    <script type="text/javascript">
        var users = {!! $users->toJson() !!};
    </script>
@stop

@section('right-buttons')

    <div class="pull-right">
        <button type="button" class="btn btn-default" title="Add a new user" data-toggle="modal" data-target="#user"><span class="fa fa-plus"></span> Add a user</button>
    </div>

@stop