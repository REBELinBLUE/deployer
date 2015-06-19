<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('notifications.create') }}" data-toggle="modal" data-target="#notification"><span class="fa fa-plus"></span> {{ Lang::get('notifications.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('notifications.slack') }}</h3>
    </div>


    <div class="box-body" id="no_notifications">
        <p>{{ Lang::get('notifications.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="notification_list">
            <thead>
                <tr>
                    <th>{{ Lang::get('notifications.name') }}</th>
                    <th>{{ Lang::get('notifications.channel') }}</th>
                    <th>{{ Lang::get('notifications.notify_failure_only') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="{{ Lang::get('notifyEmails.create') }}" data-toggle="modal" data-target="#notifyemail"><span class="fa fa-plus"></span> {{ Lang::get('notifyEmails.create') }}</button>
        </div>
        <h3 class="box-title">{{ Lang::get('notifyEmails.label') }}</h3>
    </div>


    <div class="box-body" id="no_notifyemails">
        <p>{{ Lang::get('notifyEmails.none') }}</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="notifyemail_list">
            <thead>
                <tr>
                    <th>{{ Lang::get('notifyEmails.name') }}</th>
                    <th>{{ Lang::get('notifyEmails.email') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script type="text/template" id="notification-template">
    <td><%- name %></td>
    <td><%- channel %></td>
    <td>
        <% if (failure_only) { %>
            {{ Lang::get('app.yes') }}
        <% } else { %>
            {{ Lang::get('app.no') }}
        <% } %>
    </td>
    <td>
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('notifications.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#notification"><i class="fa fa-edit"></i></button>
        </div>
    </td>
</script>
<script type="text/template" id="notifyemail-template">
    <td><%- name %></td>
    <td><%- email %></td>
    <td>
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default btn-edit" title="{{ Lang::get('notifyEmails.edit') }}" data-toggle="modal" data-backdrop="static" data-target="#notifyemail"><i class="fa fa-edit"></i></button>
        </div>
    </td>
</script>

<script type="text/javascript">
    Lang.notifyEmails = {
        create: '{{ Lang::get('notifyEmails.create') }}',
        edit: '{{ Lang::get('notifyEmails.edit') }}'
    };

    Lang.notifications = {
        create: '{{ Lang::get('notifications.create') }}',
        edit: '{{ Lang::get('notifications.edit') }}'
    };
</script>