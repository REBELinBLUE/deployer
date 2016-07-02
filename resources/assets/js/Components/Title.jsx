import React, { PropTypes } from 'react';
import { connect } from 'react-redux';

const Title = (props) => {
  const { title, subtitle } = props;

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

const mapStateToProps = (state) => ({
  title: state.app.title,
  subtitle: state.app.subtitle,
});

export default connect(mapStateToProps)(Title);
