<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="{{ Request::is('/') ? 'active' : null }}">
                <a href="/">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="treeview {{ (Request::is('projects/*') OR Request::is('deployment/*')) ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Projects</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    @foreach(App\Project::all() as $project)
                        <li><a href="{{ url('projects', $project->id) }}">{{ $project->name }}</a></li>
                    @endforeach
                </ul>
            </li>

            <li class="treeview {{ Request::is('admin/*') ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-gear"></i>
                    <span>Administration</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#">Projects</a></li>
                    <li><a href="{{ url('admin/users') }}">Users</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </li>
        </ul>
    </section>
</aside>
