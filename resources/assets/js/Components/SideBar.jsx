import React from 'react';
import { Link } from 'react-router';

const SideBar = () => {
  const strings = {
    title: Lang.get('app.dashboard'),
    admin: Lang.get('app.admin'),
    projects: Lang.get('app.projects'),
    templates: Lang.get('app.templates'),
    groups: Lang.get('app.groups'),
    users: Lang.get('app.users'),
  };

  return (
    <aside className="main-sidebar">
      <section className="sidebar">
        <ul className="sidebar-menu">
          <li className="Request::is('/') ? 'active' : null">
            <Link to="/">
              <i className="fa fa-dashboard"></i>
              <span>{strings.title}</span>
            </Link>
          </li>

          <li className="treeview Request::is('admin/*') ? 'active' : null">
            <a href="#">
              <i className="fa fa-gear"></i>
              <span>{strings.admin}</span>
              <i className="fa fa-angle-left pull-right"></i>
            </a>
            <ul className="treeview-menu">
              <li className="Request::is('admin/projects') ? 'active' : null"><Link to="route('admin.projects.index')">{strings.projects}</Link></li>
              <li className="Request::is('admin/templates*') ? 'active' : null"><Link to="route('admin.templates.index')">{strings.templates}</Link></li>
              <li className="Request::is('admin/groups') ? 'active' : null"><Link to="route('admin.groups.index')">{strings.groups}</Link></li>
              <li className="Request::is('admin/users') ? 'active' : null"><Link to="route('admin.users.index')">{strings.users}</Link></li>
            </ul>
          </li>
        </ul>
      </section>
    </aside>

  );
};

export default SideBar;

/*

 @foreach($groups as $group)
 <li className="treeview $active_group === $group->id ? 'active' : null">
 <a href="#">
 <i className="fa fa-book"></i>
 <span id="sidebar_group_$group->id">$group->name</span>
 <i className="fa fa-angle-left pull-right"></i>
 </a>
 <ul className="treeview-menu" id="group_$group->id_projects">
 @foreach($group->projects as $project)
 <li className="$active_project === $project->id ? 'active' : null"><a href="route('projects', ['id' => $project->id])" id="sidebar_project_$project->id">$project->name</a></li>
 @endforeach
 </ul>
 </li>
 @endforeach
 */
