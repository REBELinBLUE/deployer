<div class="modal fade" id="help">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-question-circle"></i> {{ Lang::get('commands.webhook_help') }}</h4>
            </div>
            <div class="modal-body">

                <p>{{ Lang::get('commands.webhook_example') }}</p>
                <h5><strong>{{ Lang::get('commands.webhook_fields') }}</strong></h5>
                <dl class="dl-horizontal" id="hook_fields">
                    <dt><em>branch</em></dt>
                    <dd>{{ Lang::get('commands.webhook_branch') }}</dd>
                    <dt><em>update_only</em></dt>
                    <dd>{{ Lang::get('commands.webhook_update') }}</dd>
                    <dt><em>reason</em></dt>
                    <dd>{{ Lang::get('commands.webhook_reason') }}</dd>
                    <dt><em>source</em></dt>
                    <dd>{{ Lang::get('commands.webhook_source') }}</dd>
                    <dt><em>url</em></dt>
                    <dd>{{ Lang::get('commands.webhook_url') }}</dd>
                    @if(count($optional))
                        <dt><em>commands</em></dt>
                        <dd>{{ Lang::get('commands.webhook_commands') }}</dd>
                    @endif
                </dl>

                @if (count($optional))
                    <h5><strong>{{ Lang::get('commands.webhook_optional') }}</strong></h5>
                    <dl class="dl-horizontal" id="hook_command_ids">
                        @foreach($optional as $command)
                        <dt><em>{{ $command->id }}</em></dt>
                        <dd>{{ $command->name }}</dd>
                        @endforeach
                    </dl>
                @endif

                <h5><strong>{{ Lang::get('commands.webhook_curl') }}</strong></h5>
                <pre>curl -X POST {{ $project->webhook_url }} -d 'reason={{ urlencode(Lang::get('commands.reason_example')) }}&amp;branch=master&amp;update_only=true'</pre>

                <hr />

                <h5><strong>{{ Lang::get('commands.services') }} - <i class="fa fa-github"></i> Github, <i class="fa fa-gitlab"></i> Gitlab, <i class="fa fa-bitbucket"></i> Bitbucket &amp; Beanstalk</strong></h5>
                <p>{!! Lang::get('commands.services_description') !!}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ Lang::get('app.close') }}</button>
            </div>
        </div>
    </div>
</div>
