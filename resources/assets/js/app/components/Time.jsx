import React, { PropTypes } from 'react';

import DateTimeFormatter from './DateTimeFormatter';

const Time = (props) => (<DateTimeFormatter {...props} />);

Time.propTypes = {
  date: PropTypes.string.isRequired,
  format: PropTypes.string,
};

Time.defaultProps = {
  format: 'h:mm:ss A',
};

export default Time;
