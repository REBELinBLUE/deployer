<div class="modal fade" id="user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <span>{{ Lang::get('users.add') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="user_id" name="id" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('users.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="user_name">{{ Lang::get('users.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" name="name" id="user_name" placeholder="John Smith" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="user_email">{{ Lang::get('users.email') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-envelope-o"></i></div>
                            <input type="email" class="form-control" name="email" id="user_email" placeholder="john.smith@example.net" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="user_password" class="user_password existing-only">{{ Lang::get('users.password_existing') }}</label>
                        <label for="user_password" class="new-only">{{ Lang::get('users.password') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-unlock"></i></div>
                            <input type="password" class="form-control" name="password" id="user_password" />
                        </div>
                    </div>

                    <div class="form-group new-only">
                        <label for="user_password_confirmation">{{ Lang::get('users.password_confirm') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                            <input type="password" class="form-control" name="password_confirmation" id="user_password_confirmation" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> {{ Lang::get('app.delete') }}</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ Lang::get('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
