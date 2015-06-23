@if (!$project->is_template)
<div class="callout">
    <h4>{{ Lang::get('commands.deploy_webhook') }} <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ Lang::get('commands.webhook_example') }}"></i></h4>
    <code id="webhook">{{ $project->webhook_url }}</code><button class="btn btn-xs btn-link" id="new_webhook" title="{{ Lang::get('commands.generate_webhook') }}" data-project-id="{{ $project->id }}"><i class="fa fa-refresh"></i></button>
</div>
@endif

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
                <tr>
                    <td>{{ Lang::get('commands.clone') }}</td>
                    <td>{{ $project->before_clone }}</td>
                    <td>{{ $project->after_clone }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, ['id' => $project->id, 'command' => 'clone']) }}" class="btn btn-default" title="{{ Lang::get('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ Lang::get('commands.install') }}</td>
                    <td>{{ $project->before_install }}</td>
                    <td>{{ $project->after_install }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, ['id' => $project->id, 'command' => 'install']) }}" class="btn btn-default" title="{{ Lang::get('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ Lang::get('commands.activate') }}</td>
                    <td>{{ $project->before_activate }}</td>
                    <td>{{ $project->after_activate }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, ['id' => $project->id, 'command' => 'activate']) }}" class="btn btn-default" title="{{ Lang::get('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ Lang::get('commands.purge') }}</td>
                    <td>{{ $project->before_purge }}</td>
                    <td>{{ $project->after_purge }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, ['id' => $project->id, 'command' => 'purge']) }}" class="btn btn-default" title="{{ Lang::get('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>