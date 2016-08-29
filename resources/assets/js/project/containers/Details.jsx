import React, { PropTypes, Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import { setPageTitle } from '../../app/actions';
import { clearActiveProject, setProject, fetchProject } from '../actions';
import { showDialog } from '../../dialogs/actions';
import { setButtons } from '../../navigation/actions';
import * as constants from '../../navigation/constants';
import ProjectDetailsComponent from '../components/Details';
import { SSH_KEY_DIALOG } from '../../dialogs/constants';
import Project from '../../models/Project';

class ProjectDetails extends Component {
  constructor(props) {
    super(props);

    this.activeProject = props.projects.find((project) => (project.id === parseInt(props.params.id, 10)));
    this.actions = props.actions;
  }

  componentWillMount() {
    this.setupProject();
  }

  // componentDidUpdate() {
  //   this.setupProject();
  // }

  componentWillUnmount() {
    this.actions.setButtons([]);
    this.actions.clearActiveProject();
  }

  setupProject() {
    this.actions.setPageTitle(this.activeProject.name);
    this.actions.setProject(this.activeProject);
    this.actions.fetchProject(this.activeProject);

    this.actions.setButtons([
      {
        id: 'ssh',
        type: 'default',
        title: Lang.get('projects.view_ssh_key'),
        fa: 'key',
        text: Lang.get('projects.ssh_key'),
        action: () => { this.actions.showDialog(SSH_KEY_DIALOG); },
      },
      {
        id: 'deploy_project',
        type: 'danger',
        title: Lang.get('projects.deploy_project'),
        fa: 'cloud-upload',
        text: Lang.get('projects.deploy'),
        action: () => {},
      },
    ]);
  }

  render() {
    return (
      <ProjectDetailsComponent project={this.activeProject}>{this.props.children}</ProjectDetailsComponent>
    );
  }
}

ProjectDetails.propTypes = {
  projects: PropTypes.arrayOf(Project).isRequired,
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
    showDialog,
    clearActiveProject,
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ProjectDetails);
