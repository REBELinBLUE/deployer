import React, { PropTypes } from 'react';

import {
  SERVER_STATUS_SUCCESSFUL,
  SERVER_STATUS_FAILED,
  SERVER_STATUS_TESTING,
  SERVER_STATUS_UNTESTED,
} from '../../constants';

const ServerStatus = (props) => {
  const { status } = props;
  let textStatus;

  switch (status) {
    case SERVER_STATUS_SUCCESSFUL:
      textStatus = Lang.get('servers.successful');
      break;
    case SERVER_STATUS_FAILED:
      textStatus = Lang.get('servers.failed');
      break;
    case SERVER_STATUS_TESTING:
      textStatus = Lang.get('servers.testing');
      break;
    case SERVER_STATUS_UNTESTED:
    default:
      textStatus = Lang.get('servers.untested');
      break;
  }

  return (
    <span>{textStatus}</span>
  );
};

ServerStatus.propTypes = {
  status: PropTypes.number.isRequired,
};

export default ServerStatus;
