import React, { PropTypes } from 'react';

const Title = (props) => {
  const {
    title,
    subtitle,
  } = props;

  return (
    <section className="content-header">
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
