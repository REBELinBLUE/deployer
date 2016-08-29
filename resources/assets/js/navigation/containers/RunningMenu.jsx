import React from 'react';
import { connect } from 'react-redux';

import * as constants from '../constants';
import DeploymentMenu from '../components/DeploymentMenu';

const RunningMenu = (props) => (<DeploymentMenu type="running" {...props} />);

const mapStateToProps = (state) => ({
  deployments: state.getIn([constants.NAME, 'running']).toJS(),
});

export default connect(mapStateToProps)(RunningMenu);
