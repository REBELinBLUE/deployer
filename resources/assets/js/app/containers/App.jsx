import React, { Component, PropTypes } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import client from 'socket.io-client';

import { online, offline } from '../../socket/actions';
import * as socket from '../../socket/constants';
import AppComponent from '../components/App';

class App extends Component {
  constructor(props) {
    super(props);
    this.socket = null;
  }

  getChildContext() {
    return {
      socket: this.socket,
    };
  }

  componentDidMount() {
    const {
      actions,
      server,
      jwt,
    } = this.props;

    this.socket = client.connect(server, {
      query: `jwt=${jwt}`,
      transports: ['websocket', 'polling'],
    });

    this.socket.on('connect_error', (error) => actions.offline(error));
    this.socket.on('connect', () => actions.online());
    this.socket.on('reconnect', () => actions.online());
  }

  render() {
    const children = this.props.children;

    return (<AppComponent>{children}</AppComponent>);
  }
}

App.propTypes = {
  server: PropTypes.string.isRequired,
  jwt: PropTypes.string.isRequired,
  actions: PropTypes.object.isRequired,
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

App.childContextTypes = {
  socket: React.PropTypes.object,
};

const mapStateToProps = (state) => ({
  server: state.getIn([socket.NAME, 'server']),
  jwt: state.getIn([socket.NAME, 'jwt']),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    offline,
    online,
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(App);
