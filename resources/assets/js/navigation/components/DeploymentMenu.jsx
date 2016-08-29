import React, { PropTypes } from 'react';

import DeploymentMenuItem from './DeploymentMenuItem';
import Icon from '../../app/components/Icon';

const DeploymentMenu = (props) => {
  const {
    deployments,
    type,
  } = props;

  let icon = 'clock-o';
  let colour = 'label label-info';
  let id = 'pending_menu';
  let translation = 'dashboard.pending';

  if (type === 'running') {
    icon = 'spinner';
    colour = 'label label-warning';
    id = 'deploying_menu';
    translation = 'dashboard.running';
  }

  const label = Lang.choice(translation, deployments.length, { count: deployments.length });

  if (!deployments.length) {
    return null;
  }

  return (
    <li className="dropdown messages-menu" id={id}>
      <a href="#" className="dropdown-toggle" data-toggle="dropdown">
        <Icon fa={icon} />
        <span className={colour}>{deployments.length}</span>
      </a>
      <ul className="dropdown-menu">
        <li className="header">{label}</li>
        <li>
          <ul className="menu">
            {
              deployments.map((deployment, index) => (
                <DeploymentMenuItem key={index} deployment={deployment} />
              ))
            }
          </ul>
        </li>
      </ul>
    </li>
  );
};

DeploymentMenu.propTypes = {
  type: PropTypes.oneOf(['pending', 'running']).isRequired,
  deployments: PropTypes.array.isRequired,
};

export default DeploymentMenu;
