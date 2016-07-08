import React, { PropTypes } from 'react';

const Box = (props) => (
  <div className="box">
    <div className="box-header">
      <h3 className="box-title">{props.title}</h3>
    </div>

    <div className="box-body" id={props.id ? `${props.id}_body` : ''}>
      {props.children}
    </div>
  </div>
);

Box.propTypes = {
  id: PropTypes.string,
  title: PropTypes.string.isRequired,
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

export default Box;
