import React, { PropTypes } from 'react';

import {
  PROJECT_STATUS_FINISHED,
  PROJECT_STATUS_PENDING,
  PROJECT_STATUS_DEPLOYING,
  PROJECT_STATUS_FAILED,
  PROJECT_STATUS_NOT_DEPLOYED,
} from '../constants';

const ProjectStatus = (props) => {
  const { status } = props;
  let textStatus;

  switch (status) {
    case PROJECT_STATUS_FINISHED:
      textStatus = Lang.get('projects.finished');
      break;
    case PROJECT_STATUS_PENDING:
      textStatus = Lang.get('projects.pending');
      break;
    case PROJECT_STATUS_DEPLOYING:
      textStatus = Lang.get('projects.deploying');
      break;
    case PROJECT_STATUS_FAILED:
      textStatus = Lang.get('projects.failed');
      break;
    case PROJECT_STATUS_NOT_DEPLOYED:
    default:
      textStatus = Lang.get('projects.not_deployed');
      break;
  }

  return (
    <span>{textStatus}</span>
  );
};

ProjectStatus.propTypes = {
  status: PropTypes.number.isRequired,
};

export default ProjectStatus;
