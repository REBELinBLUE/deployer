import React, { PropTypes } from 'react';

import Icon from './LinkIcon';
import Status from './LinkStatus';

import {
  LINK_STATUS_SUCCESS,
  LINK_STATUS_FAILED,
} from '../../../constants';

const LinkLabel = (props) => {
  const { status } = props;

  let className = 'primary';

  if (status === LINK_STATUS_SUCCESS) {
    className = 'success';
  } else if (status === LINK_STATUS_FAILED) {
    className = 'danger';
  }

  return (
    <span className={`label label-${className}`}>
      <Icon {...props} />&nbsp;<Status {...props} />
    </span>
  );
};

LinkLabel.propTypes = {
  status: PropTypes.number.isRequired,
};

export default LinkLabel;
