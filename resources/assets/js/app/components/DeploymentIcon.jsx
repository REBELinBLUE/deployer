import React, { PropTypes } from 'react';

import Icon from './Icon';

import {
  DEPLOY_STATUS_COMPLETED,
  DEPLOY_STATUS_DEPLOYING,
  DEPLOY_STATUS_FAILED,
  DEPLOY_STATUS_COMPLETED_WITH_ERRORS,
  DEPLOY_STATUS_ABORTING,
  DEPLOY_STATUS_ABORTED,
} from '../constants';

const finishedStatuses = [
  DEPLOY_STATUS_FAILED,
  DEPLOY_STATUS_COMPLETED_WITH_ERRORS,
  DEPLOY_STATUS_ABORTED,
  DEPLOY_STATUS_ABORTING,
];

const DeploymentIcon = (props) => {
  const { status } = props;
  let className = 'bg-info';

  // Figure out the CSS classname to use based on the status (info by default)
  if (status === DEPLOY_STATUS_COMPLETED || status === DEPLOY_STATUS_COMPLETED_WITH_ERRORS) {
    className = 'bg-success';
  } else if ([DEPLOY_STATUS_FAILED, DEPLOY_STATUS_ABORTING, DEPLOY_STATUS_ABORTED].indexOf(status) !== -1) {
    className = 'bg-danger';
  } else if (status === DEPLOY_STATUS_DEPLOYING) {
    className = 'bg-warning';
  }

  // Return the appropriate icon for the status
  if (status === DEPLOY_STATUS_COMPLETED) {
    return (<Icon fa="check" className={className} />);
  } else if (finishedStatuses.indexOf(status) !== -1) {
    return (<Icon fa="warning" className={className} />);
  } else if (status === DEPLOY_STATUS_DEPLOYING) {
    return (<Icon fa={['spinner', 'pulse']} className={className} />);
  }

  return (<Icon fa="clock-o" className={className} />);
};

DeploymentIcon.propTypes = {
  status: PropTypes.number.isRequired,
};

export default DeploymentIcon;
