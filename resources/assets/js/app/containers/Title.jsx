import { connect } from 'react-redux';

import Title from '../components/Title';
import * as constants from '../constants';

const mapStateToProps = (state) => ({
  title: state.getIn([constants.NAME, 'title']),
  subtitle: state.getIn([constants.NAME, 'subtitle']),
});

export default connect(mapStateToProps)(Title);
