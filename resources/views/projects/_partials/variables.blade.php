<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('variables.create') }}" data-toggle="modal" data-backdrop="static" data-target="#variable"><span class="fa fa-plus"></span> {{ Lang::get('variables.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('variables.label') }}</h3>
    </div>
    <div class="box-body">
        <p>{!! Lang::get('variables.description') !!}</p>
        <p>{!! Lang::get('variables.example') !!}</p>
    </div>

    <div class="box-body table-responsive" id="variable_list">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ Lang::get('variables.name') }}</th>
                    <th>{{ Lang::get('variables.value') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@push('templates')
    <script type="text/template" id="variable-template">
        <td data-variable-id="<%- id %>"><%- name %></td>
        <td><%- value %></td>
        <td>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('variables.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#variable"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
