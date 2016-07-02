import React, { PropTypes } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import NavBar from '../Components/NavBar';
import SideBar from '../Components/SideBar';
import Title from '../Components/Title';
import SocketError from '../Components/Socket';

const App = (props) => {
  const { children } = props;

  return (
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
  );
};

App.propTypes = {
  children: React.PropTypes.any,
};

export default App;
