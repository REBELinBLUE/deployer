import React, { PropTypes } from 'react';

import Icon from '../../../../app/components/Icon';

import {
  LINK_STATUS_SUCCESS,
  LINK_STATUS_FAILED,
} from '../../../constants';

const LinkIcon = (props) => {
  const { status } = props;

  let fa = 'question';

  // Return the appropriate icon for the status
  if (status === LINK_STATUS_SUCCESS) {
    fa = 'check';
  } else if (status === LINK_STATUS_FAILED) {
    fa = 'warning';
  }

  return (<Icon fa={fa} />);
};

LinkIcon.propTypes = {
  status: PropTypes.number.isRequired,
};

export default LinkIcon;
