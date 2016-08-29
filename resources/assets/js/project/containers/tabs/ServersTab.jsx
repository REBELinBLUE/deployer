import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import * as constants from '../../constants';
import * as dialog from '../../../dialogs/constants';
import ServerTabComponent from '../../components/servers/ServerTab';
import { addObject, editObject } from '../../../dialogs/actions';

const ServerTab = (props) => {
  const {
    actions,
    ...others,
  } = props;

  return (
    <ServerTabComponent
      onAdd={actions.addServer}
      onEdit={actions.editServer}
      {...others}
    />
  );
};

ServerTab.propTypes = {
  // ...Dialog.propTypes,
  actions: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => ({
  servers: state.getIn([constants.NAME, 'servers']).toJS(),
  fetching: state.getIn([constants.NAME, 'fetching']),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    addServer: () => (addObject(dialog.SERVER_DIALOG)),
    editServer: (object) => (editObject(dialog.SERVER_DIALOG, object)),
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ServerTab);
