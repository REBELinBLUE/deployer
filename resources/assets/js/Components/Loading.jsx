import React, { PropTypes } from 'react';

const Loading = (props) => {
  if (!props.visible) {
    return null;
  }

  return (
    <div className="overlay">
      <i className="fa fa-refresh fa-spin"></i>
    </div>
  );
};

Loading.propTypes = {
  visible: PropTypes.bool.isRequired,
};

export default Loading;

