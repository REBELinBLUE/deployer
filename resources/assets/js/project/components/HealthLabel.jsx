import React, { PropTypes } from 'react';

const HealthLabel = (props) => {
  const {
    total,
    missed,
  } = props;

  const strings = {
    na: Lang.get('app.not_applicable'),
  };

  let status = false;
  let className = 'success';
  const found = (total - missed);

  if (total > 0) {
    status = `${found} / ${total}`;
  }

  if (total === 0) {
    className = 'warning';
  } else if (missed) {
    className = 'danger';
  }

  return (
    <span className={`pull-right label label-${className}`}>
      {status || strings.na}
    </span>
  );
};

HealthLabel.propTypes = {
  total: PropTypes.number.isRequired,
  missed: PropTypes.number.isRequired,
};

export default HealthLabel;
