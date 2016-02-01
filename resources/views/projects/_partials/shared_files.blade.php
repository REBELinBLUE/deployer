<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('sharedFiles.create') }}" data-toggle="modal" data-target="#sharefile"><span class="fa fa-plus"></span> {{ Lang::get('sharedFiles.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('sharedFiles.label') }}</h3>
    </div>


    <div class="box-body" id="no_files">
        <p>{{ Lang::get('sharedFiles.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="file_list">
            <thead>
                <tr>
                    <th>{{ Lang::get('sharedFiles.name') }}</th>
                    <th>{{ Lang::get('sharedFiles.file') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script type="text/template" id="files-template">
    <td><%- name %></td>
    <td><%- file %></td>
    <td>
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('sharedFiles.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#sharefile"><i class="fa fa-edit"></i></button>
        </div>
    </td>
</script>

<script type="text/javascript">
    Lang.sharedFiles = {
        create: '{{ Lang::get('sharedFiles.create') }}',
        edit: '{{ Lang::get('sharedFiles.edit') }}'
    };
</script>
