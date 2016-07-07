import React, { PropTypes, Component } from 'react';
import { connect } from 'react-redux';

import Tools from './DevTools';
import NavBar from '../../navigation/containers/NavBar'; // fixme: should not be accessing other module
import SideBar from '../../navigation/components/SideBar';
import Title from './Title';
import SocketError from '../../socket/SocketContainer';
import Loading from '../components/Loading';

import * as constants from '../constants';
import * as actions from '../actions';

// Most of this is presentational so should be in a component!
class App extends Component {
  componentDidMount() {
    const dispatch = this.props.dispatch;

    setTimeout(() => {
      dispatch(actions.appHasLoaded());
    }, 2000);
  }

  render() {
    const {
      children,
      loaded,
    } = this.props;

    if (!loaded) {
      return (
        <div>
          <div className="wrapper">
            <NavBar />

            <div className="content-wrapper">
              <Loading visible />
            </div>
          </div>

          <Tools />
        </div>
      );
    }

    return (
      <div>
        <div className="wrapper">
          <NavBar />

          <SideBar />

          <div className="content-wrapper">
            <Title />
            <section className="content" id="app">
              <SocketError />
              {children}
            </section>
          </div>
        </div>

        <Tools />
      </div>
    );
  }
}

App.propTypes = {
  children: PropTypes.any,
  dispatch: PropTypes.func.isRequired,
  loaded: PropTypes.bool.isRequired,
};

const mapStateToProps = (state) => ({
  loaded: state.getIn([constants.NAME, 'loaded']),
});

export default connect(mapStateToProps)(App);
