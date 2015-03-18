<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Deployer</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />

        <!-- Style -->
        <link href="{{ asset('/css/style.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/css/admin.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-green">
        <div class="wrapper">

            @include('._partials.nav')

            @include('_partials.sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="pull-right">
                        @if (isset($is_dashboard)) 
                        <button type="button" class="btn btn-success" title="Add new project" data-toggle="modal" data-target="#project"><span class="fa fa-plus"></span> Add Project</button>
                        @elseif (isset($is_project_details))
                        <button type="button" class="btn btn-default" title="View SSH Key" data-toggle="modal" data-target="#key"><span class="fa fa-key"></span> SSH key</button>
                        <button type="button" class="btn btn-default" title="Edit Project Settings" data-toggle="modal" data-target="#project"><span class="fa fa-cogs"></span> Settings</button>
                        <a href="{{ route('deploy', ['id' => $project->id]) }}" class="btn btn-danger" title="Deploy"><span class="fa fa-cloud-upload"></i> Deploy</a>
                        @endif
                    </div>

                    <h1>{{ $title }}</h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    @yield('content')
                </section><!-- /.content -->

            </div><!-- /.content-wrapper -->

        </div><!-- ./wrapper -->

        <!-- jQuery 2.1.3 -->
        <script src="{{ asset('/js/style.js') }}"></script>
        <script src="{{ asset('/js/admin.js') }}"></script>
    </body>
</html>