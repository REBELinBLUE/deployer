import React from 'react';
import { connect } from 'react-redux';

import TitleComponent from '../components/Title';

const Title = (props) => (<TitleComponent {...props} />);

const mapStateToProps = (state) => ({
  title: state.getIn(['app', 'title']),
  subtitle: state.getIn(['app', 'subtitle']),
});

export default connect(mapStateToProps)(Title);
