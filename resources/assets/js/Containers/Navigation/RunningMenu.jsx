import React, { PropTypes } from 'react';
import { connect } from 'react-redux';

import ProjectMenuComponent from '../../Components/Navigation/ProjectMenu';

const RunningMenu = (props) => (<ProjectMenuComponent type="running" {...props} />);

const mapStateToProps = (state) => ({
  projects: state.getIn(['navigation', 'running']).toJS(),
});

export default connect(mapStateToProps)(RunningMenu);
