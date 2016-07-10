import React, { PropTypes } from 'react';
import { ButtonToolbar, Button } from 'react-bootstrap';

import Icon from './Icon';

const NavButtons = (props) => {
  const { buttons } = props;

  if (!buttons.length) {
    return null;
  }

  const buttonInstances = [];
  buttons.forEach((button, key) => {
    buttonInstances.push(
      <Button
        key={key}
        id={button.id}
        bsStyle={button.type}
        title={button.title}
        onClick={button.action ? button.action : null}
      >
        <Icon fa={button.fa} /> {button.text}
      </Button>
    );
  });

  return (
    <ButtonToolbar className="pull-right">{buttonInstances}</ButtonToolbar>
  );
};

NavButtons.propTypes = {
  buttons: PropTypes.array.isRequired, // FIXME array of shape
};

export default NavButtons;
