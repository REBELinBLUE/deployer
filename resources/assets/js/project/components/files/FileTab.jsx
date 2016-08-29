import React, { PropTypes } from 'react';

import PersistentFileList from './persistent/FileList';
import ConfigurationFileList from './configuration/FileList';
import Loading from '../../../app/components/Loading';
import ConfigurationFileDialog from '../../containers/dialogs/ConfigurationFileDialog';
import PersistentFileDialog from '../../containers/dialogs/PersistentFileDialog';

const CommandTab = (props) => {
  const {
    sharedFiles,
    projectFiles,
    fetching,
    onFileAdd,
    onFileEdit,
    onConfigurationAdd,
    onConfigurationEdit,
  } = props;

  if (fetching) {
    return (<Loading visible />);
  }

  return (
    <div>
      <PersistentFileList files={sharedFiles} onAdd={onFileAdd} onEdit={onFileEdit} />
      <ConfigurationFileList files={projectFiles} onAdd={onConfigurationAdd} onEdit={onConfigurationEdit} />

      <ConfigurationFileDialog />
      <PersistentFileDialog />
    </div>
  );
};

CommandTab.propTypes = {
  sharedFiles: PropTypes.array.isRequired,
  projectFiles: PropTypes.array.isRequired,
  fetching: PropTypes.bool.isRequired,
  onFileEdit: PropTypes.func.isRequired,
  onFileAdd: PropTypes.func.isRequired,
  onConfigurationEdit: PropTypes.func.isRequired,
  onConfigurationAdd: PropTypes.func.isRequired,
};

export default CommandTab;
