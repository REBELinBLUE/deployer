<div class="modal fade" id="checkurl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-link"></i> <span>{{ Lang::get('checkUrls.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="url_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ Lang::get('checkUrls.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="title">{{ Lang::get('checkUrls.title') }}</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="{{ Lang::get('checkUrls.titleTip') }}" />
                    </div>
                    <div class="form-group">
                        <label for="url">{{ Lang::get('checkUrls.url') }}</label>
                        <input type="text" class="form-control" id="url" name="url" placeholder="http://admin.example.com/" />
                    </div>
                    <div class="form-group">
                        <label for="period">{{ Lang::get('checkUrls.frequency') }}</label>
                        <ul class="list-unstyled">
                            @foreach ([5, 10, 30, 60] as $time)
                            <li>
                                <div class="checkbox">
                                    <label for="period_{{ $time }}">
                                        <input type="radio" class="checkurl-period" name="period" id="period_{{ $time }}" value="{{ $time }}" /> {{ $time }} {{ Lang::get('checkUrls.length') }}
                                    </label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> {{ Lang::get('app.delete') }}</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ Lang::get('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
