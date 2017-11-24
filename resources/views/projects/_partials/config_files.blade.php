<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ trans('configFiles.create') }}" data-toggle="modal" data-target="#configfile"><span class="fa fa-plus"></span> {{ trans('configFiles.create') }}</button>
        </div>
        <h3 class="box-title">{{ trans('configFiles.label') }}</h3>
    </div>


    <div class="box-body" id="no_configfiles">
        <p>{{ trans('configFiles.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="configfile_list">
            <thead>
                <tr>
                    <th>{{ trans('configFiles.name') }}</th>
                    <th>{{ trans('configFiles.path') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@push('templates')
    <script type="text/template" id="configfile-template">
        <td><%- name %></td>
        <td><%- path %></td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-view" title="{{ trans('configFiles.view') }}" data-toggle="modal" data-backdrop="static" data-target="#view-configfile"><i class="fa fa-eye"></i></button>
                <button type="button" class="btn btn-default btn-edit" title="{{ trans('configFiles.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#configfile"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
