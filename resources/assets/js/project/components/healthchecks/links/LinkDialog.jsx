import React, { PropTypes } from 'react';

import EditorDialog from '../../../../dialogs/EditorDialog';

const LinkDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    create: Lang.get('checkUrls.create'),
    edit: Lang.get('checkUrls.edit'),
    warning: Lang.get('checkUrls.warning'),
  };

  return (
    <EditorDialog id="checkurl" fa="link" fields={fields} translations={strings} {...others}>
      Link dialog - {submitting}
    </EditorDialog>
  );
};

LinkDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default LinkDialog;
