import React, { PropTypes } from 'react';
import { FormGroup, FormControl, ControlLabel, OverlayTrigger, Tooltip } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import EditorDialog from '../../../../dialogs/EditorDialog';

const FileDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    create: Lang.get('sharedFiles.create'),
    edit: Lang.get('sharedFiles.edit'),
    warning: Lang.get('sharedFiles.warning'),
    name: Lang.get('sharedFiles.name'),
    cache: Lang.get('sharedFiles.cache'),
    file: Lang.get('sharedFiles.file'),
    example: Lang.get('sharedFiles.example'),
  };

  return (
    <EditorDialog id="sharefile" fa="folder" fields={fields} translations={strings} {...others}>
      <FormGroup>
        <ControlLabel>{strings.name}</ControlLabel>
        <FormControl name="name" placeholder={strings.cache} disabled={submitting} {...fields.name} />
      </FormGroup>
      <FormGroup>
        <ControlLabel>
          {strings.file}&nbsp;
          <OverlayTrigger placement="right" overlay={<Tooltip id="filePath">{strings.example}</Tooltip>}>
            <Icon fa="question-circle" />
          </OverlayTrigger>
        </ControlLabel>
        <FormControl name="file" placeholder="/storage/" disabled={submitting} {...fields.file} />
      </FormGroup>

    </EditorDialog>
  );
};

FileDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default FileDialog;
