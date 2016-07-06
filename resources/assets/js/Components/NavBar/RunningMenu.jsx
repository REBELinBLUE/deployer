import React, { PropTypes } from 'react';
import { connect } from 'react-redux';

import ProjectMenu from './ProjectMenu';

const RunningMenu = (props) => {
  const { projects } = props;

  return (
    <ProjectMenu type="running" projects={projects} />
  );
};

RunningMenu.propTypes = {
  projects: PropTypes.array.isRequired,
};


// FIXME: The component should not be using state, move to a container!
const mapStateToProps = (state) => ({
  projects: state.getIn(['navigation', 'running']).toJS(),
});


export default connect(mapStateToProps)(RunningMenu);

//FIXME: This should be a component
