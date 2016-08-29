import React, { PropTypes } from 'react';

const Icon = (props) => {
  const {
    fa,
    spin,
    className,
    ...others,
  } = props;

  let iconClasses = fa;

  // Accept string class names for convenience, but we'll use an array for mapping here
  if (typeof fa === 'string') {
    iconClasses = [fa];
  }

  if (spin) {
    iconClasses.push('spin');
  }

  return (
    <i className={`fa ${iconClasses.map((icon) => (`fa-${icon}`)).join(' ')} ${className || ''}`} {...others} />
  );
};

Icon.propTypes = {
  fa: PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.array,
  ]).isRequired,
  className: PropTypes.string,
  spin: PropTypes.bool,
};

Icon.defaultProps = {
  spin: false,
};

export default Icon;
