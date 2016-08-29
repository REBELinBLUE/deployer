import LinkDialog from '../../components/healthchecks/links/LinkDialog';
import { LINK_DIALOG } from '../../../dialogs/constants';
import editorDialog from '../../../dialogs/editor';

export default editorDialog({
  dialog: LINK_DIALOG,
  fields: ['id', 'project_id'],
})(LinkDialog);
