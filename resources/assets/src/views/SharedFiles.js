import $ from 'jquery';

import SharedFileCollection from '../collections/SharedFiles';
import CollectionViewFactory from '../factories/CollectionViewFactory';
import ModelViewFactory from '../factories/ModelViewFactory';
import bindDialogs from '../handlers/dialogs';

const element = 'sharedfile';
const translationKey = 'sharedFiles';

const ModelView = ModelViewFactory(element, ['name', 'file']);

class SharedFileView extends ModelView { }

const getInput = () => ({
  name: $(`#${element}_name`).val(),
  file: $(`#${element}_file`).val(),
  target_type: $('input[name="target_type"]').val(),
  target_id: parseInt($('input[name="target_id"]').val(), 10),
});

bindDialogs(element, translationKey, getInput, SharedFileCollection);

const CollectionView = CollectionViewFactory(element, SharedFileCollection, SharedFileView);
export default class SharedFilesCollectionView extends CollectionView { }
