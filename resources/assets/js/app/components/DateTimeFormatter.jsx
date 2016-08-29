import React, { PropTypes } from 'react';
import moment from 'moment';

const DateTimeFormatter = (props) => {
  const formattedDate = moment(props.date).format(props.format);

  return (
    <span>{formattedDate}</span>
  );
};

DateTimeFormatter.propTypes = {
  date: PropTypes.string.isRequired,
  format: PropTypes.string.isRequired,
};

export default DateTimeFormatter;
