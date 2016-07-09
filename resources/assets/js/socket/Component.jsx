import React, { PropTypes } from 'react';

const Socket = (props) => {
  const { online } = props;

  const strings = {
    title: Lang.get('app.socket_error'),
    message: Lang.get('app.socket_error_info'),
  };

  if (online) {
    return null;
  }

  return (
    <div className="alert alert-danger" id="socket_offline">
      <h4><i className="icon fa fa-ban"></i> {strings.title}</h4>
      <span dangerouslySetInnerHTML={{ __html: strings.message }} />
    </div>
  );
};

Socket.propTypes = {
  online: PropTypes.bool,
};

Socket.defaultProps = {
  online: false,
};

export default Socket;
