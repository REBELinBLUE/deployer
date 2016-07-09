import React from 'react';
import { connect } from 'react-redux';

import NavBarMenuComponent from '../components/NavBarMenu';
import * as constants from '../../app/constants';

const NavBarMenu = (props) => (<NavBarMenuComponent {...props} />);

const mapStateToProps = (state) => ({
  user: state.getIn([constants.NAME, 'user']).toJS(),
});

export default connect(mapStateToProps)(NavBarMenu);
