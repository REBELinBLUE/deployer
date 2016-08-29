import VariableDialog from '../../components/commands/variables/VariableDialog';
import { VARIABLE_DIALOG } from '../../../dialogs/constants';
import editorDialog from '../../../dialogs/editor';

export default editorDialog({
  dialog: VARIABLE_DIALOG,
  fields: ['id', 'project_id', 'name', 'value'],
})(VariableDialog);
