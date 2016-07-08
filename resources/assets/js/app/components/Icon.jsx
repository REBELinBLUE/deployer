import React, { PropTypes } from 'react';

const Icon = (props) => {
  let iconClasses = props.fa;

  // Accept string classnames for convenience, but we'll use an array for mapping here
  if (typeof props.fa === 'string') {
    iconClasses = [props.fa];
  }

  return (
    <i className={`fa ${iconClasses.map((icon) => (`fa-${icon}`))} ${props.className}`} />
  );
};

Icon.propTypes = {
  fa: PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.array,
  ]).isRequired,
  className: PropTypes.string,
};

export default Icon;
