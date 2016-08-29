import React, { PropTypes } from 'react';
import { Link } from 'react-router';

import PendingMenu from '../containers/PendingMenu';
import RunningMenu from '../containers/RunningMenu';

const UserMenu = (props) => {
  const { user } = props;

  const strings = {
    profile: Lang.get('users.profile'),
    signout: Lang.get('app.signout'),
  };

  // fixme: the form here needs the CSRF token
  return (
    <div className="navbar-custom-menu">
      <ul className="nav navbar-nav">
        <PendingMenu />
        <RunningMenu />

        <li className="dropdown user user-menu">
          <a href="#" className="dropdown-toggle" data-toggle="dropdown">
            <img src={user.avatar_url} className="user-image" alt="" />
            <span className="hidden-xs">{user.name}</span>
          </a>

          <ul className="dropdown-menu">
            <li className="user-header">
              <img src={user.avatar_url} className="img-circle" alt="" />
              <p>{user.name}</p>
            </li>
            <li className="user-footer">
              <div className="pull-left">
                <Link to="/profile" className="btn btn-default btn-flat">{strings.profile}</Link>
              </div>
              <div className="pull-right">
                <form method="post" action="/logout">
                  <button type="submit" className="btn btn-default btn-flat">{strings.signout}</button>
                </form>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  );
};

UserMenu.propTypes = {
  user: PropTypes.object.isRequired,
};

export default UserMenu;
