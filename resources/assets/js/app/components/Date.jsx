import React, { PropTypes } from 'react';

import DateTimeFormatter from './DateTimeFormatter';

const Date = (props) => (<DateTimeFormatter {...props} />);

Date.propTypes = {
  date: PropTypes.string.isRequired,
  format: PropTypes.string,
};

Date.defaultProps = {
  format: 'Do MMM YYYY',
};

export default Date;
