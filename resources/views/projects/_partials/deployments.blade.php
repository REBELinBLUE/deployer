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
                    <th>{{ Lang::get('deployments.started_by') }}</th>
                    <th>{{ Lang::get('deployments.deployer') }}</th>
                    <th>{{ Lang::get('deployments.committer') }}</th>
                    <th>{{ Lang::get('deployments.commit') }}</th>
                    <th>{{ Lang::get('deployments.branch') }}</th>
                    <th>{{ Lang::get('app.status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deployments as $deployment)
                <tr id="deployment_{{ $deployment->id }}">
                    <td>{{ $deployment->started_at->format('jS F Y g:i:s A') }}</td>
                    <td>
                        {{ $deployment->is_webhook ? Lang::get('deployments.webhook') : Lang::get('deployments.manually') }}
                        @if (!empty($deployment->reason))
                            <i class="fa fa-comment-o deploy-reason" data-toggle="tooltip" data-placement="right" title="{{ $deployment->reason }}"></i>
                        @endif
                    </td>
                    <td>
                        @if ($deployment->build_url)
                            <a href="{{ $deployment->build_url }}" target="_blank">{{ $deployment->deployer_name }}</a>
                        @else
                            {{ $deployment->deployer_name }}
                        @endif
                    </td>
                    <td>{{ $deployment->committer_name }}</td>
                    <td>
                        @if ($deployment->commit_url)
                        <a href="{{ $deployment->commit_url }}" target="_blank">{{ $deployment->short_commit_hash }}</a>
                        @else
                        {{ $deployment->short_commit_hash }}
                        @endif
                    </td>
                    <td><a href="{{ $deployment->branch_url }}" target="_blank"><span class="label label-default">{{ $deployment->branch }}</span></a></td>
                    <td>
                        <span class="label label-{{ $deployment->css_class }}"><i class="fa fa-{{ $deployment->icon }}"></i> <span>{{ $deployment->readable_status }}</span></span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            @if ($deployment->isSuccessful() && !$deployment->isCurrent())
                                <!--button type="button" data-toggle="modal" data-backdrop="static" data-target="#redeploy" data-deployment-id="{{ $deployment->id }}" class="btn btn-default" title="{{ Lang::get('deployments.rollback') }}"><i class="fa fa-cloud-upload"></i></button-->
                                <a href="{{ route('rollback', ['id' => $deployment->id]) }}" class="btn btn-default" title="{{ Lang::get('deployments.rollback') }}"><i class="fa fa-cloud-upload"></i></a>
                            @endif

                            @if ($deployment->isPending() || $deployment->isRunning())
                                <!-- FIXME: Try and change this to a form as abort should be a POST request -->
                                <a href="{{ route('abort', ['id' => $deployment->id]) }}" class="btn btn-default" title="{{ Lang::get('deployments.cancel') }}"><i class="fa fa-ban"></i></a>
                            @endif
                            <a href="{{ route('deployment', ['id' => $deployment->id]) }}" type="button" class="btn btn-default" title="{{ Lang::get('app.details') }}"><i class="fa fa-info-circle"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {!! $deployments->render() !!}
    </div>

    @endif
</div>
