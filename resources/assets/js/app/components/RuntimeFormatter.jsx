import React, { PropTypes } from 'react';

const RuntimeFormatter = (props) => {
  const { runtime } = props;

  const units = {
    week: (7 * 24 * 3600),
    day: (24 * 3600),
    hour: 3600,
    minute: 60,
    second: 1,
  };

  let output = Lang.choice('deployments.second', 0, { time: 0 });

  if (runtime > 0) {
    output = '';

    let remaining = runtime;
    Object.keys(units).forEach((name) => {
      const divisor = units[name];

      const quotient = parseInt(remaining / divisor, 10);
      if (quotient) {
        output += Lang.choice(`deployments.${name}`, quotient, { time: quotient });
        output += ', ';

        remaining -= quotient * divisor;
      }
    });

    output = output.slice(0, -2);
  }

  return (
    <span>{output}</span>
  );
};

RuntimeFormatter.propTypes = {
  runtime: PropTypes.number.isRequired,
};

export default RuntimeFormatter;
