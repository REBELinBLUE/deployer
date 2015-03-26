<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Deployer</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />

        <!-- Style -->
        <link href="{{ elixir('css/style.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ elixir('css/admin.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <meta name="token" content="{{ Session::token() }}" />
    </head>
    <body class="skin-green">
        <div class="wrapper">

            @include('._partials.nav')

            @include('_partials.sidebar')

            <div class="content-wrapper">
                <section class="content-header">
                    <div class="pull-right">
                        @if (isset($is_dashboard)) 
                        <button type="button" class="btn btn-success" title="Add a new project" data-toggle="modal" data-target="#project"><span class="fa fa-plus"></span> Add Project</button>
                        @elseif (isset($is_project_details))
                        <form method="post" action="{{ route('deploy', ['id' => $project->id]) }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <button type="button" class="btn btn-default" title="View the public SSH sey" data-toggle="modal" data-target="#key"><span class="fa fa-key"></span> SSH key</button>
                            <button type="button" class="btn btn-default" title="Edit the project settings" data-project-id="{{ $project->id }}" data-toggle="modal" data-target="#project"><span class="fa fa-cogs"></span> Settings</button>
                            <button type="submit" class="btn btn-danger" title="Deploy the project" {{ ($project->isDeploying() OR !count($project->servers)) ? 'disabled' : '' }}><span class="fa fa-cloud-upload"></i> Deploy</button>
                        </form>
                        @endif
                    </div>

                    <h1>{{ $title }}</h1>

                    @if(isset($breadcrumb))
                    <ol class="breadcrumb">
                        @foreach($breadcrumb as $entry)
                        <li><a href="{{ $entry['url'] }}">{{ $entry['label'] }}</a></li>
                        @endforeach
                        <li class="active">{{ $title }}</li>
                    </ol>
                    @endif
                </section>

                <section class="content" id="app">
                    @yield('content')
                </section>
            </div>
        </div>

        <script src="{{ elixir('js/style.js') }}"></script>
        <script src="{{ elixir('js/admin.js') }}"></script>

        @yield('javascript')
    </body>
</html>