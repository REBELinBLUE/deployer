@extends('layout')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-magic"></i> Responsive Hover Table</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Server</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Finished</th>
                            <th>Duration</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <td>Server 1</td>
                            <td>
                                 <span class="label label-warning"><i class="fa fa-spinner"></i> <span>Running</span></span>
                            </td>
                            <td>3:06:05PM</td>
                            <td>3:06:08PM</td>
                            <td>3 seconds</td>
                            <td>
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default" title="View output" data-toggle="modal" data-backdrop="static" data-target="#log"><i class="fa fa-terminal"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('project._partials.dialogs.log')
@stop