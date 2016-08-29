import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import * as constants from '../../constants';
import * as dialog from '../../../dialogs/constants';
import FileTabComponent from '../../components/files/FileTab';
import { addObject, editObject } from '../../../dialogs/actions';

const FileTab = (props) => {
  const {
    actions,
    ...others,
  } = props;

  return (
    <FileTabComponent
      onFileAdd={actions.addFile}
      onFileEdit={actions.editFile}
      onConfigurationAdd={actions.addConfiguration}
      onConfigurationEdit={actions.editConfiguration}
      {...others}
    />
  );
};

FileTab.propTypes = {
  // ...Dialog.propTypes,
  actions: PropTypes.object.isRequired,
};

const mapStateToProps = (state) => ({
  sharedFiles: state.getIn([constants.NAME, 'sharedFiles']).toJS(),
  projectFiles: state.getIn([constants.NAME, 'projectFiles']).toJS(),
  fetching: state.getIn([constants.NAME, 'fetching']),
});

const mapDispatchToProps = (dispatch) => ({
  actions: bindActionCreators({
    addFile: () => (addObject(dialog.PERSISTENT_DIALOG)),
    editFile: (object) => (editObject(dialog.PERSISTENT_DIALOG, object)),
    addConfiguration: () => (addObject(dialog.CONFIGURATION_DIALOG)),
    editConfiguration: (object) => (editObject(dialog.CONFIGURATION_DIALOG, object)),
  }, dispatch),
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(FileTab);
