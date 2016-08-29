import React, { PropTypes } from 'react';

import {
  HEARTBEAT_STATUS_OK,
  HEARTBEAT_STATUS_MISSING,
  HEARTBEAT_STATUS_UNTESTED,
} from '../../../constants';

const HeartBeatStatus = (props) => {
  const { status } = props;
  let textStatus;

  switch (status) {
    case HEARTBEAT_STATUS_OK:
      textStatus = Lang.get('heartbeats.ok');
      break;
    case HEARTBEAT_STATUS_MISSING:
      textStatus = Lang.get('heartbeats.missing');
      break;
    case HEARTBEAT_STATUS_UNTESTED:
    default:
      textStatus = Lang.get('heartbeats.untested');
      break;
  }

  return (
    <span>{textStatus}</span>
  );
};

HeartBeatStatus.propTypes = {
  status: PropTypes.number.isRequired,
};

export default HeartBeatStatus;
