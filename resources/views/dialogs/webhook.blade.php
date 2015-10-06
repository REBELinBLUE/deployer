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
                <ul>
                    <li><em>reason</em> - {{ Lang::get('commands.webhook_reason') }}</li>
                    <li><em>source</em> - {{ Lang::get('commands.webhook_source') }}</li>
                    <li><em>url</em> - {{ Lang::get('commands.webhook_url') }}</li>
                    @if(count($optional))
                        <li><em>commands</em> - {{ Lang::get('commands.webhook_commands') }}</li>
                    @endif
                </ul>

                @if (count($optional))
                    <h5><strong>{{ Lang::get('commands.webhook_optional') }}</strong></h5>
                    <ul>
                        @foreach($optional as $command)
                        <li><em>{{ $command->id }}</em> - {{ $command->name }}</li>
                        @endforeach
                    </ul>
                @endif


                <h5><strong>{{ Lang::get('commands.webhook_curl') }}</strong></h5>
                <pre>curl -X POST {{ $project->webhook_url }} -d 'reason={{ urlencode(Lang::get('commands.reason_example')) }}'</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ Lang::get('app.close') }}</button>
            </div>
        </div>
    </div>
</div>
