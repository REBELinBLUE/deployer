<header class="main-header">
    <a href="/" class="logo"><b>{{ Lang::get('app.name') }}</b></a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ Lang::get('app.toggle_nav') }}</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li class="dropdown messages-menu" id="pending_menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-clock-o"></i>
                        <span class="label label-info">{{ $pending_count }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">{{ Lang::choice('dashboard.pending', $pending_count, ['count' => $pending_count]) }}</li>
                        <li>
                            <ul class="menu">
                                @foreach ($pending as $deployment)
                                    <li id="deployment_info_{{ $deployment->id }}">
                                        <a href="{{ route('deployment', ['id' => $deployment->id]) }}">
                                            <h4>{{ $deployment->project->name }} <small class="pull-right">{{ Lang::get('dashboard.started') }}: {{ $deployment->started_at->format('g:i:s A') }}</small></h4>
                                            <p>{{ Lang::get('deployments.branch') }}: {{ $deployment->branch }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="dropdown messages-menu" id="deploying_menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-spinner"></i>
                        <span class="label label-warning">{{ $deploying_count }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">{{ Lang::choice('dashboard.running', $deploying_count, ['count' => $deploying_count]) }}</li>
                        <li>
                            <ul class="menu">
                                @foreach ($deploying as $deployment)
                                    <li id="deployment_info_{{ $deployment->id }}">
                                        <a href="{{ route('deployment', ['id' => $deployment->id]) }}">
                                            <h4>{{ $deployment->project->name }} <small class="pull-right">{{ Lang::get('dashboard.started') }}: {{ $deployment->started_at->format('g:i:s A') }}</small></h4>
                                            <p>{{ Lang::get('deployments.branch') }}: {{ $deployment->branch }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </li>


                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ Gravatar::get(Auth::user()->email) }}" class="user-image" />
                        <span class="hidden-xs">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">

                        <li class="user-header">
                            <img src="{{ Gravatar::get(Auth::user()->email) }}" class="img-circle" />
                            <p>
                                {{ Auth::user()->name }}
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="{{ url('auth/logout') }}" class="btn btn-default btn-flat">{{ Lang::get('app.signout') }}</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<script type="text/template" id="deployment_list_template">
    <li id="deployment_info_<%- id %>">
        <a href="<%- url %>">
            <h4><%- project_name %> <small class="pull-right">{{ Lang::get('dashboard.started') }}: <%- time %></small></h4>
            <p>{{ Lang::get('deployments.branch') }}: <%- branch %></p>
        </a>
    </li>
</script>