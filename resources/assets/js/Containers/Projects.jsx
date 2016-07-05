import React, { PropTypes, Component } from 'react';
import { connect } from 'react-redux';
import 'whatwg-fetch';

import GroupedProjectList from '../Components/Projects';
import { getProjectList } from '../actions/dashboard';

class Projects extends Component {
  componentDidMount() {
    this.props.dispatch(getProjectList());
  }

  render() {
    return (<GroupedProjectList {...this.props} />);
  }
}

Projects.propTypes = {
  dispatch: PropTypes.func.isRequired,
  ...GroupedProjectList.propTypes,
};

const mapStateToProps = (state) => ({
  projects: state.get('navigation').get('projects').get('data').toJS(),
  fetching: state.get('navigation').get('projects').get('fetching'),
});

export default connect(mapStateToProps)(Projects);
