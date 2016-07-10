import React, { PropTypes } from 'react';

import { ButtonToolbar, Button } from 'react-bootstrap';

const NavButtons = (props) => {
  const { buttons } = props;

  if (!buttons.length) {
    return null;
  }

  const buttonInstances = [];
  buttons.forEach((button) => {
    buttonInstances.push(
      <Button id={button.id} bsStyle={button.type} title={button.title}>
        <span className="fa fa-key"></span>
        {button.text}
      </Button>
    );
  });

  return (
    <ButtonToolbar className="pull-right">{buttonInstances}</ButtonToolbar>
  );
};

NavButtons.propTypes = {
  buttons: PropTypes.array.isRequired, // array of shape
};

export default NavButtons;
