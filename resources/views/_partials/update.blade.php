@if ($is_outdated)
<div class="alert alert-info" id="update-available">
    <h4><i class="icon fa fa-cloud-download"></i> {{ Lang::get('app.update_available') }}</h4>
    <strong>{!! Lang::get('app.outdated', ['current' => $current_version, 'latest' => $latest_version, 'link' => 'https://github.com/REBELinBLUE/deployer/releases/latest' ]) !!}</strong>
</div>
@endif
