import React, { PropTypes } from 'react';

import Icon from './Icon';

const Loading = (props) => {
  if (!props.visible) {
    return null;
  }

  return (
    <div className="overlay">
      <Icon fa="refresh" spin />
    </div>
  );
};

Loading.propTypes = {
  visible: PropTypes.bool.isRequired,
};

export default Loading;

