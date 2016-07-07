import React, { PropTypes, Component } from 'react';
import { connect } from 'react-redux';
import 'whatwg-fetch';

import ProjectListComponent from '../components/Projects';
import { getProjectList } from '../actions';

class Projects extends Component {
  componentDidMount() {
    this.props.dispatch(getProjectList());
  }

  render() {
    return (<ProjectListComponent {...this.props} />);
  }
}

Projects.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = (state) => ({
  projects: state.getIn(['navigation', 'projects', 'data']).toJS(),
  fetching: state.getIn(['navigation', 'projects', 'fetching']),
});

export default connect(mapStateToProps)(Projects);
