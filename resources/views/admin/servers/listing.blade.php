@extends('layout')

@section('content')
    <div class="box">
        <div class="box-body" id="no_server_templates">
            <p>{{ trans('servers.none') }}</p>
        </div>
        <div class="box-body table-responsive" id="server_template_list">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>{{ trans('servers.name') }}</th>
                    <th>{{ trans('servers.ip_address') }}</th>
                    <th>{{ trans('servers.port') }}</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    @include('admin.servers.dialog')
@stop

@section('right-buttons')
    <div class="pull-right">
        <button type="button" class="btn btn-default" title="{{ trans('servers.create') }}" data-toggle="modal" data-target="#server_template"><span class="fa fa-plus"></span> {{ trans('servers.create') }}</button>
    </div>
@stop

@push('javascript')
<script type="text/javascript">
    new app.ServerTemplatesTab();
    app.ServerTemplates.add({!! $servers !!});
</script>
@endpush

@push('templates')
<script type="text/template" id="server-template">
    <td><%- name %></td>
    <td><%- ip_address %></td>
    <td><%- port %></td>
    <td>
        <div class="btn-group pull-right">
            <button class="btn btn-default btn-edit" title="{{ trans('app.edit') }}" data-toggle="modal" data-target="#server_template"><i class="fa fa-edit"></i></button>
        </div>
    </td>
</script>
@endpush