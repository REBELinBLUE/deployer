@extends('layout')

@section('content')
    <div class="box">
        <div class="box-body table-responsive" id="group_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('groups.name') }}</th>
                        <th>{{ trans('groups.projects') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('admin.groups.dialog')
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="{{ trans('groups.create') }}" data-toggle="modal" data-target="#group"><span class="fa fa-plus"></span> {{ trans('groups.create') }}</button>
    </div>
@stop

@push('javascript')
    <script type="text/javascript">
        new app.views.Groups();
        app.collections.Groups.add({!! $groups->toJson() !!});
    </script>
@endpush

@push('templates')
    <script type="text/template" id="group-template">
        <td data-group-id="<%- id %>"><%- name %></td>
        <td><%- project_count %></td>
        <td>
            <div class="btn-group pull-right">
                <button class="btn btn-default btn-edit" title="{{ trans('groups.edit') }}" data-toggle="modal" data-target="#group" data-group-id="<%- id %>"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>

    <script type="text/template" id="group-sidebar-template">
        <li class="treeview">
            <a href="#">
                <i class="fa fa-book"></i>
                <span id="sidebar_group_<%- id %>"><%- name %></span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>

            <ul class="treeview-menu" id="group_<%- id %>_projects">
            </ul>
        </li>
    </script>
@endpush
