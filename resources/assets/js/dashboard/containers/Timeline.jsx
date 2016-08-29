import { connect } from 'react-redux';

import Timeline from '../components/Timeline';
import * as constants from '../constants';

const mapStateToProps = (state) => ({
  timeline: state.getIn([constants.NAME, 'timeline']).toJS(),
});

export default connect(mapStateToProps)(Timeline);
