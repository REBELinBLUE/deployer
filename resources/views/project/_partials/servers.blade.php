<div class="box">
    <div class="box-header">
        <div class="pull-right">
            <button type="button" class="btn btn-default" title="Add a new server" data-toggle="modal" data-backdrop="static" data-target="#server"><span class="fa fa-plus"></span> Add Server</button>
        </div>
        <h3 class="box-title">Servers</h3>
    </div>
    
    <div class="box-body" id="no_servers">
        <p>The project does not currently have any servers setup</p>
    </div>

    <div class="box-body table-responsive" id="server_list">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Connect As</th>
                    <th>IP Address</th>
                    <th>Connection Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

<script type="text/template" id="server-template">
    <td><%- name %></td>
    <td><%- user %></td>
    <td><%- ip_address %></td>
    <td>
         <span class="label"><i class="fa"></i> <span><%- status %></span></span>
    </td>
    <td>
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-default btn-test" title="Test the server connection"><i class="fa fa-refresh"></i></button>
            <button type="button" class="btn btn-default btn-edit" title="Edit the server" data-toggle="modal" data-backdrop="static" data-target="#server"><i class="fa fa-edit"></i></button>
        </div>
    </td>
</script>