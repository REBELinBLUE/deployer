import React, { PropTypes } from 'react';
import { Link } from 'react-router';
import { connect } from 'react-redux';
import jQuery from 'jquery';

import PendingMenu from './PendingMenu';
import RunningMenu from './RunningMenu';

import { receivedProjects } from '../../Actions/app';

class NavBarMenu extends React.Component {
  componentDidMount() {
    this.loadData();
  }

  loadData() {
    let { dispatch } = this.props;

    jQuery.get('/running').done((response) => {
      dispatch(receivedProjects(response));
    }).fail((error) => {
      console.log(error);
    });
  }

  render() {
    const { user } = this.props;

    const strings = {
      profile: Lang.get('users.profile'),
      signout: Lang.get('app.signout'),
    };

    return (
      <div className="navbar-custom-menu">
        <ul className="nav navbar-nav">
          <PendingMenu />
          <RunningMenu />

          <li className="dropdown user user-menu">
            <a href="#" className="dropdown-toggle" data-toggle="dropdown">
              <img src={user.avatar_url} className="user-image" alt=""/>
              <span className="hidden-xs">{user.name}</span>
            </a>

            <ul className="dropdown-menu">
              <li className="user-header">
                <img src={user.avatar_url} className="img-circle" alt=""/>
                <p>{user.name}</p>
              </li>
              <li className="user-footer">
                <div className="pull-left">
                  <Link to="/profile" className="btn btn-default btn-flat">{strings.profile}</Link>
                </div>
                <div className="pull-right">
                  <a href="/logout" className="btn btn-default btn-flat">{strings.signout}</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    );
  }
}

NavBarMenu.propTypes = {
  user: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => ({
  user: state.app.user,
});

export default connect(mapStateToProps)(NavBarMenu);
