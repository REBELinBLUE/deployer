import React, { PropTypes } from 'react';
import { FormGroup, FormControl, ControlLabel } from 'react-bootstrap';

import EditorDialog from '../../../../dialogs/EditorDialog';

const EmailDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    create: Lang.get('notifyEmails.create'),
    edit: Lang.get('notifyEmails.edit'),
    warning: Lang.get('notifyEmails.warning'),
    name: Lang.get('notifyEmails.name'),
    email: Lang.get('notifyEmails.email'),
    address: Lang.get('notifyEmails.address'),
  };

  return (
    <EditorDialog id="notifyemail" fa="envelope" fields={fields} translations={strings} {...others}>
      <FormGroup>
        <ControlLabel>{strings.name}</ControlLabel>
        <FormControl name="name" placeholder={strings.name} disabled={submitting} {...fields.name} />
      </FormGroup>
      <FormGroup>
        <ControlLabel>{strings.email}</ControlLabel>
        <FormControl name="address" placeholder={strings.address} disabled={submitting} {...fields.address} />
      </FormGroup>
    </EditorDialog>
  );
};

EmailDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default EmailDialog;
