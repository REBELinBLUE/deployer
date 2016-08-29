import { connect } from 'react-redux';

import NavButtons from '../components/NavButtons';
import * as constants from '../../navigation/constants';

const mapStateToProps = (state) => ({
  buttons: state.getIn([constants.NAME, 'buttons']).toJS(),
});

export default connect(mapStateToProps)(NavButtons);
