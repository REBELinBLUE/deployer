import React, { PropTypes } from 'react';
import { Button } from 'react-bootstrap';
import classNames from 'classnames';

import Icon from './Icon';

const Box = (props) => {
  const {
    id,
    create,
    title,
    header,
    children,
    table,
    onAdd,
  } = props;

  const className = classNames('box-body', {
    'table-responsive': table,
  });

  return (
    <div className="box">
      <div className="box-header">
        {
          create ?
            <div className="pull-right">
              <Button title={create} onClick={onAdd}><Icon fa="plus" /> {create}</Button>
            </div>
          :
            null
        }
        <h3 className="box-title">{title}</h3>
      </div>

      {header}

      <div className={className} id={id ? `${id}_body` : ''}>
        {children}
      </div>
    </div>
  );
};

Box.propTypes = {
  id: PropTypes.string,
  title: PropTypes.string.isRequired,
  create: PropTypes.string,
  table: PropTypes.bool,
  header: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]),
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
  onAdd: PropTypes.func,
};

Box.defaultProps = {
  table: false,
  header: null,
  onAdd: () => {},
};

export default Box;
