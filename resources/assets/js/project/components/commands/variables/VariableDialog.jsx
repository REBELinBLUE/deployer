import React, { PropTypes } from 'react';
import { FormGroup, FormControl, ControlLabel } from 'react-bootstrap';

import EditorDialog from '../../../../dialogs/EditorDialog';

const VariableDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    name: Lang.get('variables.name'),
    value: Lang.get('variables.value'),
    create: Lang.get('variables.create'),
    edit: Lang.get('variables.edit'),
    warning: Lang.get('variables.warning'),
  };

  return (
    <EditorDialog id="variable" fa="dollar" fields={fields} translations={strings} {...others}>
      <FormGroup controlId="variableName">
        <ControlLabel>{strings.name}</ControlLabel>
        <FormControl name="name" placeholder="COMPOSER_PROCESS_TIMEOUT" disabled={submitting} {...fields.name} />
      </FormGroup>
      <FormGroup controlId="variableValue">
        <ControlLabel>{strings.value}</ControlLabel>
        <FormControl name="value" placeholder="300" disabled={submitting} {...fields.value} />
      </FormGroup>
    </EditorDialog>
  );
};

VariableDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default VariableDialog;
