import React from 'react';
import { connect } from 'react-redux';

import * as constants from '../constants';
import DeploymentMenu from '../components/DeploymentMenu';

const PendingMenu = (props) => (<DeploymentMenu type="pending" {...props} />);

const mapStateToProps = (state) => ({
  deployments: state.getIn([constants.NAME, 'pending']).toJS(),
});

export default connect(mapStateToProps)(PendingMenu);
