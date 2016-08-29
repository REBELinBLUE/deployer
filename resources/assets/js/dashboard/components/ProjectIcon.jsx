import React, { PropTypes } from 'react';

import Icon from '../../app/components/Icon';

import {
  PROJECT_STATUS_FINISHED,
  PROJECT_STATUS_PENDING,
  PROJECT_STATUS_DEPLOYING,
  PROJECT_STATUS_FAILED,
} from '../constants';

const ProjectIcon = (props) => {
  const { status } = props;

  let spin = false;
  let fa = 'question-circle';

  // Return the appropriate icon for the status
  if (status === PROJECT_STATUS_FINISHED) {
    fa = 'check';
  } else if (status === PROJECT_STATUS_DEPLOYING) {
    fa = ['spinner', 'pulse'];
    spin = true;
  } else if (status === PROJECT_STATUS_FAILED) {
    fa = 'warning';
  } else if (status === PROJECT_STATUS_PENDING) {
    fa = 'clock-o';
  }

  return (<Icon fa={fa} spin={spin} />);
};

ProjectIcon.propTypes = {
  status: PropTypes.number.isRequired,
};

export default ProjectIcon;
