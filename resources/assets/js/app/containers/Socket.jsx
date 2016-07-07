import React from 'react';
import { connect } from 'react-redux';

import SocketComponent from '../components/Socket';
import NAME from '../constants';

const Socket = (props) => (<SocketComponent {...props} />);

const mapStateToProps = (state) => ({
  online: state.getIn([NAME, 'socket', 'online']),
});

export default connect(mapStateToProps)(Socket);

