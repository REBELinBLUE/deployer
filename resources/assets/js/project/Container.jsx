import React, { PropTypes, Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { setPageTitle } from '../app/actions';
import { setProject, fetchProject } from './actions';
import { setButtons } from '../navigation/actions';
import * as constants from '../navigation/constants';
import ProjectDetailsComponent from './Components/Details';

class ProjectDetails extends Component {
  constructor(props) {
    super(props);

    this.activeProject = props.projects.find((project) => (project.id === parseInt(props.params.id, 10)));
    this.setPageTitle = props.actions.setPageTitle;
    this.setProject = props.actions.setProject;
    this.fetchProject = props.actions.fetchProject;
    this.setButtons = props.actions.setButtons;
    
// <div class="pull-right">
//   <button type="button" class="btn btn-default" title="{{ Lang::get('projects.view_ssh_key') }}" data-toggle="modal" data-target="#key"><span class="fa fa-key"></span> {{ Lang::get('projects.ssh_key') }}</button>
//   <button id="deploy_project" data-toggle="modal" data-backdrop="static" data-target="#reason" type="button" class="btn btn-danger" title="{{ Lang::get('projects.deploy_project') }}" {{ ($project->isDeploying() OR !count($project->servers)) ? 'disabled' : '' }}><span class="fa fa-cloud-upload"></span> {{ Lang::get('projects.deploy') }}</button>
// </div>
// [
//   { id: 'ssh', type: 'primary', title: 'blah', fa: 'sd', text: 'blah'}
// ]
  }

  componentDidMount() {
    this.setupProject();
  }

  componentDidUpdate() {
    this.setupProject();
  }

  setupProject() {
    this.setPageTitle(this.activeProject.name);
    this.setProject(this.activeProject);

    this.setButtons([
      { id: 'ssh', type: 'primary', title: 'blah', fa: 'sd', text: 'blah'},
      { id: 'ssh', type: 'primary', title: 'blah', fa: 'sd', text: 'blah'}
    ]);
  }

  render() {
    return (
      <ProjectDetailsComponent project={this.activeProject}>{this.props.children}</ProjectDetailsComponent>
    );
  }
}

ProjectDetails.propTypes = {
  projects: PropTypes.array.isRequired,
  actions: PropTypes.object.isRequired,
  params: PropTypes.object.isRequired,
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

const mapStateToProps = (state) => ({
  projects: state.getIn([constants.NAME, 'projects']).toJS(),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    setPageTitle,
    setProject,
    fetchProject,
    setButtons,
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ProjectDetails);
