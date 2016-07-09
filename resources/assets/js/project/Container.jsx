import React, { PropTypes, Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { setPageTitle } from '../app/actions';
import * as constants from '../navigation/constants';
import ProjectDetailsComponent from './Component';

class ProjectDetails extends Component {
  constructor(props) {
    super(props);

    this.activeProject = props.projects.find((project) => (project.id === parseInt(props.params.id, 10)));
    this.setPageTitle = props.actions.setPageTitle;
  }

  componentDidMount() {
    this.setTitle();
  }

  componentDidUpdate() {
    this.setTitle();
  }

  setTitle() {
    this.setPageTitle(this.activeProject.name);
  }

  render() {
    return (<ProjectDetailsComponent project={this.activeProject} />);
  }
}

ProjectDetails.propTypes = {
  projects: PropTypes.array.isRequired,
  actions: PropTypes.object.isRequired,
  params: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => ({
  projects: state.getIn([constants.NAME, 'projects']).toJS(),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    setPageTitle,
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ProjectDetails);
