import React from 'react';
import { connect } from 'react-redux';

import UserMenuComponent from '../components/UserMenu';
import * as constants from '../../app/constants';

const UserMenu = (props) => (<UserMenuComponent {...props} />);

const mapStateToProps = (state) => ({
  user: state.getIn([constants.NAME, 'user']).toJS(),
});

export default connect(mapStateToProps)(UserMenu);
