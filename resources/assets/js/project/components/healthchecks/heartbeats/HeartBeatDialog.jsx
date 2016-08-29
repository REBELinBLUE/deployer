import React, { PropTypes } from 'react';
import { FormGroup, FormControl, ControlLabel } from 'react-bootstrap';

import EditorDialog from '../../../../dialogs/EditorDialog';

const HeartBeatDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    create: Lang.get('heartbeats.create'),
    edit: Lang.get('heartbeats.edit'),
    warning: Lang.get('heartbeats.warning'),
  };

  return (
    <EditorDialog id="heartbeat" fa="heartbeat" fields={fields} translations={strings} {...others}>
      Heartbeat dialog - {submitting}
    </EditorDialog>
  );
};

HeartBeatDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default HeartBeatDialog;
