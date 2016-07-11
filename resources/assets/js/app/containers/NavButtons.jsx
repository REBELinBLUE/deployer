import React, { PropTypes } from 'react';
import { connect } from 'react-redux';

import NavButtonsComponent from '../components/NavButtons';
import * as constants from '../../navigation/constants';

const NavButtons = (props) => (<NavButtonsComponent {...props} />);

NavButtons.propTypes = {
  buttons: PropTypes.array.isRequired,
};

const mapStateToProps = (state) => ({
  buttons: state.getIn([constants.NAME, 'buttons']).toJS(),
});

export default connect(mapStateToProps)(NavButtons);
