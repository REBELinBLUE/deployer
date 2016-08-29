import React, { PropTypes } from 'react';
import { Link } from 'react-router';

import Box from '../../app/components/Box';
import Label from './ProjectLabel';
import Icon from '../../app/components/Icon';
import FormattedDateTime from '../../app/components/DateTime';
import Project from '../../models/Project';
import Group from '../../models/Group';

const Projects = (props) => {
  const {
    projects,
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
        <p>{strings.none}</p>
      </Box>
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
          <td>{project.last_run ? <FormattedDateTime date={project.last_run} /> : strings.never}</td>
          <td><Label status={project.status} /></td>
          <td>
            <div className="btn-group pull-right">
              {
                project.url ?
                  <a
                    href={project.url}
                    className="btn btn-default"
                    title={strings.site}
                    target="_blank"
                  ><Icon fa="globe" /></a>
                  :
                  null
              }
              <Link to={`/projects/${project.id}`} className="btn btn-default" title={strings.view}>
                <Icon fa="info-circle" />
              </Link>
            </div>
          </td>
        </tr>
      );
    });

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
  projects: PropTypes.arrayOf(PropTypes.shape({
    group: Group.isRequired,
    projects: PropTypes.arrayOf(Project).isRequired,
  })).isRequired,
};

export default Projects;
