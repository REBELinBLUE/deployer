<div class="box">
    <div class="box-header">
        <h3 class="box-title">Commands</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Step</th>
                    <th>Before</th>
                    <th>After</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ deploy_step_label('Clone') }}</td>
                    <td>None</td>
                    <td>None</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="/project/1/commands/clone" class="btn btn-default" title="Configure"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ deploy_step_label('Install') }}</td>
                    <td>None</td>
                    <td>None</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="/project/1/commands/install" class="btn btn-default" title="Configure"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ deploy_step_label('Activate') }}</td>
                    <td>None</td>
                    <td>None</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="/project/1/commands/activate" class="btn btn-default" title="Configure"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ deploy_step_label('Purge') }}</td>
                    <td>None</td>
                    <td>None</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="/project/1/commands/purge" class="btn btn-default" title="Configure"><i class="fa fa-gear"></i></a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>