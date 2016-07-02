import React, { PropTypes } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import NavBar from '../Components/NavBar';
import SideBar from '../Components/SideBar';
import Title from '../Components/Title';
import UpdateAlert from '../Components/Update';
import SocketError from '../Components/Socket';

const App = (props) => {
  const { children } = props;

  return (
    <div className="wrapper">
      <NavBar />
      <SideBar />

      <div className="content-wrapper">
        <section className="content-header">
          <Title />
        </section>
        <section className="content" id="app">
          <UpdateAlert />
          <SocketError />
          {children}
        </section>
      </div>
    </div>
  );
};

App.propTypes = {
  children: React.PropTypes.any,
};

export default App;

//
// const mapStateToProps = (state) => ({ });
// const mapDispatchToProps = (dispatch) => ({ });
//
// export default connect(
//   mapStateToProps,
//   mapDispatchToProps
// )(App);
