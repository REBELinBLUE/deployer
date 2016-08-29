import ServerDialog from '../../components/servers/ServerDialog';
import { SERVER_DIALOG } from '../../../dialogs/constants';
import editorDialog from '../../../dialogs/editor';

export default editorDialog({
  dialog: SERVER_DIALOG,
  fields: ['id', 'project_id', 'name', 'user', 'ip_address', 'port', 'path', 'deploy_code'],
})(ServerDialog);
