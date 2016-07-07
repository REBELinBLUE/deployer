import React from 'react';
import { connect } from 'react-redux';

import * as constants from '../../app/constants';
import NavBarMenuComponent from '../components/NavBarMenu';

const NavBarMenu = (props) => (<NavBarMenuComponent {...props} />);

const mapStateToProps = (state) => ({
  user: state.getIn([constants.NAME, 'user']),
});

export default connect(mapStateToProps)(NavBarMenu);
