import React from 'react';
import { Link } from 'react-router';

import NavBarMenu from './NavBarMenu';

const NavBar = () => {
  const strings = {
    title: Lang.get('app.name'),
    toggle: Lang.get('app.toggle_nav'),
  };

  return (
    <header className="main-header">
      <Link to="/" className="logo"><b>{strings.title}</b></Link>

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
