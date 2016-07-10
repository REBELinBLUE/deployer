import React, { PropTypes } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import * as constants from '../constants';
import Dialog from '../components/KeyDialog';
import { hideKey } from '../actions';

const KeyDialog = (props) => {
  const {
    actions,
    ...others,
  } = props;

  return (
    <Dialog onHide={actions.hideKey} {...others} />
  );
};

KeyDialog.propTypes = {
  project: PropTypes.object.isRequired,
  actions: PropTypes.object.isRequired,
  visible: PropTypes.bool.isRequired,
};

const mapStateToProps = (state) => ({
  visible: state.getIn([constants.NAME, 'showKey']),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    hideKey,
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(KeyDialog);
