@extends('emails.layout')

@section('content')
    <p>
        <span>{{ Lang::get('notifyEmails.project_name') }}:</span>
        <small>{{ $project['name'] }}</small>
    </p>
    <p>
        <span>{{ Lang::get('notifyEmails.deployed_branch') }}:</span>
        <small>{{ $deployment['branch'] }}</small>
    </p>
    <p>
        <span>{{ Lang::get('notifyEmails.started_at') }}:</span>
        <small>{{ $deployment['started_at'] }}</small>
    </p>
    <p>
        <span>{{ Lang::get('notifyEmails.finished_at') }}:</span>
        <small>{{ $deployment['finished_at'] }}</small>
    </p>
    <div class="deployment-info">
        <p>
            <span>{{ Lang::get('notifyEmails.last_committer') }}:</span>
            <small>{{ $deployment['committer'] }}</small>
        </p>
        <p>
            <span>{{ Lang::get('notifyEmails.last_committ') }}:</span>
            <a href="{{ $deployment['commitURL'] }}">
                <small>{{ $deployment['shortCommit'] }}</small>
            </a>
        </p>
        @if($deployment['reason'])
        <p>
            <span>{{ Lang::get('notifyEmails.reason') }}:</span>
            <small>{{ $deployment['reason'] }}</small>
        </p>
        @endif
    </div>
@stop
