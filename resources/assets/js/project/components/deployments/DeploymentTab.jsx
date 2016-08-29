import React, { PropTypes } from 'react';

import DeploymentListComponent from './DeploymentList';

const DeploymentTab = (props) => {
  const {
    fetching,
    ...others,
  } = props;

  return (<DeploymentListComponent {...others} />);
};

DeploymentTab.propTypes = {
  fetching: PropTypes.bool.isRequired,
};

export default DeploymentTab;
