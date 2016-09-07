import React, { PropTypes } from 'react';
import { Checkbox, FormControl, ControlLabel, OverlayTrigger, Tooltip } from 'react-bootstrap';

import FormGroup from '../../../app/components/FormGroup';
import Icon from '../../../app/components/Icon';
import EditorDialog from '../../../dialogs/EditorDialog';

const ServerDialog = (props) => {
  const {
    fields,
    ...others,
  } = props;

  const submitting = props.submitting;

  const strings = {
    name: Lang.get('servers.name'),
    web: Lang.get('servers.web'),
    connect_as: Lang.get('servers.connect_as'),
    ip_address: Lang.get('servers.ip_address'),
    port: Lang.get('servers.port'),
    path: Lang.get('servers.path'),
    options: Lang.get('servers.options'),
    deploy_code: Lang.get('servers.deploy_code'),
    example: Lang.get('servers.example'),
    value: Lang.get('servers.value'),
    create: Lang.get('servers.create'),
    edit: Lang.get('servers.edit'),
    warning: Lang.get('servers.warning'),
  };

  // FIXME: Figure out why this appears to be needed
  fields.deploy_code.defaultChecked = others.values.deploy_code;
  fields.deploy_code.value = 1;

  delete fields.deploy_code.checked;

  return (
    <EditorDialog id="servers" fa="tasks" fields={fields} translations={strings} {...others}>
      <FormGroup controlId="serverName" {...fields.name}>
        <ControlLabel>{strings.name}</ControlLabel>
        <FormControl placeholder={strings.web} disabled={submitting} {...fields.name} />
      </FormGroup>
      <FormGroup controlId="serverUser" {...fields.user}>
        <ControlLabel>{strings.connect_as}</ControlLabel>
        <FormControl placeholder="deploy" disabled={submitting} {...fields.user} />
      </FormGroup>
      <FormGroup controlId="serverIpAddress" {...fields.ip_address}>
        <ControlLabel>{strings.ip_address}</ControlLabel>
        <FormControl placeholder="192.168.0.1" disabled={submitting} {...fields.ip_address} />
      </FormGroup>
      <FormGroup controlId="serverPort" {...fields.port}>
        <ControlLabel>{strings.port}</ControlLabel>
        <FormControl placeholder="22" disabled={submitting} {...fields.port} />
      </FormGroup>
      <FormGroup controlId="serverPath" {...fields.path}>
        <ControlLabel>{strings.path}</ControlLabel>&nbsp;
        <OverlayTrigger placement="right" overlay={<Tooltip id="serverPath">{strings.example}</Tooltip>}>
          <Icon fa="question-circle" />
        </OverlayTrigger>
        <FormControl placeholder="/var/www/project" disabled={submitting} {...fields.path} />
      </FormGroup>
      <FormGroup controlId="serverOptions">
        <label>{strings.options}</label>
        <Checkbox disabled={submitting} {...fields.deploy_code}>
          {strings.deploy_code}
        </Checkbox>
      </FormGroup>
    </EditorDialog>
  );
};

/* FIXME: Add this

 @if ($project->commands->count() > 0)
 <div class="checkbox" id="add-server-command">
 <label for="server_commands">
 <input type="checkbox" value="1" name="commands" id="server_commands" checked />
 {{ Lang::get('servers.add_command') }}
 </label>
 </div>
 @endif
 </div>
 */

ServerDialog.propTypes = {
  fields: PropTypes.object.isRequired,
  submitting: PropTypes.bool.isRequired,
};

export default ServerDialog;
