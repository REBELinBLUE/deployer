<div class="col-md-6" id="commands-{{ strtolower($step) }}_list">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-code"></i> {{ trans('commands.title', ['step' => $step]) }}</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-default" title="{{ trans('commands.create') }}" data-step="{{ $action }}" data-toggle="modal" data-target="#command"><i class="fa fa-plus"></i> {{ trans('commands.create') }}</button>
            </div>
        </div>

        <div class="box-body no-commands">
            <p>{{ trans('commands.none') }}</p>
        </div>

        <div class="box-body table-responsive command-list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('commands.name') }}</th>
                        <th>{{ trans('commands.run_as') }}</th>
                        <th>{{ trans('commands.optional') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
