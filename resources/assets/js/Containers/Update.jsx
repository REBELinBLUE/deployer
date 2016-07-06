import React from 'react';
import { connect } from 'react-redux';

import UpdateComponent from '../Components/Update';

const Update = (props) => (<UpdateComponent {...props} />);

const mapStateToProps = (state) => ({
  outdated: state.getIn(['app', 'outdated']),
  latest: state.getIn(['app', 'latest']),
  version: state.getIn(['app', 'version']),
});

export default connect(mapStateToProps)(Update);
