<div class="channel-config" id="channel-config-slack">
    <div class="form-group">
        <label for="notification_config_icon">{{ Lang::get('channels.icon') }}</label>
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('channels.icon_info') }}"></i>
        <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-address-book-o"></i></div>
            <input type="text" class="form-control" id="notification_config_icon" name="icon" placeholder=":ghost:" />
        </div>
    </div>
    <div class="form-group">
        <label for="notification_config_channel">{{ Lang::get('channels.channel') }}</label>
        <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-commenting-o"></i></div>
            <input type="text" class="form-control" id="notification_config_channel" name="channel" placeholder="#slack" />
        </div>
    </div>
    <div class="form-group">
        <label for="notification_config_webhook">{{ Lang::get('channels.webhook') }}</label>
        <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-external-link"></i></div>
            <input type="text" class="form-control" id="notification_config_webhook" name="webhook" placeholder="https://hooks.slack.com/services/" />
        </div>
    </div>
</div>
