import React, { PropTypes } from 'react';
import { connect } from 'react-redux';

import * as constants from '../../navigation/constants';
import SideBarComponent from '../components/SideBar';
import Project from '../../models/Project';

const SideBar = (props) => {
  const {
    projects,
    groups,
  } = props;

  const groupedProjects = [];

  groups.forEach((group) => {
    groupedProjects[group.id] = {
      group,
      projects: [],
    };
  });

  projects.forEach((project) => {
    groupedProjects[project.group_id].projects.push(project);
  });

  return (<SideBarComponent projects={groupedProjects} />);
};

SideBar.propTypes = {
  projects: PropTypes.arrayOf(Project).isRequired,
  groups: PropTypes.array.isRequired,
};

const mapStateToProps = (state) => ({
  projects: state.getIn([constants.NAME, 'projects']).toJS(),
  groups: state.getIn([constants.NAME, 'groups']).toJS(),
});

export default connect(mapStateToProps)(SideBar);
