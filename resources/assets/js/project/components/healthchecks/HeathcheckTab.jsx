import React, { PropTypes } from 'react';

import HeartbeatList from './heartbeats/HeartBeatList';
import LinkList from './links/LinkList';
import Loading from '../../../app/components/Loading';
import HeartbeatDialog from '../../containers/dialogs/HeartbeatDialog';
import LinkDialog from '../../containers/dialogs/LinkDialog';

const HealthcheckTab = (props) => {
  const {
    heartbeats,
    links,
    fetching,
    onHeartbeatAdd,
    onHeartbeatEdit,
    onLinkAdd,
    onLinkEdit,
  } = props;

  if (fetching) {
    return (<Loading visible />);
  }

  return (
    <div>
      <HeartbeatList heartbeats={heartbeats} onAdd={onHeartbeatAdd} onEdit={onHeartbeatEdit} />
      <LinkList links={links} onAdd={onLinkAdd} onEdit={onLinkEdit} />

      <HeartbeatDialog />
      <LinkDialog />
    </div>
  );
};

HealthcheckTab.propTypes = {
  links: PropTypes.array.isRequired,
  heartbeats: PropTypes.array.isRequired,
  fetching: PropTypes.bool.isRequired,
  onHeartbeatAdd: PropTypes.func.isRequired,
  onHeartbeatEdit: PropTypes.func.isRequired,
  onLinkAdd: PropTypes.func.isRequired,
  onLinkEdit: PropTypes.func.isRequired,
};

export default HealthcheckTab;
