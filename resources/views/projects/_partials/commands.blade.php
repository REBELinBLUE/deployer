@if ($target_type === 'project')
<div class="callout">
    <h4>{{ trans('commands.deploy_webhook') }} <i class="fa fa-question-circle" id="show_help" data-toggle="modal" data-backdrop="static" data-target="#help"></i></h4>
    <code id="webhook">{{ $project->webhook_url }}</code><button class="btn btn-xs btn-link" id="new_webhook" title="{{ trans('commands.generate_webhook') }}" data-project-id="{{ $project->id }}"><i class="fa fa-refresh"></i></button>
</div>
@endif

<div class="box">
    <div class="box-header">
        <h3 class="box-title">{{ trans('commands.label') }}</h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans('commands.step') }}</th>
                    <th>{{ trans('commands.before') }}</th>
                    <th>{{ trans('commands.after') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ trans('commands.clone') }}</td>
                    <td>{{ $project->before_clone }}</td>
                    <td>{{ $project->after_clone }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, [$route_field => $project->id, 'step' => 'clone']) }}" class="btn btn-default" title="{{ trans('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ trans('commands.install') }}</td>
                    <td>{{ $project->before_install }}</td>
                    <td>{{ $project->after_install }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, [$route_field => $project->id, 'step' => 'install']) }}" class="btn btn-default" title="{{ trans('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ trans('commands.activate') }}</td>
                    <td>{{ $project->before_activate }}</td>
                    <td>{{ $project->after_activate }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, [$route_field => $project->id, 'step' => 'activate']) }}" class="btn btn-default" title="{{ trans('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ trans('commands.purge') }}</td>
                    <td>{{ $project->before_purge }}</td>
                    <td>{{ $project->after_purge }}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{{ route($route, [$route_field => $project->id, 'step' => 'purge']) }}" class="btn btn-default" title="{{ trans('commands.configure') }}"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
