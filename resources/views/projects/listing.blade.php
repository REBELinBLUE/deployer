@extends('layout')

@section('content')
    <div class="box">

        <div class="box-body" id="no_projects">
            <p>There are currently no projects setup</p>
        </div>

        <div class="box-body table-responsive" id="project_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Group</th>
                        <th>Repository</th>
                        <th>Branch</th>
                        <th>Latest Deploy</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('dialogs.project')

    <script type="text/template" id="project-template">
        <td><%- name %></td>
        <td><%- group_id %></td>
        <td><%- repository %></td>
        <td><span class="label label-default"><%- branch %></span></td>
        <td><%- id %></td>
        <td>
            <div class="btn-group pull-right">
                <button class="btn btn-default btn-edit" title="Edit" data-toggle="modal" data-target="#project"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        new app.ProjectsTab();
        app.Projects.add({!! $projects->toJson() !!});
    </script>
@stop


@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="Add a new project" data-toggle="modal" data-target="#project"><span class="fa fa-plus"></span> Add a project</button>
    </div>
@stop