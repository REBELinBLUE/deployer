<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="{{ Request::is('/') ? 'active' : null }}">
                <a href="/">
                    <i class="fa fa-dashboard"></i>
                    <span>{{ trans('app.dashboard') }}</span>
                </a>
            </li>

            @foreach($groups as $group)
            <li class="treeview {{ $active_group === $group->id ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span id="sidebar_group_{{ $group->id }}">{{ $group->name }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" id="group_{{ $group->id }}_projects">
                    @foreach($group->projects as $project)
                        <li class="{{ $active_project === $project->id ? 'active' : null }}"><a href="{{ route('projects', ['id' => $project->id]) }}" id="sidebar_project_{{ $project->id }}">{{ $project->name }}</a></li>
                    @endforeach
                </ul>
            </li>
            @endforeach

            <li class="treeview {{ Request::is('admin/*') ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-gear"></i>
                    <span>{{ trans('app.admin') }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/projects') ? 'active' : null }}"><a href="{{ route('admin.projects.index') }}">{{ trans('app.projects') }}</a></li>
                    <li class="{{ Request::is('admin/templates*') ? 'active' : null }}"><a href="{{ route('admin.templates.index') }}">{{ trans('app.templates') }}</a></li>
                    <li class="{{ Request::is('admin/servers') ? 'active' : null }}"><a href="{{ route('admin.servers.index') }}">{{ trans('app.servers') }}</a></li>
                    <li class="{{ Request::is('admin/groups') ? 'active' : null }}"><a href="{{ route('admin.groups.index') }}">{{ trans('app.groups') }}</a></li>
                    <li class="{{ Request::is('admin/users') ? 'active' : null }}"><a href="{{ route('admin.users.index') }}">{{ trans('app.users') }}</a></li>
                </ul>
            </li>
        </ul>
    </section>
</aside>
