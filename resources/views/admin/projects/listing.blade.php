@extends('layout')

@section('content')
    <div class="box">

        <div class="box-body" id="no_projects">
            <p>{{ Lang::get('projects.none') }}</p>
        </div>

        <div class="box-body table-responsive" id="project_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ Lang::get('projects.name') }}</th>
                        <th>{{ Lang::get('projects.group') }}</th>
                        <th>{{ Lang::get('projects.repository') }}</th>
                        <th>{{ Lang::get('projects.branch') }}</th>
                        <th>{{ Lang::get('projects.builds') }}</th>
                        <th>{{ Lang::get('projects.latest') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('admin.dialogs.project')

    <script type="text/template" id="project-template">
        <td><%- name %></td>
        <td><%- group_name %></td>
        <td><%- repository %></td>
        <td><span class="label label-default"><%- branch %></span></td>
        <td><%- builds_to_keep %></td>
        <td>
            <% if (deploy) { %>
                <%- deploy %>
            <% } else { %>
                {{ Lang::get('app.never') }}
            <% } %>
        </td>
        <td>
            <div class="btn-group pull-right">
                <button class="btn btn-default btn-edit" title="{{ Lang::get('app.edit') }}" data-toggle="modal" data-target="#project"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>

    <script type="text/javascript">
        Lang.create = '{{ Lang::get('projects.create') }}';
        Lang.edit = '{{ Lang::get('projects.edit') }}';
    </script>
@stop

@section('javascript')
    <script type="text/javascript">
        new app.ProjectsTab();
        app.Projects.add({!! $projects !!});
    </script>
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="{{ Lang::get('projects.create') }}" data-toggle="modal" data-target="#project"><span class="fa fa-plus"></span> {{ Lang::get('projects.create') }}</button>
    </div>
@stop