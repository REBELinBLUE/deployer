import React, { PropTypes } from 'react';
import { FormGroup, FormControl, ControlLabel } from 'react-bootstrap';


import EditorDialog from '../../../../dialogs/EditorDialog';

const FileDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    create: Lang.get('projectFiles.create'),
    edit: Lang.get('projectFiles.edit'),
    warning: Lang.get('projectFiles.warning'),
    name: Lang.get('projectFiles.name'),
    config: Lang.get('projectFiles.config'),
    path: Lang.get('projectFiles.path'),
    content: Lang.get('projectFiles.content'),
  };

  return (
    <EditorDialog id="projectfile" fa="file-code-o" fields={fields} translations={strings} {...others}>
      <FormGroup>
        <ControlLabel>{strings.name}</ControlLabel>
        <FormControl name="name" placeholder={strings.config} disabled={submitting} {...fields.name} />
      </FormGroup>
      <FormGroup>
        <ControlLabel>{strings.path}</ControlLabel>
        <FormControl name="path" placeholder="config/app.php" disabled={submitting} {...fields.path} />
      </FormGroup>
      <FormGroup>
        <ControlLabel>{strings.content}</ControlLabel>
        <div>editor here</div>
      </FormGroup>
    </EditorDialog>
  );
};

FileDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default FileDialog;
