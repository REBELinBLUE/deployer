<div class="callout">
    <h4>Deployments may be triggered by using the following webhook URL</h4>
    <code id="webhook">{{ route('webhook', $project->hash) }}</code><button class="btn btn-xs btn-link" id="new_webhook" title="Generate a new webhook URL (old URL will stop working)" data-project-id="{{ $project->id }}"><i class="fa fa-refresh"></i></button>
</div>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Commands</h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Step</th>
                    <th>Before</th>
                    <th>After</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['clone', 'install', 'activate', 'purge'] as $step)
                <tr>
                    <td>{{ deploy_step_label(ucfirst($step)) }}</td>
                    <td>{{ command_list_readable($commands, $step, 'before') }}</td>
                    <td>{{ command_list_readable($commands, $step, 'after') }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route('commands', ['id' => $project->id, 'command' => $step]) }}" class="btn btn-default" title="Configure"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>