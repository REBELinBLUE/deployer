<div class="box">
    <div class="box-header">
        <h3 class="box-title">{{ Lang::get('deployments.latest') }}</h3>
    </div>
    
    @if (!count($deployments))
    <div class="box-body">
        <p>{{ Lang::get('deployments.none') }}</p>
    </div>
    @else
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ Lang::get('app.date') }}</th>
                    <th>{{ Lang::get('deployments.started') }}</th>
                    <th>{{ Lang::get('deployments.deployer') }}</th>
                    <th>{{ Lang::get('deployments.committer') }}</th>
                    <th>{{ Lang::get('deployments.commit') }}</th>
                    <th>{{ Lang::get('app.status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deployments as $deployment)
                <tr id="deployment_{{ $deployment->id }}">
                    <td>{{ $deployment->started_at->format('jS F Y g:i:s A') }}</td>
                    <td>
                        {{ !empty($deployment->user_id) ? Lang::get('deployments.manually') : Lang::get('deployments.webhook') }}
                        @if (!empty($deployment->reason))
                            <button class="btn btn-box-tool" id="deploy-reason"><i class="fa fa-comment-o" data-toggle="tooltip" title="{{ $deployment->reason }}"></i></button>
                        @endif
                    </td>
                    <td>
                        @if (!empty($deployment->user_id))
                            {{ $deployment->user->name }}
                        @else
                            {{ $deployment->committer_name }}
                        @endif
                    </td>
                    <td>{{ $deployment->committer_name }}</td>
                    <td>
                        @if ($deployment->commitURL())
                        <a href="{{ $deployment->commitURL() }}" target="_blank">{{ $deployment->shortCommit() }}</a></td>
                        @else
                        {{ $deployment->short_commit_hash }}
                        @endif
                    <td>
                        <span class="label label-{{ $deployment->css_class }}"><i class="fa fa-{{ $deployment->icon }}"></i> {{ $deployment->readable_status }}</span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default" title="{{ Lang::get('deployments.reactivate') }}" {{ $deployment->isRunning() ? 'disabled' : '' }}><i class="fa fa-cloud-upload"></i></button>
                            <a href="{{ route('deployment', ['id' => $deployment->id]) }}" type="button" class="btn btn-default" title="{{ Lang::get('app.details') }}"><i class="fa fa-info-circle"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>