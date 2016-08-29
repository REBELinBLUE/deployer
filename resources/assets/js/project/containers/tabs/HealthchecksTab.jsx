import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import * as constants from '../../constants';
import * as dialog from '../../../dialogs/constants';
import HealthcheckTabComponent from '../../components/healthchecks/HeathcheckTab';
import { addObject, editObject } from '../../../dialogs/actions';

const HealthcheckTab = (props) => {
  const {
    actions,
    ...others,
  } = props;

  return (
    <HealthcheckTabComponent
      onHeartbeatAdd={actions.addHeartbeat}
      onHeartbeatEdit={actions.editHeartbeat}
      onLinkAdd={actions.addLink}
      onLinkEdit={actions.editLink}
      {...others}
    />
  );
};

HealthcheckTab.propTypes = {
  // ...Dialog.propTypes,
  actions: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => ({
  heartbeats: state.getIn([constants.NAME, 'heartbeats']).toJS(),
  links: state.getIn([constants.NAME, 'links']).toJS(),
  fetching: state.getIn([constants.NAME, 'fetching']),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    addHeartbeat: () => (addObject(dialog.HEARTBEAT_DIALOG)),
    editHeartbeat: (object) => (editObject(dialog.HEARTBEAT_DIALOG, object)),
    addLink: () => (addObject(dialog.LINK_DIALOG)),
    editLink: (object) => (editObject(dialog.LINK_DIALOG, object)),
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(HealthcheckTab);
