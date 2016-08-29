import FileDialog from '../../components/files/persistent/FileDialog';
import { PERSISTENT_DIALOG } from '../../../dialogs/constants';
import editorDialog from '../../../dialogs/editor';

export default editorDialog({
  dialog: PERSISTENT_DIALOG,
  fields: ['id', 'project_id', 'name', 'file'],
})(FileDialog);
