import React from 'react';
import { Link } from 'react-router';

const NavBar = () => {
  const strings = {
    title: Lang.get('app.name'),
    toggle: Lang.get('app.toggle_nav'),
    profile: Lang.get('users.profile'),
    signout: Lang.get('app.signout'),
  };

  return (
    <header className="main-header">
      <a href="/" className="logo"><b>{strings.title}</b></a>
      <nav className="navbar navbar-static-top" role="navigation">
        <a href="#" className="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span className="sr-only">{strings.toggle}</span>
        </a>
        <div className="navbar-custom-menu">
          <ul className="nav navbar-nav">

            <li className="dropdown messages-menu" id="pending_menu">
              <a href="#" className="dropdown-toggle" data-toggle="dropdown">
                <i className="fa fa-clock-o"></i>
                <span className="label label-info">pending_count</span>
              </a>
              <ul className="dropdown-menu">
                <li className="header">PENDING....</li>
                <li>
                  <ul className="menu">

                  </ul>
                </li>
              </ul>
            </li>

            <li className="dropdown messages-menu" id="deploying_menu">
              <a href="#" className="dropdown-toggle" data-toggle="dropdown">
                <i className="fa fa-spinner"></i>
                <span className="label label-warning">deploying_count</span>
              </a>
              <ul className="dropdown-menu">
                <li className="header">DEPLOYING...</li>
                <li>
                  <ul className="menu">

                  </ul>
                </li>
              </ul>
            </li>

            <li className="dropdown user user-menu">
              <a href="#" className="dropdown-toggle" data-toggle="dropdown">
                <img src="{{ $logged_in_user->avatar_url }}" className="user-image"/>
                <span className="hidden-xs">logged_in_user->name</span>
              </a>
              <ul className="dropdown-menu">
                <li className="user-header">
                  <img src="logged_in_user->avatar_url" className="img-circle"/>
                  <p>logged_in_user->name</p>
                </li>
                <li className="user-footer">
                  <div className="pull-left">
                    <Link to="profile.index" className="btn btn-default btn-flat">{strings.profile}</Link>
                  </div>
                  <div className="pull-right">
                    <Link to="auth.logout" className="btn btn-default btn-flat">{strings.signout}</Link>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
  );
};

export default NavBar;

// @foreach ($pending as $deployment)
// <li id="deployment_info_{{ $deployment->id }}">
//   <a href="{{ route('deployments', ['id' => $deployment->id]) }}">
//   <h4>{{$deployment->project->name }}
// <small
//   className="pull-right">{{Lang::get('dashboard.started')}}: {{$deployment->started_at->format('g:i:s A') }}</small>
// </h4>
// <p>{{Lang::get('deployments.branch')}}: {{$deployment->branch }}</p>
// </a>
// </li>
// @endforeach

// @foreach ($deploying as $deployment)
// <li id="deployment_info_{{ $deployment->id }}">
//   <a href="{{ route('deployments', ['id' => $deployment->id]) }}">
//   <h4>{{$deployment->project->name }}
// <small
//   className="pull-right">{{Lang::get('dashboard.started')}}: {{$deployment->started_at->format('g:i:s A') }}</small>
// </h4>
// <p>{{Lang::get('deployments.branch')}}: {{$deployment->branch }}</p>
// </a>
// </li>
// @endforeach

//
// @push('templates')
// <script type="text/template" id="deployment-list-template">
//   <li id="deployment_info_<%- id %>">
//   <a href="<%- url %>">
//   <h4><%- project_name %> <small className="pull-right">{{ Lang::get('dashboard.started') }}: <%- time %></small></h4>
// <p>{{ Lang::get('deployments.branch') }}: <%- branch %></p>
// </a>
// </li>
// </script>
// @endpush
