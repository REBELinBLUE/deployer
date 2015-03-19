<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>USERNAME</p>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="{{ Request::is('/') ? 'active' : null }}">
                <a href="/">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="treeview {{ (Request::is('project/*') OR Request::is('deployment/*')) ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Projects</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    @foreach(App\Project::all() as $project)
                        <li><a href="{{ route('project', $project->id) }}"><i class="fa fa-{{ project_icon_status($project) }} text-{{ project_css_status($project) }}"></i> {{ $project->name }}</a></li>
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
                    <li><a href="#"><i class="fa fa-circle-o"></i> General</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Icons</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Buttons</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Sliders</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Timeline</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Modals</a></li>
                </ul>
            </li>
        </ul>
    </section>
</aside>
