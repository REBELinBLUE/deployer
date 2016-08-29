import React, { PropTypes } from 'react';

const OptionalLink = (props) => {
  const {
    to,
    children,
    ...others,
  } = props;

  if (to) {
    return (<a href={to} {...others}>{children}</a>);
  }

  return (<span>{children}</span>);
};

OptionalLink.propTypes = {
  to: PropTypes.string,
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
  target: PropTypes.string,
};

OptionalLink.defaultProps = {
  target: '_blank',
};

export default OptionalLink;
