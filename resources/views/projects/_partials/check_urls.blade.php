<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ trans('checkUrls.create') }}" data-toggle="modal" data-target="#checkurl"><span class="fa fa-plus"></span> {{ trans('checkUrls.create') }}</button>
        </div>
        <h3 class="box-title">{{ trans('checkUrls.label') }}</h3>
    </div>


    <div class="box-body" id="no_checkurls">
        <p>{{ trans('checkUrls.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="checkurl_list">
            <thead>
                <tr>
                    <th>{{ trans('checkUrls.title') }}</th>
                    <th>{{ trans('checkUrls.url') }}</th>
                    <th>{{ trans('checkUrls.frequency') }}</th>
                    <th>{{ trans('checkUrls.last_seen') }}</th>
                    <th>{{ trans('checkUrls.last_status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@push('templates')
    <script type="text/template" id="checkurl-template">
        <td><%- name %></td>
        <td><%- url %></td>
        <td><%- interval_label %></td>
        <td>
            <% if (has_run) { %>
                <%- formatted_date %>
            <% } else { %>
                {{ trans('app.never') }}
            <% } %>
        </td>
        <td>
            <span class="label label-<%- status_css %>">
                <i class="fa fa-<%-icon_css %>"></i>
                <%- status %>
            </span>
        </td>
        <td>
            <div class="btn-group pull-right">
                <% if (has_log) { %>
                    <button type="button" class="btn btn-default btn-view" title="{{ trans('checkUrls.log') }}" data-toggle="modal" data-backdrop="static" data-target="#result"><i class="fa fa-eye"></i></button>
                <% } %>
                <button type="button" class="btn btn-default btn-edit" title="{{ trans('checkUrls.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#checkurl"><i class="fa fa-edit"></i></button>
            </div>
        </td>
    </script>
@endpush
