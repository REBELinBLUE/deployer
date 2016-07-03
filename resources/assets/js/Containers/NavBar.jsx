import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import jQuery from 'jquery';

import NavBarComponent from '../Components/NavBar/NavBar';
import { receivedProjects } from '../actions/navigation';

class NavBar extends React.Component {
  componentDidMount() {
    this.loadData();
  }

  loadData() {
    const { dispatch } = this.props;

    jQuery.get('/api/deployment/running').done((response) => {
      dispatch(receivedProjects(response));
    }).fail((error) => {
      console.log(error);
    });
  }

  render() {
    return (<NavBarComponent />);
  }
}

NavBar.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

export default connect()(NavBar);
