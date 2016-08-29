import React, { PropTypes } from 'react';
import { Checkbox, FormGroup, FormControl, ControlLabel, OverlayTrigger, Tooltip } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import EditorDialog from '../../../../dialogs/EditorDialog';

const NotificationDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    name: Lang.get('notifications.name'),
    bot: Lang.get('notifications.bot'),
    create: Lang.get('notifications.create'),
    edit: Lang.get('notifications.edit'),
    warning: Lang.get('notifications.warning'),
    icon: Lang.get('notifications.icon'),
    icon_info: Lang.get('notifications.icon_info'),
    channel: Lang.get('notifications.channel'),
    webhook: Lang.get('notifications.webhook'),
    failure_only: Lang.get('notifications.failure_only'),
    failure_description: Lang.get('notifications.failure_description'),
  };

  return (
    <EditorDialog id="notification" fa="slack" fields={fields} translations={strings} {...others}>
      <FormGroup>
        <ControlLabel>{strings.name}</ControlLabel>
        <FormControl name="name" placeholder={strings.bot} disabled={submitting} {...fields.name} />
      </FormGroup>
      <FormGroup>
        <ControlLabel>
          {strings.icon}&nbsp;
          <OverlayTrigger placement="right" overlay={<Tooltip id="iconInfo">{strings.icon_info}</Tooltip>}>
            <Icon fa="question-circle" />
          </OverlayTrigger>
        </ControlLabel>
        <FormControl name="icon" placeholder=":ghost:" disabled={submitting} {...fields.icon} />
      </FormGroup>
      <FormGroup>
        <ControlLabel>{strings.channel}</ControlLabel>
        <FormControl name="channel" placeholder="#slack" disabled={submitting} {...fields.channel} />
      </FormGroup>
      <FormGroup>
        <ControlLabel>{strings.webhook}</ControlLabel>
        <FormControl
          name="webhook"
          placeholder="https://hooks.slack.com/services/"
          disabled={submitting}
          {...fields.webhook}
        />
      </FormGroup>
      <FormGroup>
        <label>{strings.failure_only}</label>
        <Checkbox name="failure_only" disabled={submitting} {...fields.failure_only}>
          {strings.failure_description}
        </Checkbox>
      </FormGroup>
    </EditorDialog>
  );
};

NotificationDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default NotificationDialog;
