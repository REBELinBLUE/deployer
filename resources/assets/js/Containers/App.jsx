import React, { PropTypes } from 'react';

import Tools from './DevTools';
import NavBar from '../Containers/NavBar';
import SideBar from '../Components/SideBar';
import Title from '../Components/Title';
import SocketError from '../Components/Socket';

const App = (props) => {
  const { children } = props;

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
  children: PropTypes.any,
};

export default App;
