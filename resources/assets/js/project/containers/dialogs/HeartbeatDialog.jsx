import HeartBeatDialog from '../../components/healthchecks/heartbeats/HeartBeatDialog';
import { HEARTBEAT_DIALOG } from '../../../dialogs/constants';
import editorDialog from '../../../dialogs/editor';

export default editorDialog({
  dialog: HEARTBEAT_DIALOG,
  fields: ['id', 'project_id'],
})(HeartBeatDialog);
