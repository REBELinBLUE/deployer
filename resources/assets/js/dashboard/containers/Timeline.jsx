import React from 'react';
import { connect } from 'react-redux';

import DashboardTimeline from '../components/Timeline';

const Timeline = (props) => (<DashboardTimeline {...props} />);

const mapStateToProps = (state) => ({
  timeline: state.getIn(['dashboard', 'timeline']).toJS(),
});

export default connect(mapStateToProps)(Timeline);
