import React, { PropTypes } from 'react';
import { NavItem as BsNavItem } from 'react-bootstrap';
import { LinkContainer, IndexLinkContainer } from 'react-router-bootstrap';

import Icon from './Icon';

const NavItem = (props) => {
  const {
    to,
    id,
    fa,
    children,
    primary,
  } = props;

  let Container = (primary ? IndexLinkContainer : LinkContainer);

  return (
    <Container to={to}>
      <BsNavItem eventKey={id}>
        <Icon fa={fa} /> {children}
      </BsNavItem>
    </Container>
  );
};

NavItem.propTypes = {
  to: PropTypes.string.isRequired,
  id: PropTypes.any.isRequired,
  fa: PropTypes.string.isRequired,
  children: PropTypes.string.isRequired,
  primary: PropTypes.bool,
};

NavItem.defaultProps = {
  primary: false,
};

export default NavItem;
