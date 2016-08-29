import React, { PropTypes } from 'react';

import Icon from '../../../../app/components/Icon';

import {
  HEARTBEAT_STATUS_OK,
  HEARTBEAT_STATUS_MISSING,
} from '../../../constants';

const HeartBeatIcon = (props) => {
  const { status } = props;

  let fa = 'question';

  // Return the appropriate icon for the status
  if (status === HEARTBEAT_STATUS_OK) {
    fa = 'check';
  } else if (status === HEARTBEAT_STATUS_MISSING) {
    fa = 'warning';
  }

  return (<Icon fa={fa} />);
};

HeartBeatIcon.propTypes = {
  status: PropTypes.number.isRequired,
};

export default HeartBeatIcon;
