import React, { PropTypes } from 'react';

import Icon from './DeploymentIcon';
import Status from './DeploymentStatus';

import {
  DEPLOY_STATUS_COMPLETED,
  DEPLOY_STATUS_DEPLOYING,
  DEPLOY_STATUS_FAILED,
  DEPLOY_STATUS_COMPLETED_WITH_ERRORS,
  DEPLOY_STATUS_ABORTING,
  DEPLOY_STATUS_ABORTED,
} from '../../constants';

const DeploymentLabel = (props) => {
  const { status } = props;

  let className = 'info';

  if (status === DEPLOY_STATUS_COMPLETED || status === DEPLOY_STATUS_COMPLETED_WITH_ERRORS) {
    className = 'success';
  } else if ([DEPLOY_STATUS_FAILED, DEPLOY_STATUS_ABORTING, DEPLOY_STATUS_ABORTED].indexOf(status) !== -1) {
    className = 'danger';
  } else if (status === DEPLOY_STATUS_DEPLOYING) {
    className = 'warning';
  }

  return (
    <span className={`label label-${className}`}>
      <Icon {...props} />&nbsp;<Status {...props} />
    </span>
  );
};

DeploymentLabel.propTypes = {
  status: PropTypes.number.isRequired,
};

export default DeploymentLabel;
