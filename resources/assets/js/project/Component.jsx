import React, { PropTypes } from 'react';

const ProjectDetails = (props) => {
  return (
    <div>
      <strong>ProjectDetails</strong>
      <pre>{JSON.stringify(props.project)}</pre>
    </div>);
};

ProjectDetails.propTypes = {
  project: PropTypes.object.isRequired,
};

export default ProjectDetails;
