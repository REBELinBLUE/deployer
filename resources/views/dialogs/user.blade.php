<div class="modal fade" id="user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <span>Add a new user</span></h4>
            </div>
            <form role="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="user_id" name="id" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> The user could not be saved, please check the form below.
                    </div>

                    <div class="form-group">
                        <label for="user_name">Name</label>
                        <input type="text" class="form-control" name="name" id="user_name" placeholder="John Smith" />
                    </div>
                    <div class="form-group">
                        <label for="user_email">Email</label>
                        <input type="email" class="form-control" name="email" id="user_email" placeholder="john.smith@example.net" />
                    </div>

                    <div class="form-group">
                        <label for="user_password" class="user_password existing-only">Password (leave blank to leave unchanged)</label>
                        <label for="user_password" class="new-only">Password</label>
                        <input type="password" class="form-control" name="password" id="user_password" />
                    </div>

                    <div class="form-group new-only">
                        <label for="user_password_confirmation">Password Confirmation</label>
                        <input type="password" class="form-control" name="password_confirmation" id="user_password_confirmation" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>