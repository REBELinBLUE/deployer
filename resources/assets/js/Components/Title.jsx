import React, { PropTypes } from 'react';
import { connect } from 'react-redux';

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

// FIXME:this should be in a component
const mapStateToProps = (state) => ({
  title: state.getIn(['app', 'title']),
  subtitle: state.getIn(['app', 'subtitle']),
});

export default connect(mapStateToProps)(Title);
