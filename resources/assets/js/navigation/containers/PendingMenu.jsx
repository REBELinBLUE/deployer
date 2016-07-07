import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import NAME from '../constants';

import ProjectMenuComponent from '../components/ProjectMenu';

const PendingMenu = (props) => (<ProjectMenuComponent type="pending" {...props} />);

const mapStateToProps = (state) => ({
  projects: state.getIn([NAME, 'pending']).toJS(),
});

export default connect(mapStateToProps)(PendingMenu);
