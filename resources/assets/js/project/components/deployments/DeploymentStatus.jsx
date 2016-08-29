import React, { PropTypes } from 'react';

import {
  DEPLOY_STATUS_COMPLETED,
  DEPLOY_STATUS_DEPLOYING,
  DEPLOY_STATUS_FAILED,
  DEPLOY_STATUS_COMPLETED_WITH_ERRORS,
  DEPLOY_STATUS_ABORTING,
  DEPLOY_STATUS_ABORTED,
  DEPLOY_STATUS_PENDING,
} from '../../constants';

const DeploymentStatus = (props) => {
  const { status } = props;
  let textStatus;

  switch (status) {
    case DEPLOY_STATUS_COMPLETED:
      textStatus = Lang.get('deployments.completed');
      break;
    case DEPLOY_STATUS_COMPLETED_WITH_ERRORS:
      textStatus = Lang.get('deployments.completed_with_errors');
      break;
    case DEPLOY_STATUS_ABORTING:
      textStatus = Lang.get('deployments.aborting');
      break;
    case DEPLOY_STATUS_ABORTED:
      textStatus = Lang.get('deployments.aborted');
      break;
    case DEPLOY_STATUS_FAILED:
      textStatus = Lang.get('deployments.failed');
      break;
    case DEPLOY_STATUS_DEPLOYING:
      textStatus = Lang.get('deployments.deploying');
      break;
    case DEPLOY_STATUS_PENDING:
    default:
      textStatus = Lang.get('deployments.pending');
      break;
  }

  return (
    <span>{textStatus}</span>
  );
};

DeploymentStatus.propTypes = {
  status: PropTypes.number.isRequired,
};

export default DeploymentStatus;
