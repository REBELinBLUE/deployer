import { connect } from 'react-redux';

import Socket from '../components/Socket';
import * as constants from '../constants';

const mapStateToProps = (state) => ({
  online: state.getIn([constants.NAME, 'online']),
});

export default connect(mapStateToProps)(Socket);
