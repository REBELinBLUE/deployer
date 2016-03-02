<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('projectFiles.create') }}" data-toggle="modal" data-target="#projectfile"><span class="fa fa-plus"></span> {{ Lang::get('projectFiles.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('projectFiles.label') }}</h3>
    </div>


    <div class="box-body" id="no_projectfiles">
        <p>{{ Lang::get('projectFiles.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="projectfile_list">
            <thead>
                <tr>
                    <th>{{ Lang::get('projectFiles.name') }}</th>
                    <th>{{ Lang::get('projectFiles.path') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@push('templates')
    <script type="text/template" id="project-files-template">
        <td><%- name %></td>
        <td><%- path %></td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-view" title="{{ Lang::get('projectFiles.view') }}" data-toggle="modal" data-backdrop="static" data-target="#view-projectfile"><i class="fa fa-eye"></i></button>
                <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('projectFiles.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#projectfile"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
