import React from 'react';
import { connect } from 'react-redux';

import TitleComponent from '../components/Title';
import NAME from '../constants';

const Title = (props) => (<TitleComponent {...props} />);

const mapStateToProps = (state) => ({
  title: state.getIn([NAME, 'title']),
  subtitle: state.getIn([NAME, 'subtitle']),
});

export default connect(mapStateToProps)(Title);
