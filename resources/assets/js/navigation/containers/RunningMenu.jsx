import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import NAME from '../constants';

import ProjectMenuComponent from '../components/ProjectMenu';

const RunningMenu = (props) => (<ProjectMenuComponent type="running" {...props} />);

const mapStateToProps = (state) => ({
  projects: state.getIn([NAME, 'running']).toJS(),
});

export default connect(mapStateToProps)(RunningMenu);
