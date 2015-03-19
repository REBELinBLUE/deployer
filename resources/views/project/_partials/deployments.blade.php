<div class="box">
    <div class="box-header">
        <h3 class="box-title">Recent Deployments</h3>
    </div>
    
    @if (!count($deployments))
    <div class="box-body">
        <p>There have not been any deployments yet.</p>
    </div>
    @else
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Deployer</th>
                    <th>Committer</th>
                    <th>Commit</th>
                    <th>Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deployments as $deployment)
                <tr id="deployment_{{ $deployment->id }}">
                    <td>{{ $deployment->started_at->format('jS F Y g:i:s A') }}</td>
                    <td>{{ $deployment->user->name }}</td>
                    <td>{{ $deployment->committer}}</td> <!-- Link to repo? -->
                    <td><a href="#">{{ $deployment->commit }}</a></td>
                    <td>
                        <span class="label label-{{ deployment_css_status($deployment) }}"><i class="fa fa-{{ deployment_icon_status($deployment) }}"></i> {{ $deployment->status }}</span>
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <!-- FIXME Hide this button on the newest deployment -->
                            <button type="button" class="btn btn-default" title="Re-Deploy" {{ $deployment->isRunning() ? 'disabled' : '' }}><i class="fa fa-cloud-upload"></i></button>
                            <a href="{{ route('deployment', ['id' => $deployment->id]) }}" type="button" class="btn btn-default" title="Details"><i class="fa fa-info-circle"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>