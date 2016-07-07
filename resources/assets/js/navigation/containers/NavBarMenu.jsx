import React from 'react';
import { connect } from 'react-redux';

import NavBarMenuComponent from '../components/NavBarMenu';

const NavBarMenu = (props) => (<NavBarMenuComponent {...props} />);

const mapStateToProps = (state) => ({
  user: state.getIn(['deployer', 'user']).toJS(), // FIXME: This constant is from another module
});

export default connect(mapStateToProps)(NavBarMenu);
