import React from 'react';
import { IndexLink } from 'react-router';

import NavBarMenu from '../containers/UserMenu';

const NavBar = () => {
  const strings = {
    title: Lang.get('app.name'),
    toggle: Lang.get('app.toggle_nav'),
  };

  return (
    <header className="main-header">
      <IndexLink to="/" className="logo"><b>{strings.title}</b></IndexLink>

      <nav className="navbar navbar-static-top" role="navigation">
        <a href="#" className="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span className="sr-only">{strings.toggle}</span>
        </a>

        <NavBarMenu />
      </nav>
    </header>
  );
};

export default NavBar;
