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

// FIXME: The component should not be using state, move to a container!
const mapStateToProps = (state) => ({
  projects: state.get('navigation').get('pending').toJS(),
});

export default connect(mapStateToProps)(PendingMenu);
