import React, { PropTypes } from 'react';

import Icon from './ServerIcon';
import Status from './ServerStatus';

import {
  SERVER_STATUS_SUCCESSFUL,
  SERVER_STATUS_FAILED,
  SERVER_STATUS_TESTING,
} from '../../constants';

const ServerLabel = (props) => {
  const { status } = props;

  let className = 'primary';

  if (status === SERVER_STATUS_SUCCESSFUL) {
    className = 'success';
  } else if (status === SERVER_STATUS_TESTING) {
    className = 'warning';
  } else if (status === SERVER_STATUS_FAILED) {
    className = 'danger';
  }

  return (
    <span className={`label label-${className}`}>
      <Icon {...props} />&nbsp;<Status {...props} />
    </span>
  );
};

ServerLabel.propTypes = {
  status: PropTypes.number.isRequired,
};

export default ServerLabel;
