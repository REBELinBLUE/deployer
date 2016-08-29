import React, { PropTypes } from 'react';

import ServerListComponent from './ServerList';
import Loading from '../../../app/components/Loading';
import ServerDialog from '../../containers/dialogs/ServerDialog';

const ServerTab = (props) => {
  const {
    servers,
    onAdd,
    onEdit,
    fetching,
  } = props;

  if (fetching) {
    return (<Loading visible />);
  }

  return (
    <div>
      <ServerListComponent servers={servers} onAdd={onAdd} onEdit={onEdit} />
      <ServerDialog />
    </div>
  );
};

ServerTab.propTypes = {
  servers: PropTypes.array.isRequired,
  fetching: PropTypes.bool.isRequired,
  onEdit: PropTypes.func.isRequired,
  onAdd: PropTypes.func.isRequired,
};

export default ServerTab;
