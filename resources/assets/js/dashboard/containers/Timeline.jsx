import React from 'react';
import { connect } from 'react-redux';

import DashboardTimeline from '../components/Timeline';
import * as constants from '../constants';

const Timeline = (props) => (<DashboardTimeline {...props} />);

const mapStateToProps = (state) => ({
  timeline: state.getIn([constants.NAME, 'timeline']).toJS(),
});

export default connect(mapStateToProps)(Timeline);
