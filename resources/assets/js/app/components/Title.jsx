import React, { PropTypes } from 'react';

import NavButtons from '../containers/NavButtons';

const Title = (props) => {
  const {
    title,
    subtitle,
  } = props;

  return (
    <section className="content-header">
      <NavButtons />
      <h1>
        <span>{title}</span>
        {subtitle ? <small>{subtitle}</small> : null}
      </h1>
    </section>
  );
};

Title.propTypes = {
  title: PropTypes.string.isRequired,
  subtitle: PropTypes.string,
};

export default Title;
