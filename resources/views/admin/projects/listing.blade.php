@extends('layout')

@section('content')
    <div class="box">

        <div class="box-body" id="no_projects">
            <p>{{ trans('projects.none') }}</p>
        </div>

        <div class="box-body table-responsive" id="project_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('projects.name') }}</th>
                        <th>{{ trans('projects.group') }}</th>
                        <th>{{ trans('projects.repository') }}</th>
                        <th>{{ trans('projects.branch') }}</th>
                        <th>{{ trans('projects.builds') }}</th>
                        <th>{{ trans('projects.latest') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('admin.projects.dialog')
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="{{ trans('projects.create') }}" data-toggle="modal" data-target="#project"><span class="fa fa-plus"></span> {{ trans('projects.create') }}</button>
    </div>
@stop

@push('javascript')
    <script type="text/javascript">
        new app.views.Projects();
        app.collections.Projects.add({!! $projects !!});
    </script>
@endpush

@push('templates')
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
                {{ trans('app.never') }}
            <% } %>
        </td>
        <td>
            <div class="btn-group pull-right">
                <button class="btn btn-default btn-edit" title="{{ trans('app.edit') }}" data-toggle="modal" data-target="#project"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>

    <script type="text/template" id="project-sidebar-template">
        <li><a href="/projects/<%- id %>" id="sidebar_project_<%- id %>"><%- name %></a></li>
    </script>
@endpush
