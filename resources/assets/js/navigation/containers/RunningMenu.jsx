import React from 'react';
import { connect } from 'react-redux';

import * as constants from '../constants';
import ProjectMenuComponent from '../components/ProjectMenu';

const RunningMenu = (props) => (<ProjectMenuComponent type="running" {...props} />);

const mapStateToProps = (state) => ({
  projects: state.getIn([constants.NAME, 'running']).toJS(),
});

export default connect(mapStateToProps)(RunningMenu);
