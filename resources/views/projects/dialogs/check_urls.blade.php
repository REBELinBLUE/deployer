<div class="modal fade" id="checkurl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-link"></i> <span>{{ trans('checkUrls.create') }}</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="checkurl_id" name="id" />
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> {{ trans('checkUrls.warning') }}
                    </div>

                    <div class="form-group">
                        <label for="url_name">{{ trans('checkUrls.title') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-tag"></i></div>
                            <input type="text" class="form-control" id="checkurl_name" name="name" placeholder="{{ trans('checkUrls.titleTip') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="url_url">{{ trans('checkUrls.url') }}</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-external-link"></i></div>
                            <input type="text" class="form-control" id="checkurl_url" name="url" placeholder="http://admin.example.com/" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="period">{{ trans('checkUrls.frequency') }}</label>
                        <ul class="list-unstyled">
                            @foreach ([5, 10, 30, 60] as $time)
                            <li>
                                <div class="radio">
                                    <label for="period_{{ $time }}">
                                        <input type="radio" class="checkurl-period" name="period" id="checkurl_period_{{ $time }}" value="{{ $time }}" @if ($time === 30) checked @endif /> {{ trans('checkUrls.length', ['time' => $time]) }}
                                    </label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-delete"><i class="fa fa-trash"></i> {{ trans('app.delete') }}</button>
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> {{ trans('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
