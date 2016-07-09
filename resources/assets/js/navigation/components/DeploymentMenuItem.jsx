import React, { PropTypes } from 'react';
import { Link } from 'react-router';

const DeploymentMenuItem = (props) => {
  const { deployment } = props;

  const strings = {
    branch: Lang.get('deployments.branch'),
    started: Lang.get('dashboard.started'),
  };

  const id = `deployment_info_${deployment.id}`;
  const url = `/projects/${deployment.project_id}/deployments/${deployment.id}`;

  return (
    <li id={id}>
      <Link to={url}>
        <h4>
          {deployment.project_name}
          <small className="pull-right">{strings.started}: {deployment.started_at}</small>
        </h4>
        <p>{strings.branch}: {deployment.branch}</p>
      </Link>
    </li>
  );
};

DeploymentMenuItem.propTypes = {
  deployment: PropTypes.object.isRequired,
};

export default DeploymentMenuItem;
