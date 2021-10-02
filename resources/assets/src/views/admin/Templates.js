import $ from 'jquery';

import TemplateCollection from '../../collections/Templates';
import CollectionViewFactory from '../../factories/CollectionViewFactory';
import ModelViewFactory from '../../factories/ModelViewFactory';
import bindDialogs from '../../handlers/dialogs';

const element = 'template';
const translationKey = 'templates';

const ModelView = ModelViewFactory(element, ['name']);

class TemplateView extends ModelView { }

const getInput = () => ({
  name: $(`#${element}_name`).val(),
});

bindDialogs(element, translationKey, getInput, TemplateCollection);

const CollectionView = CollectionViewFactory(element, TemplateCollection, TemplateView);
export default class TemplatesCollectionView extends CollectionView { }
