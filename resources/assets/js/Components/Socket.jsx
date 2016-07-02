import React from 'react';

const Socket = (props) => {
  const strings = {
    title: Lang.get('app.socket_error'),
    message: Lang.get('app.socket_error_info'),
  };

  return (
    <div className="alert alert-danger" id="socket_offline">
      <h4><i className="icon fa fa-ban"></i> {strings.title}</h4>
      {strings.message}
    </div>
  );
};

export default Socket;
