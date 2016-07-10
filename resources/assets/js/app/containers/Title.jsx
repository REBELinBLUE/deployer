import React from 'react';
import { connect } from 'react-redux';

import TitleComponent from '../components/Title';
import * as constants from '../constants';

const Title = (props) => (<TitleComponent {...props} />);

const mapStateToProps = (state) => ({
  title: state.getIn([constants.NAME, 'title']),
  subtitle: state.getIn([constants.NAME, 'subtitle']),
});

export default connect(mapStateToProps)(Title);
