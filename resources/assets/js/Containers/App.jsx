import React, { PropTypes, Component } from 'react';
import { connect } from 'react-redux';

import Tools from './DevTools';
import NavBar from '../Containers/NavBar';
import SideBar from '../Components/NavBar/SideBar';
import Title from '../Components/Title';
import SocketError from '../Components/Socket';
import Loading from '../Components/Loading';

import { appHasLoaded } from '../actions/app';

// Most of this is presentational so should be in a component!
class App extends Component {
  componentDidMount() {
    const dispatch = this.props.dispatch;

    setTimeout(() => {
      dispatch(appHasLoaded());
    }, 10000);
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
  loaded: state.get('app').get('loaded'),
});

export default connect(mapStateToProps)(App);
