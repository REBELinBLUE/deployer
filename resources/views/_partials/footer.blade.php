<footer class="main-footer @if($is_outdated) bg-red @endif">
    <div class="pull-right">
        <strong>{{ Lang::get('app.version') }}</strong> {{ $current_version }}
    </div>

    &nbsp;

    @if($is_outdated)
        <span><strong>{!! Lang::get('app.outdated', ['version' => $latest_version, 'link' => 'https://github.com/REBELinBLUE/deployer/releases/latest' ]) !!}</strong></span>
    @endif

</footer>
