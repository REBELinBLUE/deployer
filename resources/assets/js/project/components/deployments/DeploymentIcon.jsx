import React, { PropTypes } from 'react';

import Icon from '../../../app/components/Icon';

import {
  DEPLOY_STATUS_COMPLETED,
  DEPLOY_STATUS_DEPLOYING,
  DEPLOY_STATUS_FAILED,
  DEPLOY_STATUS_COMPLETED_WITH_ERRORS,
  DEPLOY_STATUS_ABORTING,
  DEPLOY_STATUS_ABORTED,
} from '../../constants';

const finishedStatuses = [
  DEPLOY_STATUS_FAILED,
  DEPLOY_STATUS_COMPLETED_WITH_ERRORS,
  DEPLOY_STATUS_ABORTED,
  DEPLOY_STATUS_ABORTING,
];

const DeploymentIcon = (props) => {
  const {
    status, includeBackground,
  } = props;

  let className = '';

  if (includeBackground) {
    className = 'bg-aqua';

    // Figure out the CSS class name to use based on the status (info by default)
    if (status === DEPLOY_STATUS_COMPLETED || status === DEPLOY_STATUS_COMPLETED_WITH_ERRORS) {
      className = 'bg-green';
    } else if ([DEPLOY_STATUS_FAILED, DEPLOY_STATUS_ABORTING, DEPLOY_STATUS_ABORTED].indexOf(status) !== -1) {
      className = 'bg-red';
    } else if (status === DEPLOY_STATUS_DEPLOYING) {
      className = 'bg-yellow';
    }
  }

  let spin = false;
  let fa = 'clock-o';

  // Return the appropriate icon for the status
  if (status === DEPLOY_STATUS_COMPLETED) {
    fa = 'check';
  } else if (finishedStatuses.indexOf(status) !== -1) {
    fa = 'warning';
  } else if (status === DEPLOY_STATUS_DEPLOYING) {
    fa = ['spinner', 'pulse'];
    spin = true;
  }

  return (<Icon fa={fa} className={className} spin={spin} />);
};

DeploymentIcon.propTypes = {
  status: PropTypes.number.isRequired,
  includeBackground: PropTypes.bool,
};

DeploymentIcon.defaultProps = {
  includeBackground: false,
};

export default DeploymentIcon;
