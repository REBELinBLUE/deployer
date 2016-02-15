<div class="modal modal-default fade" id="redeploy">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cloud-upload"></i> {{ Lang::get('deployments.rollback_title') }}</h4>
            </div>
            <form role="form" method="post" action="{{ route('rollback', ['deployment' => '?']) }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="modal-body">

                    <div class="alert alert-danger">
                        <h4>{{ Lang::get('deployments.caution') }}</h4>
                        <p>{{ Lang::get('deployments.expert') }}</p>
                        <br />
                        <p>{{ Lang::get('deployments.rollback_warning') }}</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> {{ Lang::get('app.confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
