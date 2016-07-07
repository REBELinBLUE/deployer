import React from 'react';
import { connect } from 'react-redux';

import SocketComponent from '../components/Socket';

const Socket = (props) => (<SocketComponent {...props} />);

const mapStateToProps = (state) => ({
  online: state.getIn(['app', 'socket', 'online']),
});

export default connect(mapStateToProps)(Socket);

