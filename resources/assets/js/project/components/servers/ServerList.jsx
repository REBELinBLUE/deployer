import React, { PropTypes } from 'react';
import { Button, ButtonGroup } from 'react-bootstrap';

import { SERVER_STATUS_TESTING } from '../../constants';
import Label from './ServerLabel';
import Icon from '../../../app/components/Icon';
import Box from '../../../app/components/Box';

const Servers = (props) => {
  const {
    servers,
    onAdd,
    onEdit,
  } = props;

  const strings = {
    create: Lang.get('servers.create'),
    label: Lang.get('servers.label'),
    none: Lang.get('servers.none'),
    name: Lang.get('servers.name'),
    connect_as: Lang.get('servers.connect_as'),
    ip_address: Lang.get('servers.ip_address'),
    port: Lang.get('servers.port'),
    runs_code: Lang.get('servers.runs_code'),
    status: Lang.get('servers.status'),
    yes: Lang.get('app.yes'),
    no: Lang.get('app.no'),
    edit: Lang.get('servers.edit'),
    test: Lang.get('servers.test'),
  };

  if (servers.length === 0) {
    return (
      <Box title={strings.label} onAdd={onAdd} create={strings.create}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const serverList = [];
  servers.forEach((server) => {
    const id = `server_${server.id}`;

    const testing = (server.status === SERVER_STATUS_TESTING);

    serverList.push(
      <tr key={id} id={id}>
        <td>{server.name}</td>
        <td>{server.user}</td>
        <td>{server.ip_address}</td>
        <td>{server.port}</td>
        <td>{server.deploy_code ? strings.yes : strings.no}</td>
        <td><Label status={server.status} /></td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className="btn-test" title={strings.test} disabled={testing}>
              <Icon fa="refresh" spin={testing} />
            </Button>
            <Button type="button" className="btn-edit" title={strings.edit} onClick={() => onEdit(server)}>
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
            <th>{strings.connect_as}</th>
            <th>{strings.ip_address}</th>
            <th>{strings.port}</th>
            <th>{strings.runs_code}</th>
            <th>{strings.status}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{serverList}</tbody>
      </table>
    </Box>
  );
};

Servers.propTypes = {
  servers: PropTypes.array.isRequired,
  onAdd: PropTypes.func.isRequired,
  onEdit: PropTypes.func.isRequired,
};

export default Servers;
