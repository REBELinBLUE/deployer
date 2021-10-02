import $ from 'jquery';
import ace from 'brace';
import 'brace/mode/php';
import 'brace/mode/xml';
import 'brace/mode/ini';
import 'brace/mode/yaml';
import 'brace/mode/json';
import 'brace/mode/sh';

import ConfigFileCollection from '../collections/ConfigFiles';
import CollectionViewFactory from '../factories/CollectionViewFactory';
import ModelViewFactory from '../factories/ModelViewFactory';
import bindDialogs from '../handlers/dialogs';

const element = 'configfile';
const translationKey = 'configFiles';

const ModelView = ModelViewFactory(
  element,
  ['name', 'path'],
  {
    'click .btn-view': 'showFile',
  },
);

let editor;
let openFile;

function createEditor(content, readOnly) {
  editor = ace.edit(content);
  editor.setReadOnly(readOnly || false);
  editor.getSession().setUseWrapMode(true);

  let extension = openFile.substr(openFile.lastIndexOf('.') + 1).toLowerCase();
  if (extension === 'yml') {
    extension = 'yaml';
  }

  if (['php', 'ini', 'yaml', 'sh', 'xml', 'json'].indexOf(extension) !== -1) {
    editor.getSession().setMode(`ace/mode/${extension}`);
  }
}

function destroyEditor() {
  editor.setValue('');
  editor.gotoLine(1);
  editor.destroy();
  openFile = null;
  $(`#${element}_content`).text('');
  $('#preview-content').text('');
}

$(`div#${element}.modal`)
  .on('show.bs.modal', () => {
    openFile = $(`#${element}_path`).val();
    createEditor(`${element}_content`, false);
  })
  .on('hidden.bs.modal', destroyEditor);

$(`div#view-${element}.modal`)
  .on('show.bs.modal', () => createEditor('preview-content', true))
  .on('hidden.bs.modal', destroyEditor);

class ConfigFileView extends ModelView {
  showFile() {
    openFile = this.model.get('path');
    $('#preview-content').text(this.model.get('content'));
  }

  editModel() {
    super.editModel();

    $(`#${element}_content`).text(this.model.get('content'));
  }
}

const getInput = () => ({
  name: $(`#${element}_name`).val(),
  path: $(`#${element}_path`).val(),
  content: editor.getValue(),
  target_type: $('input[name="target_type"]').val(),
  target_id: parseInt($('input[name="target_id"]').val(), 10),
});

bindDialogs(element, translationKey, getInput, ConfigFileCollection);

const CollectionView = CollectionViewFactory(element, ConfigFileCollection, ConfigFileView);
export default class ConfigFilesCollectionView extends CollectionView { }
