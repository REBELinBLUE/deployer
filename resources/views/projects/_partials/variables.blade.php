<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ trans('variables.create') }}" data-toggle="modal" data-backdrop="static" data-target="#variable"><span class="fa fa-plus"></span> {{ trans('variables.create') }}</button>
        </div>
        <h3 class="box-title">{{ trans('variables.label') }}</h3>
    </div>
    <div class="box-body">
        <p>{!! trans('variables.description') !!}</p>
        <p>{!! trans('variables.example') !!}</p>
    </div>

    <div class="box-body table-responsive" id="variable_list">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans('variables.name') }}</th>
                    <th>{{ trans('variables.value') }}</th>
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
                <button type="button" class="btn btn-default btn-edit" title="{{ trans('variables.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#variable"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
