<div class="modal fade" id="log">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-file-o"></i> {{ Lang::get('deployments.process') }} (<span id="action">&nbsp;</span>)</h4>
            </div>
            <div class="modal-body">
                <div id="loading">
                    <i class="fa fa-spinner fa-pulse"></i> {{ Lang::get('deployments.loading') }}
                </div>
                <pre></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ Lang::get('app.close') }}</button>
            </div>
        </div>
    </div>
</div>