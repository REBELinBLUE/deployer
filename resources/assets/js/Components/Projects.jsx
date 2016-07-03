import React, { PropTypes } from 'react';
import { Link } from 'react-router';

import Loading from './Loading';

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
      <div className="box">
        <div className="box-header">
          <h3 className="box-title">{strings.title}</h3>
        </div>

        <div className="box-body">
          <p>{fetching ? 'Loading...' : strings.none}</p>
        </div>

        <Loading visible={fetching} />
      </div>
    );
  }

  let groups = [];
  projects.forEach((group, index) => {
    let groupProjects = [];

    group.projects.forEach((project) => {
      const id = `project_${project.id}`;

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
      <div className="box" key={index}>
        <div className="box-header">
          <h3 className="box-title">{group.group.name}</h3>
        </div>

        <div className="box-body table-responsive">
          <table className="table table-hover">
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
        </div>
      </div>
    );
  });

  return (<div>{groups}</div>);
};

Projects.propTypes = {
  fetching: PropTypes.bool.isRequired,
  projects: PropTypes.array.isRequired,
};

export default Projects;
