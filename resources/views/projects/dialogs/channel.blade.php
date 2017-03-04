<div class="modal fade" id="notification">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-bullhorn"></i> <span>{{ trans('channels.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="notification_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <input type="hidden" name="type" id="notification_type" value="" />
                <div class="modal-body">
                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ trans('channels.warning') }}
                    </div>

                    <div class="callout callout-warning">
                        <h4><i class="icon fa fa-ban"></i> {{ trans('channels.not_configured_title') }}</h4>
                        {{ trans('channels.not_configured') }}
                    </div>

                    <div id="channel-type">
                        <p>{{ trans('channels.which') }}</p>
                        <div class="row text-center">
                            <a class="btn btn-app" data-type="slack"><i class="fa fa-slack"></i> {{ trans('channels.slack') }}</a>
                            <a class="btn btn-app" data-type="hipchat" @if (empty(config('services.hipchat.token'))) disabled @endif><i class="fa fa-comment-o fa-flip-horizontal"></i> {{ trans('channels.hipchat') }}</a>
                            <a class="btn btn-app" data-type="twilio" @if (empty(config('services.twilio.account_sid'))) disabled @endif><i class="fa fa-mobile"></i> {{ trans('channels.twilio') }}</a>
                            <a class="btn btn-app" data-type="mail"><i class="fa fa-envelope-o"></i> {{ trans('channels.mail') }}</a>
                            <a class="btn btn-app" data-type="custom"><i class="fa fa-cogs"></i> {{ trans('channels.custom') }}</a>
                        </div>
                    </div>

                    <div class="channel-config form-group" id="channel-name">
                        <label for="notification_name">{{ trans('channels.name') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" id="notification_name" name="name" placeholder="{{ trans('channels.bot') }}" />
                        </div>
                    </div>

                    @include('projects.dialogs.channels.slack')
                    @include('projects.dialogs.channels.hipchat')
                    @include('projects.dialogs.channels.twilio')
                    @include('projects.dialogs.channels.mail')
                    @include('projects.dialogs.channels.custom')

                    <div class="channel-config form-group" id="channel-triggers">
                        <label>{{ trans('channels.triggers') }}</label>
                        <div class="checkbox">
                            <label for="notification_on_deployment_success">
                                <input type="checkbox" value="1" name="on_deployment_success" id="notification_on_deployment_success" />
                                {{ trans('channels.on_deployment_success') }}
                            </label>
                        </div>

                        <div class="checkbox">
                            <label for="notification_on_deployment_failure">
                                <input type="checkbox" value="1" name="on_deployment_failure" id="notification_on_deployment_failure" />
                                {{ trans('channels.on_deployment_failure') }}
                            </label>
                        </div>

                        <div class="checkbox">
                            <label for="notification_on_link_down">
                                <input type="checkbox" value="1" name="on_link_down" id="notification_on_link_down" />
                                {{ trans('channels.on_link_down') }}
                            </label>
                        </div>

                        <div class="checkbox">
                            <label for="notification_on_link_still_down">
                                <input type="checkbox" value="1" name="on_link_still_down" id="notification_on_link_still_down" />
                                {{ trans('channels.on_link_still_down') }}
                            </label>
                        </div>

                        <div class="checkbox">
                            <label for="notification_on_link_recovered">
                                <input type="checkbox" value="1" name="on_link_recovered" id="notification_on_link_recovered" />
                                {{ trans('channels.on_link_recovered') }}
                            </label>
                        </div>

                        <div class="checkbox">
                            <label for="notification_on_heartbeat_missing">
                                <input type="checkbox" value="1" name="on_heartbeat_missing" id="notification_on_heartbeat_missing" />
                                {{ trans('channels.on_heartbeat_missing') }}
                            </label>
                        </div>

                        <div class="checkbox">
                            <label for="notification_on_heartbeat_still_missing">
                                <input type="checkbox" value="1" name="on_heartbeat_still_missing" id="notification_on_heartbeat_still_missing" />
                                {{ trans('channels.on_heartbeat_still_missing') }}
                            </label>
                        </div>

                        <div class="checkbox">
                            <label for="notification_on_heartbeat_recovered">
                                <input type="checkbox" value="1" name="on_heartbeat_recovered" id="notification_on_heartbeat_recovered" />
                                {{ trans('channels.on_heartbeat_recovered') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> {{ trans('app.delete') }}</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ trans('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
