import React, { PropTypes } from 'react';
import { connect } from 'react-redux';

import ProjectMenu from './ProjectMenu';

const PendingMenu = (props) => {
  const { projects } = props;

  return (
    <ProjectMenu type="pending" projects={projects} />
  );
};

PendingMenu.propTypes = {
  projects: PropTypes.array.isRequired,
};

const mapStateToProps = (state) => ({
  projects: state.projects.pending,
});

export default connect(mapStateToProps)(PendingMenu);
