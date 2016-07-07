import React from 'react';
import { connect } from 'react-redux';

import * as constants from '../constants';
import ProjectMenuComponent from '../components/ProjectMenu';

const PendingMenu = (props) => (<ProjectMenuComponent type="pending" {...props} />);

const mapStateToProps = (state) => ({
  projects: state.getIn([constants.NAME, 'pending']).toJS(),
});

export default connect(mapStateToProps)(PendingMenu);
