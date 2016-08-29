import { connect } from 'react-redux';

import Update from '../components/Update';
import * as constants from '../../app/constants';

const mapStateToProps = (state) => ({
  outdated: state.getIn([constants.NAME, 'outdated']),
  latest: state.getIn([constants.NAME, 'latest']),
  version: state.getIn([constants.NAME, 'version']),
});

export default connect(mapStateToProps)(Update);
