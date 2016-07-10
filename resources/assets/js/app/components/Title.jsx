import React, { PropTypes } from 'react';

import NavButtons from '../containers/NavButtons';

const Title = (props) => {
  const {
    title,
    subtitle,
  } = props;

  return (
    <h1>
      <span>{title}</span>
      {subtitle ? <small>{subtitle}</small> : null}
    </h1>
  );
};

Title.propTypes = {
  title: PropTypes.string.isRequired,
  subtitle: PropTypes.string,
};

export default Title;
