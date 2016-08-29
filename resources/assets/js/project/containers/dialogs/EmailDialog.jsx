import EmailDialog from '../../components/notifications/emails/EmailDialog';
import { EMAIL_DIALOG } from '../../../dialogs/constants';
import editorDialog from '../../../dialogs/editor';

export default editorDialog({
  dialog: EMAIL_DIALOG,
  fields: ['id', 'project_id', 'name', 'address'],
})(EmailDialog);
