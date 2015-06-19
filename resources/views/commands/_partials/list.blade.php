<div class="col-md-6" id="commands-{{ strtolower($step) }}">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-code"></i> {{ Lang::get('commands.title', ['step' => $step]) }}</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-default" title="{{ Lang::get('commands.create') }}" data-step="{{ $action }}" data-toggle="modal" data-target="#command"><i class="fa fa-plus"></i> {{ Lang::get('commands.create') }}</button>
            </div>
        </div>

        <div class="box-body no-commands">
            <p>{{ Lang::get('commands.none') }}</p>
        </div>

        <div class="box-body table-responsive command-list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ Lang::get('commands.name') }}</th>
                        <th>{{ Lang::get('commands.run_as') }}</th>
                        <th>{{ Lang::get('commands.optional') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>