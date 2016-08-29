import FileDialog from '../../components/files/configuration/FileDialog';
import { CONFIGURATION_DIALOG } from '../../../dialogs/constants';
import editorDialog from '../../../dialogs/editor';

export default editorDialog({
  dialog: CONFIGURATION_DIALOG,
  fields: ['id', 'project_id', 'name', 'path', 'content'],
})(FileDialog);
