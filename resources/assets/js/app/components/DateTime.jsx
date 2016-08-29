import React, { PropTypes } from 'react';

import DateTimeFormatter from './DateTimeFormatter';

const DateTime = (props) => (<DateTimeFormatter {...props} />);

DateTime.propTypes = {
  date: PropTypes.string.isRequired,
  format: PropTypes.string,
};

DateTime.defaultProps = {
  format: 'Do MMMM YYYY h:mm:ss A',
};

export default DateTime;
