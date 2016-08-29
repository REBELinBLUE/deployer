import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import * as constants from '../../constants';
import * as dialog from '../../../dialogs/constants';
import NotificationTabComponent from '../../components/notifications/NotificationTab';
import { addObject, editObject } from '../../../dialogs/actions';

const NotificationTab = (props) => {
  const {
    actions,
    ...others,
  } = props;

  return (
    <NotificationTabComponent
      onNotificationAdd={actions.addNotification}
      onNotificationEdit={actions.editNotification}
      onEmailAdd={actions.addEmail}
      onEmailEdit={actions.editEmail}
      {...others}
    />
  );
};

NotificationTab.propTypes = {
  // ...Dialog.propTypes,
  actions: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => ({
  notifications: state.getIn([constants.NAME, 'notifications']).toJS(),
  emails: state.getIn([constants.NAME, 'emails']).toJS(),
  fetching: state.getIn([constants.NAME, 'fetching']),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    addNotification: () => (addObject(dialog.SLACK_DIALOG)),
    editNotification: (object) => (editObject(dialog.SLACK_DIALOG, object)),
    addEmail: () => (addObject(dialog.EMAIL_DIALOG)),
    editEmail: (object) => (editObject(dialog.EMAIL_DIALOG, object)),
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(NotificationTab);
