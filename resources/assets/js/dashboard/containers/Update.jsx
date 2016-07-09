import React from 'react';
import { connect } from 'react-redux';

import UpdateComponent from '../components/Update';
import * as constants from '../../app/constants';

const Update = (props) => (<UpdateComponent {...props} />);

const mapStateToProps = (state) => ({
  outdated: state.getIn([constants.NAME, 'outdated']),
  latest: state.getIn([constants.NAME, 'latest']),
  version: state.getIn([constants.NAME, 'version']),
});

export default connect(mapStateToProps)(Update);
