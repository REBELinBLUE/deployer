@if ($is_outdated)
<div class="alert alert-info" id="update-available">
    <h4><i class="icon fa fa-cloud-download"></i> {{ trans('app.update_available') }}</h4>
    <strong>{!! trans('app.outdated', [
        'current' => $current_version,
        'latest'  => $latest_version,
        'link'    => 'http://www.dereferer.org/?https%3A%2F%2Fgithub%2Ecom%2FREBELinBLUE%2Fdeployer%2Freleases%2Flatest'
    ]) !!}</strong>
</div>
@endif
