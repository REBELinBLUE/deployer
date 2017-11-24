<header class="main-header">
    <a href="/" class="logo"><b>{{ trans('app.name') }}</b></a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">{{ trans('app.toggle_nav') }}</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li class="dropdown messages-menu" id="pending_menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-clock-o"></i>
                        <span class="label label-info">{{ $pending_count }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">{{ trans_choice('dashboard.pending', $pending_count, ['count' => $pending_count]) }}</li>
                        <li>
                            <ul class="menu">
                                @foreach ($pending as $deployment)
                                    <li id="deployment_info_{{ $deployment->id }}">
                                        <a href="{{ route('deployments', ['id' => $deployment->id]) }}">
                                            <h4>{{ $deployment->project->name }} <small class="pull-right">{{ trans('dashboard.started') }}: {{ $deployment->started_at->format('g:i:s A') }}</small></h4>
                                            <p>{{ trans('deployments.branch') }}: {{ $deployment->branch }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="dropdown messages-menu" id="running_menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-spinner"></i>
                        <span class="label label-warning">{{ $deploying_count }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">{{ trans_choice('dashboard.running', $deploying_count, ['count' => $deploying_count]) }}</li>
                        <li>
                            <ul class="menu">
                                @foreach ($deploying as $deployment)
                                    <li id="deployment_info_{{ $deployment->id }}">
                                        <a href="{{ route('deployments', ['id' => $deployment->id]) }}">
                                            <h4>{{ $deployment->project->name }} <small class="pull-right">{{ trans('dashboard.started') }}: {{ $deployment->started_at->format('g:i:s A') }}</small></h4>
                                            <p>{{ trans('deployments.branch') }}: {{ $deployment->branch }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ $logged_in_user->avatar_url }}" class="user-image" />
                        <span class="hidden-xs">{{ $logged_in_user->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="{{ $logged_in_user->avatar_url }}" class="img-circle" />
                            <p>{{ $logged_in_user->name }}</p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('profile.index') }}" class="btn btn-default btn-flat">{{ trans('users.profile') }}</a>
                            </div>
                            <form method="post" action="{{ route('auth.logout') }}" class="pull-right">
                                <button type="submit" class="btn btn-default btn-flat">{{ trans('app.signout') }}</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

@push('templates')
    <script type="text/template" id="deployment-list-template">
        <li id="deployment_info_<%- id %>">
            <a href="<%- url %>">
                <h4><%- project_name %> <small class="pull-right">{{ trans('dashboard.started') }}: <%- time %></small></h4>
                <p>{{ trans('deployments.branch') }}: <%- branch %></p>
            </a>
        </li>
    </script>
@endpush
