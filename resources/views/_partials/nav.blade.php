<header class="main-header">
    <a href="/" class="logo"><b>{{ Lang::get('app.name') }}</b></a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ Lang::get('app.toggle_nav') }}</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
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