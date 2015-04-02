<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="Add a new channel" data-toggle="modal" data-target="#notification"><span class="fa fa-plus"></span> Add Notification</button>
        </div>
        <h3 class="box-title">Notifications</h3>
    </div>


    <div class="box-body" id="no_notifications">
        <p>The project does not currently have any notifications setup</p>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped" id="notification_list">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Channel</th>
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
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default btn-edit" title="Edit the notification" data-toggle="modal" data-backdrop="static" data-target="#notification"><i class="fa fa-edit"></i></button>
        </div>
    </td>
</script>