<div class="modal fade" id="notification">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-slack"></i> <span>{{ Lang::get('notifications.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="notification_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('notifications.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="notification_name">{{ Lang::get('notifications.name') }}</label>
                        <input type="text" class="form-control" id="notification_name" name="name" placeholder="{{ Lang::get('notifications.bot') }}" />
                    </div>
                    <div class="form-group">
                        <label for="notification_icon">{{ Lang::get('notifications.icon') }}</label>
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('notifications.icon_info') }}"></i>
                        <input type="text" class="form-control" id="notification_icon" name="icon" placeholder=":ghost:" />
                    </div>
                    <div class="form-group">
                        <label for="notification_channel">{{ Lang::get('notifications.channel') }}</label>
                        <input type="text" class="form-control" id="notification_channel" name="channel" placeholder="#slack" />
                    </div>
                    <div class="form-group">
                        <label for="notification_webhook">{{ Lang::get('notifications.webhook') }}</label>
                        <input type="text" class="form-control" id="notification_webhook" name="webhook" placeholder="https://hooks.slack.com/services/" />
                    </div>
                    <div class="form-group">
                        <label>{{ Lang::get('notifications.failure_only') }}</label>
                        <div class="checkbox">
                            <label for="notification_failure_only">
                                <input type="checkbox" value="1" name="failure_only" id="notification_failure_only" /> 
                                {{ Lang::get('notifications.failure_description') }}
                            </label>
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