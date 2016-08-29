import { connect } from 'react-redux';

import UserMenu from '../components/UserMenu';
import * as constants from '../../app/constants';

const mapStateToProps = (state) => ({
  user: state.getIn([constants.NAME, 'user']).toJS(),
});

export default connect(mapStateToProps)(UserMenu);
