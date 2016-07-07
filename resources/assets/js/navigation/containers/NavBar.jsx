import React, { PropTypes, Component } from 'react';
import { connect } from 'react-redux';

import NavBarComponent from '../components/NavBar';
import * as actions from '../actions';

class NavBar extends Component {
  componentDidMount() {
    this.props.dispatch(actions.getRunningProjects());
  }

  render() {
    return (<NavBarComponent />);
  }
}

NavBar.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

export default connect()(NavBar);
