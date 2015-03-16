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
                        <button type="button" class="btn btn-default btn-flat" title="Settings"><span class="fa fa-cogs"></span> Settings</button>
                        <button type="button" class="btn btn-danger btn-flat" title="Deploy"><span class="fa fa-cloud-upload"></i> Deploy</button>
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