@extends('layout')

@section('content')
    <div class="box">
        <div class="box-body table-responsive" id="group_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Number of Projects</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('dialogs.group')

    <script type="text/template" id="group-template">
        <td><%- name %></td>
        <td><%- project_count %></td>
        <td>
            <div class="btn-group pull-right">
                <button class="btn btn-default btn-edit" title="Edit" data-toggle="modal" data-target="#group" data-group-id="<%- id %>"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        var groups = {!! $groups->toJson() !!};

        new app.GroupsTab();
        app.Groups.add(groups);
    </script>
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="Add a new group" data-toggle="modal" data-target="#group"><span class="fa fa-plus"></span> Add a group</button>
    </div>
@stop