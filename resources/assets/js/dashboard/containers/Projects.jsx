import React from 'react';
import { connect } from 'react-redux';

import * as constants from '../../navigation/constants';
import ProjectListComponent from '../components/Projects';

const Projects = (props) => (<ProjectListComponent {...props} />);

const mapStateToProps = (state) => ({
  projects: state.getIn([constants.NAME, 'projects']).toJS(),
});

export default connect(mapStateToProps)(Projects);
