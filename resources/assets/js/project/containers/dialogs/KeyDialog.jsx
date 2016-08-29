import React, { PropTypes } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

import * as dialog from '../../../dialogs/constants';
import Dialog from '../../components/KeyDialog';
import { hideDialog } from '../../../dialogs/actions';

const KeyDialog = (props) => {
  const {
    actions,
    ...others,
  } = props;

  return (
    <Dialog
      onHide={actions.hideDialog}
      {...others}
    />
  );
};

KeyDialog.propTypes = {
  actions: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => ({
  visible: (state.getIn([dialog.NAME, 'visible']) === dialog.SSH_KEY_DIALOG),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    hideDialog: () => (hideDialog(dialog.SSH_KEY_DIALOG)),
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(KeyDialog);
