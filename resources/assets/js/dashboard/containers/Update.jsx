import React from 'react';
import { connect } from 'react-redux';

import UpdateComponent from '../components/Update';

const Update = (props) => (<UpdateComponent {...props} />);

// FIXME: Move these into the dashboard reducer?
const mapStateToProps = (state) => ({
  outdated: state.getIn(['deployer', 'outdated']),
  latest: state.getIn(['deployer', 'latest']),
  version: state.getIn(['deployer', 'version']),
});

export default connect(mapStateToProps)(Update);
