<div class="callout">
    <h4>{{ Lang::get('commands.deploy_webhook') }}</h4>
    <code id="webhook">{{ route('webhook', $project->hash) }}</code><button class="btn btn-xs btn-link" id="new_webhook" title="{{ Lang::get('commands.generate_webhook') }}" data-project-id="{{ $project->id }}"><i class="fa fa-refresh"></i></button>
</div>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">{{ Lang::get('commands.label') }}</h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ Lang::get('commands.step') }}</th>
                    <th>{{ Lang::get('commands.before') }}</th>
                    <th>{{ Lang::get('commands.after') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_keys($commands) as $step)
                <tr>
                    <td>{{ command_label($step) }}</td>
                    <td>{{ command_list_readable($commands, $step, 'before') }}</td>
                    <td>{{ command_list_readable($commands, $step, 'after') }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route('commands', ['id' => $project->id, 'command' => command_name($step)]) }}" class="btn btn-default" title="{{ Lang::get('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>