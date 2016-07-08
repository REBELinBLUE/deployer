import React, { PropTypes } from 'react';
import { Link } from 'react-router';

const ProjectMenuItem = (props) => {
  const { project } = props;

  const strings = {
    branch: Lang.get('deployments.branch'),
    started: Lang.get('dashboard.started'),
  };

  const id = `deployment_info_${project.id}`;
  const url = `/projects/${project.id}`;

  return (
    <li id={id}>
      <Link to={url}>
        <h4>{project.project_name} <small className="pull-right">{strings.started}: {project.started_at}</small></h4>
        <p>{strings.branch}: {project.branch}</p>
      </Link>
    </li>
  );
};

ProjectMenuItem.propTypes = {
  project: PropTypes.object.isRequired,
};

export default ProjectMenuItem;
