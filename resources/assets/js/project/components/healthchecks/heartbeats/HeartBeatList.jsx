import React, { PropTypes } from 'react';
import { Button, ButtonGroup } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import Box from '../../../../app/components/Box';
import FormattedDateTime from '../../../../app/components/DateTime';
import Label from './HeartBeatLabel';

import {
  HEARTBEAT_STATUS_OK,
  HEARTBEAT_STATUS_MISSING,
} from '../../../constants';

const HeartBeatList = (props) => {
  const {
    heartbeats,
    onAdd,
    onEdit,
  } = props;

  const strings = {
    create: Lang.get('heartbeats.create'),
    edit: Lang.get('heartbeats.edit'),
    label: Lang.get('heartbeats.title'),
    none: Lang.get('heartbeats.none'),
    name: Lang.get('heartbeats.name'),
    url: Lang.get('heartbeats.url'),
    interval: Lang.get('heartbeats.interval'),
    last_check_in: Lang.get('heartbeats.last_check_in'),
    status: Lang.get('heartbeats.status'),
    never: Lang.get('app.never'),
    interval_10: Lang.get('heartbeats.interval_10'),
    interval_30: Lang.get('heartbeats.interval_30'),
    interval_60: Lang.get('heartbeats.interval_60'),
    interval_120: Lang.get('heartbeats.interval_120'),
    interval_720: Lang.get('heartbeats.interval_720'),
    interval_1440: Lang.get('heartbeats.interval_1440'),
    interval_10080: Lang.get('heartbeats.interval_10080'),
  };

  if (heartbeats.length === 0) {
    return (
      <Box title={strings.label} onAdd={onAdd} create={strings.create}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const heartbeatList = [];
  heartbeats.forEach((heartbeat) => {
    const id = `heartbeat_${heartbeat.id}`;

    const label = strings[`interval_${heartbeat.interval}`];

    let hasRun = false;
    if (heartbeat.status === HEARTBEAT_STATUS_OK) {
      hasRun = true;
    } else if (heartbeat.status === HEARTBEAT_STATUS_MISSING) {
      hasRun = (heartbeat.last_activity !== null);
    }

    heartbeatList.push(
      <tr key={id} id={id}>
        <td>{heartbeat.name}</td>
        <td>{heartbeat.callback_url}</td>
        <td>{label}</td>
        <td>{hasRun ? <FormattedDateTime date={heartbeat.last_activity} /> : strings.never}</td>
        <td><Label status={heartbeat.status} /></td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className="btn-edit" title={strings.edit} onClick={() => onEdit(heartbeat)}>
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
            <th>{strings.url}</th>
            <th>{strings.interval}</th>
            <th>{strings.last_check_in}</th>
            <th>{strings.status}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{heartbeatList}</tbody>
      </table>
    </Box>
  );
};

HeartBeatList.propTypes = {
  heartbeats: PropTypes.array.isRequired,
  onAdd: PropTypes.func.isRequired,
  onEdit: PropTypes.func.isRequired,
};

export default HeartBeatList;
