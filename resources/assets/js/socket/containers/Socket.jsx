import React from 'react';
import { connect } from 'react-redux';

import SocketComponent from '../components/Socket';
import * as constants from '../constants';

const Socket = (props) => (<SocketComponent {...props} />);

const mapStateToProps = (state) => ({
  online: state.getIn([constants.NAME, 'online']),
});

export default connect(mapStateToProps)(Socket);

