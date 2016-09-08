@extends('layout')

@section('content')
    <div class="box">

        <div class="box-body" id="no_templates">
            <p>{{ Lang::get('templates.none') }}</p>
        </div>

        <div class="box-body table-responsive" id="template_list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ Lang::get('templates.name') }}</th>
                        <th>{{ Lang::get('commands.label') }}</th>
                        <th>{{ Lang::get('variables.label') }}</th>
                        <th>{{ Lang::get('sharedFiles.label') }}</th>
                        <th>{{ Lang::get('configFiles.label') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    @include('admin.templates.dialog')
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="{{ Lang::get('templates.create') }}" data-toggle="modal" data-target="#template"><span class="fa fa-plus"></span> {{ Lang::get('templates.create') }}</button>
    </div>
@stop

@push('javascript')
    <script type="text/javascript">
        new app.TemplatesTab();
        app.Templates.add({!! $templates !!});
    </script>
@endpush

@push('templates')
    <script type="text/template" id="template-template">
        <td><%- name %></td>
        <td><%- command_count %></td>
        <td><%- variable_count %></td>
        <td><%- file_count %></td>
        <td><%- config_count %></td>
        <td>
            <div class="btn-group pull-right">
                <a href="/admin/templates/<%- id %>" class="btn btn-default" title="{{ Lang::get('commands.configure') }}"><i class="fa fa-gear"></i></a>
                <button class="btn btn-default btn-edit" title="{{ Lang::get('app.edit') }}" data-toggle="modal" data-target="#template"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
