import React, { PropTypes } from 'react';

import Icon from './ProjectIcon';
import Status from './ProjectStatus';

import {
  PROJECT_STATUS_FINISHED,
  PROJECT_STATUS_PENDING,
  PROJECT_STATUS_DEPLOYING,
  PROJECT_STATUS_FAILED,
} from '../constants';

const ProjectLabel = (props) => {
  const { status } = props;

  let className = 'primary';

  if (status === PROJECT_STATUS_FINISHED) {
    className = 'success';
  } else if (status === PROJECT_STATUS_DEPLOYING) {
    className = 'warning';
  } else if (status === PROJECT_STATUS_FAILED) {
    className = 'danger';
  } else if (status === PROJECT_STATUS_PENDING) {
    className = 'info';
  }

  return (
    <span className={`label label-${className}`}>
      <Icon {...props} />&nbsp;<Status {...props} />
    </span>
  );
};

ProjectLabel.propTypes = {
  status: PropTypes.number.isRequired,
};

export default ProjectLabel;
