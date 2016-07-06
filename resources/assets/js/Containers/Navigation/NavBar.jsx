import React, { PropTypes, Component } from 'react';
import { connect } from 'react-redux';
import 'whatwg-fetch';

import NavBarComponent from '../../Components/Navigation/NavBar';
import { getRunningProjects } from '../../actions/navigation';

class NavBar extends Component {
  componentDidMount() {
    this.props.dispatch(getRunningProjects());
  }

  render() {
    return (<NavBarComponent />);
  }
}

NavBar.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

export default connect()(NavBar);
