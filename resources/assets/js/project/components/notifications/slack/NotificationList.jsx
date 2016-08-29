import React, { PropTypes } from 'react';
import { Button, ButtonGroup } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import Box from '../../../../app/components/Box';

const NotificationList = (props) => {
  const {
    notifications,
    onAdd,
    onEdit,
  } = props;

  const strings = {
    create: Lang.get('notifications.create'),
    edit: Lang.get('notifications.edit'),
    label: Lang.get('notifications.slack'),
    none: Lang.get('notifications.none'),
    name: Lang.get('notifications.name'),
    channel: Lang.get('notifications.channel'),
    notify_failure_only: Lang.get('notifications.notify_failure_only'),
    yes: Lang.get('app.yes'),
    no: Lang.get('app.no'),
  };

  if (notifications.length === 0) {
    return (
      <Box title={strings.label} onAdd={onAdd} create={strings.create}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const notificationList = [];
  notifications.forEach((notification) => {
    const id = `notification_${notification.id}`;

    notificationList.push(
      <tr key={id} id={id}>
        <td>{notification.name}</td>
        <td>{notification.channel}</td>
        <td>{notification.failure_only ? strings.yes : strings.no}</td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className="btn-edit" title={strings.edit} onClick={() => onEdit(notification)}>
              <Icon fa="edit" />
            </Button>
          </ButtonGroup>
        </td>
      </tr>
    );
  });

  return (
    <Box title={strings.label} onAdd={onAdd} create={strings.create} table>
      <table className="table table-striped">
        <thead>
          <tr>
            <th>{strings.name}</th>
            <th>{strings.channel}</th>
            <th>{strings.notify_failure_only}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{notificationList}</tbody>
      </table>
    </Box>
  );
};

NotificationList.propTypes = {
  notifications: PropTypes.array.isRequired,
  onAdd: PropTypes.func.isRequired,
  onEdit: PropTypes.func.isRequired,
};

export default NotificationList;
