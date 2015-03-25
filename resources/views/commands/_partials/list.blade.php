<div class="col-md-6" id="commands-{{ strtolower($step) }}">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-code"></i> {{ $step }} Commands</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-default" title="Add a new {{ strtolower($step) }} command" data-step="{{ $step }} {{ $action }}" data-toggle="modal" data-target="#command"><i class="fa fa-plus"></i> Add Command</button>
            </div>
        </div>

        <div class="box-body no-commands">
            <p>No commands have been configured</p>
        </div>

        <div class="box-body table-responsive command-list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="50">&nbsp;</th>
                        <th>Name</th>
                        <th>Run As</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>