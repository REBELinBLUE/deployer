@extends('layout')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Projects</h3>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Repository</th>
                        <th>Latest Deployment</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="/project/1">Project name</a></td>
                        <td>....</td>
                        <td>March 17th, 10:35:34 AM</td>
                        <td>
                            <span class="label label-warning"><i class="fa fa-spinner"></i> Running</span>
                        </td>
                        <td>
                            <div class="btn-group pull-right">
                                <a href="#" class="btn btn-default btn-flat" title="View Site"><i class="fa fa-globe"></i></a>
                                <a href="/project/1" class="btn btn-default btn-flat" title="View Details"><i class="fa fa-info-circle"></i></a> <!-- details -->
                            </div><!-- /.btn-group -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop