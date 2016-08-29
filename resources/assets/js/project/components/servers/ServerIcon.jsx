import React, { PropTypes } from 'react';

import Icon from '../../../app/components/Icon';

import {
  SERVER_STATUS_SUCCESSFUL,
  SERVER_STATUS_FAILED,
  SERVER_STATUS_TESTING,
} from '../../constants';

const ServerIcon = (props) => {
  const { status } = props;

  let spin = false;
  let fa = 'question';

  // Return the appropriate icon for the status
  if (status === SERVER_STATUS_SUCCESSFUL) {
    fa = 'check';
  } else if (status === SERVER_STATUS_TESTING) {
    fa = ['spinner', 'pulse'];
    spin = true;
  } else if (status === SERVER_STATUS_FAILED) {
    fa = 'warning';
  }

  return (<Icon fa={fa} spin={spin} />);
};

ServerIcon.propTypes = {
  status: PropTypes.number.isRequired,
};

export default ServerIcon;
