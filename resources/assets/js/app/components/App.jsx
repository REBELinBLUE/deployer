import React, { PropTypes } from 'react';

import Tools from '../containers/DevTools';
import NavBar from '../../navigation/components/NavBar';
import SocketError from '../../socket/containers/Socket';
import SideBar from '../../navigation/containers/SideBar';
import Header from './Header';

const App = (props) => {
  const {
    children,
  } = props;

  return (
    <div>
      <div className="wrapper">
        <NavBar />

        <SideBar />

        <div className="content-wrapper">
          <Header />
          <section className="content" id="app">
            <SocketError />
            {children}
          </section>
        </div>
      </div>

      <Tools />
    </div>
  );
};

App.propTypes = {
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

export default App;
