import React from 'react';
import { connect } from 'react-redux';
import NAME from '../constants';

import NavBarMenuComponent from '../components/NavBarMenu';

const NavBarMenu = (props) => (<NavBarMenuComponent {...props} />);

const mapStateToProps = (state) => ({
  user: state.getIn(['app', 'user']),
});

export default connect(mapStateToProps)(NavBarMenu);
