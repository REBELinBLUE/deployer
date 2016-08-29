import React, { PropTypes } from 'react';

import NotificationList from './slack/NotificationList';
import EmailList from './emails/EmailList';
import Loading from '../../../app/components/Loading';
import NotificationDialog from '../../containers/dialogs/NotificationDialog';
import EmailDialog from '../../containers/dialogs/EmailDialog';

const NotificationTab = (props) => {
  const {
    notifications,
    emails,
    fetching,
    onEmailAdd,
    onEmailEdit,
    onNotificationAdd,
    onNotificationEdit,
  } = props;

  if (fetching) {
    return (<Loading visible />);
  }

  return (
    <div>
      <NotificationList notifications={notifications} onAdd={onNotificationAdd} onEdit={onNotificationEdit} />
      <EmailList emails={emails} onAdd={onEmailAdd} onEdit={onEmailEdit} />

      <NotificationDialog />
      <EmailDialog />
    </div>
  );
};

NotificationTab.propTypes = {
  notifications: PropTypes.array.isRequired,
  emails: PropTypes.array.isRequired,
  fetching: PropTypes.bool.isRequired,
  onEmailAdd: PropTypes.func.isRequired,
  onEmailEdit: PropTypes.func.isRequired,
  onNotificationAdd: PropTypes.func.isRequired,
  onNotificationEdit: PropTypes.func.isRequired,
};

export default NotificationTab;
