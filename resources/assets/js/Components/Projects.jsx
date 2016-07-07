import React, { PropTypes } from 'react';
import { Link } from 'react-router';

import Loading from './Loading';
import Box from './Box';

const Projects = (props) => {
  const {
    projects,
    fetching,
  } = props;

  const strings = {
    none: Lang.get('dashboard.no_projects'),
    title: Lang.get('dashboard.projects'),
    name: Lang.get('projects.name'),
    latest: Lang.choice('dashboard.latest', 1),
    status: Lang.get('dashboard.status'),
    view: Lang.get('dashboard.view'),
    site: Lang.get('dashboard.site'),
    never: Lang.get('app.never'),
  };

  if (!projects.length) {
    return (
      <Box title={strings.title} id="projects">
        <p>{fetching ? 'Loading...' : strings.none}</p>
        <Loading visible={fetching} />
      </Box>
    );
  }

  let groups = [];
  projects.forEach((group, index) => {
    let groupProjects = [];

    group.projects.forEach((project) => {
      const id = `project_${project.id}`;

      // FIXME: Add icon component and button component, use https://github.com/JedWatson/classnames for className
      groupProjects.push(
        <tr id={id} key={id}>
          <td><Link to={`/projects/${project.id}`} title={strings.view}>{project.name}</Link></td>
          <td>{project.last_run ? project.last_run : strings.never}</td>
          <td>
            <span className="label"><i className="fa"></i> <span>{group.readable_status}</span></span>
          </td>
          <td>
            <div className="btn-group pull-right">
              {project.url ? <a href={project.url} className="btn btn-default" title={strings.site} target="_blank"><i className="fa fa-globe"></i></a> : null}
              <Link to={`/projects/${project.id}`} className="btn btn-default" title={strings.view}><i className="fa fa-info-circle"></i></Link>
            </div>
          </td>
        </tr>
      );
    });

    // {{ $group_project->last_run ? $group_project->last_run->format('jS F Y g:i:s A') : 'Never' }}
    // <span class="label label-{{ $group_project->css_class }}"><i class="fa fa-{{ $group_project->icon }}"></i> <span>{{ $group_project->readable_status }}</span></span>

    groups.push(
      <Box title={group.group.name} key={index}>
        <table className="table table-responsive table-hover">
          <thead>
            <tr>
              <th>{strings.name}</th>
              <th>{strings.latest}</th>
              <th>{strings.status}</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>{groupProjects}</tbody>
        </table>
      </Box>
    );
  });

  return (<div>{groups}</div>);
};

Projects.propTypes = {
  fetching: PropTypes.bool.isRequired,
  projects: PropTypes.array.isRequired, // FIXME: Should be a shape object?
};

export default Projects;
