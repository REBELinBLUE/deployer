import React, { PropTypes } from 'react';

import {
  LINK_STATUS_SUCCESS,
  LINK_STATUS_FAILED,
} from '../../../constants';

const LinkStatus = (props) => {
  const { status } = props;
  let textStatus;

  switch (status) {
    case LINK_STATUS_SUCCESS:
      textStatus = Lang.get('checkUrls.successful');
      break;
    case LINK_STATUS_FAILED:
      textStatus = Lang.get('checkUrls.failed');
      break;
    default:
      textStatus = Lang.get('checkUrls.untested');
      break;
  }

  return (
    <span>{textStatus}</span>
  );
};

LinkStatus.propTypes = {
  status: PropTypes.number.isRequired,
};

export default LinkStatus;
