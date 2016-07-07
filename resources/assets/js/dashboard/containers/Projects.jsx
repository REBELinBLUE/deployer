import React from 'react';
import { connect } from 'react-redux';

import ProjectListComponent from '../components/Projects';

const Projects = (props) => (<ProjectListComponent {...props} />);

const mapStateToProps = (state) => ({
  projects: state.getIn(['navigation', 'projects']).toJS(),
});

export default connect(mapStateToProps)(Projects);
