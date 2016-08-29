import React, { PropTypes } from 'react';

import Icon from './HeartBeatIcon';
import Status from './HeartBeatStatus';

import {
  HEARTBEAT_STATUS_OK,
  HEARTBEAT_STATUS_MISSING,
} from '../../../constants';

const HeartBeatLabel = (props) => {
  const { status } = props;

  let className = 'primary';

  if (status === HEARTBEAT_STATUS_OK) {
    className = 'success';
  } else if (status === HEARTBEAT_STATUS_MISSING) {
    className = 'danger';
  }

  return (
    <span className={`label label-${className}`}>
      <Icon {...props} />&nbsp;<Status {...props} />
    </span>
  );
};

HeartBeatLabel.propTypes = {
  status: PropTypes.number.isRequired,
};

export default HeartBeatLabel;
