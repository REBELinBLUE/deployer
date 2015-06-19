<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="{{ Request::is('/') ? 'active' : null }}">
                <a href="/">
                    <i class="fa fa-dashboard"></i>
                    <span>{{ Lang::get('app.dashboard') }}</span>
                </a>
            </li>

            @foreach($groups as $group)
            <li class="treeview {{ $active_group === $group->id ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span id="sidebar_group_{{ $group->id }}">{{ $group->name }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    @foreach($group->projects as $project)
                        <li class="{{ $active_project === $project->id ? 'active' : null }}"><a href="{{ url('projects', $project->id) }}" id="sidebar_project_{{ $project->id }}">{{ $project->name }}</a></li>
                    @endforeach
                </ul>
            </li>
            @endforeach

            <li class="treeview {{ Request::is('admin/*') ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-gear"></i>
                    <span>{{ Lang::get('app.admin') }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/projects') ? 'active' : null }}"><a href="{{ url('admin/projects') }}">{{ Lang::get('app.projects') }}</a></li>
                    <li class="{{ Request::is('admin/templates*') ? 'active' : null }}"><a href="{{ url('admin/templates') }}">{{ Lang::get('app.templates') }}</a></li>
                    <li class="{{ Request::is('admin/groups') ? 'active' : null }}"><a href="{{ url('admin/groups') }}">{{ Lang::get('app.groups') }}</a></li>
                    <li class="{{ Request::is('admin/users') ? 'active' : null }}"><a href="{{ url('admin/users') }}">{{ Lang::get('app.users') }}</a></li>
                </ul>
            </li>
        </ul>
    </section>
</aside>
