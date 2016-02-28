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
                <ul class="treeview-menu" id="group_{{ $group->id }}_projects">
                    @foreach($group->projects as $project)
                        <li class="{{ $active_project === $project->id ? 'active' : null }}"><a href="{{ route('projects', ['id' => $project->id]) }}" id="sidebar_project_{{ $project->id }}">{{ $project->name }}</a></li>
                    @endforeach
                </ul>
            </li>
            @endforeach

            @can('admin')
            <li class="treeview {{ Request::is('admin/*') ? 'active' : null }}">
                <a href="#">
                    <i class="fa fa-gear"></i>
                    <span>{{ Lang::get('app.admin') }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    @can('admin.projects')
                        <li class="{{ Request::is('admin/projects') ? 'active' : null }}"><a href="{{ route('admin.projects.index') }}">{{ Lang::get('app.projects') }}</a></li>
                    @endcan
                    @can('admin.templates')
                        <li class="{{ Request::is('admin/templates*') ? 'active' : null }}"><a href="{{  route('admin.templates.index') }}">{{ Lang::get('app.templates') }}</a></li>
                    @endcan
                    @can('admin.groups')
                        <li class="{{ Request::is('admin/groups') ? 'active' : null }}"><a href="{{ route('admin.groups.index') }}">{{ Lang::get('app.groups') }}</a></li>
                    @endcan
                    @can('admin.users')
                        <li class="{{ Request::is('admin/users') ? 'active' : null }}"><a href="{{ route('admin.users.index') }}">{{ Lang::get('app.users') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcan
        </ul>
    </section>
</aside>
