<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ trans('sharedFiles.create') }}" data-toggle="modal" data-target="#sharedfile"><span class="fa fa-plus"></span> {{ trans('sharedFiles.create') }}</button>
        </div>
        <h3 class="box-title">{{ trans('sharedFiles.label') }}</h3>
    </div>


    <div class="box-body" id="no_sharedfiles">
        <p>{{ trans('sharedFiles.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="sharedfile_list">
            <thead>
                <tr>
                    <th>{{ trans('sharedFiles.name') }}</th>
                    <th>{{ trans('sharedFiles.file') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@push('templates')
    <script type="text/template" id="sharedfile-template">
        <td><%- name %></td>
        <td><%- file %></td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-edit" title="{{ trans('sharedFiles.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#sharedfile"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
