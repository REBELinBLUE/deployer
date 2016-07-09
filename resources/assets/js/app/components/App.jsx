import React, { PropTypes } from 'react';

import Tools from '../containers/DevTools';
import NavBar from '../../navigation/components/NavBar';
import SideBar from '../../navigation/containers/SideBar';
import Title from '../containers/Title';
import SocketError from '../../socket/SocketContainer';

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
};

App.propTypes = {
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

export default App;
