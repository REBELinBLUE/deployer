<div class="modal fade" id="variable">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-tasks"></i> <span>{{ Lang::get('variables.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="variable_id" name="id" />
                <input type="hidden" name="target_type" value="{{ $target_type }}" />
                <input type="hidden" name="target_id" value="{{ $target_id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('variables.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="variable_name">{{ Lang::get('variables.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" id="variable_name" name="name" placeholder="COMPOSER_PROCESS_TIMEOUT" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="variable_value">{{ Lang::get('variables.value') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-usd"></i></div>
                            <input type="text" class="form-control" id="variable_value" name="value" placeholder="300" />
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
