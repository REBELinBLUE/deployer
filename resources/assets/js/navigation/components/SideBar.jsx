import React, { PropTypes } from 'react';
import { Link, IndexLink } from 'react-router';

import Icon from '../../app/components/Icon';
import Project from '../../models/Project';
import Group from '../../models/Group';

const SideBar = (props) => {
  const { projects } = props;

  const strings = {
    title: Lang.get('app.dashboard'),
    admin: Lang.get('app.admin'),
    projects: Lang.get('app.projects'),
    templates: Lang.get('app.templates'),
    groups: Lang.get('app.groups'),
    users: Lang.get('app.users'),
  };

  let navigation = [];
  projects.forEach((nav) => {
    const subNavigation = [];

    const group = nav.group;
    const groupProjects = nav.group.projects;

    groupProjects.forEach((project) => {
      let id = `sidebar_project_${project.id}`;
      subNavigation.push(
        <li key={id}><Link activeClassName="active" to={`/projects/${project.id}`} id={id}>{project.name}</Link></li>
      );
    });

    let id = `sidebar_group_${group.id}`;
    navigation.push(
      <li className="treeview" key={id}>
        <a href="#">
          <Icon fa="book" />
          <span id={id}>{group.name}</span>
          <Icon fa="angle-left" className="pull-right" />
        </a>
        <ul className="treeview-menu" id={`group_${group.id}_projects`}>{subNavigation}</ul>
      </li>
    );
  });

  return (
    <aside className="main-sidebar">
      <section className="sidebar">
        <ul className="sidebar-menu">
          <li>
            <IndexLink to="/">
              <Icon fa="dashboard" />
              <span>{strings.title}</span>
            </IndexLink>
          </li>

          {navigation}

          <li className="treeview">
            <a href="#">
              <Icon fa="gear" />
              <span>{strings.admin}</span>
              <Icon fa="angle-left" className="pull-right" />
            </a>
            <ul className="treeview-menu">
              <li><Link activeClassName="active" to="/admin/projects">{strings.projects}</Link></li>
              <li><Link activeClassName="active" to="/admin/templates">{strings.templates}</Link></li>
              <li><Link activeClassName="active" to="/admin/groups">{strings.groups}</Link></li>
              <li><Link activeClassName="active" to="/admin/users">{strings.users}</Link></li>
            </ul>
          </li>
        </ul>
      </section>
    </aside>
  );
};

SideBar.propTypes = {
  projects: PropTypes.arrayOf(PropTypes.shape({
    group: Group.isRequired,
    projects: PropTypes.arrayOf(Project).isRequired,
  })).isRequired,
};

export default SideBar;
