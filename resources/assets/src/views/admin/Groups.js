import $ from 'jquery';

import GroupCollection from '../../collections/Groups';
import CollectionViewFactory from '../../factories/CollectionViewFactory';
import ModelViewFactory from '../../factories/ModelViewFactory';
import routes from '../../routes';
import reorderModels from '../../handlers/reorderModels';
import bindDialogs from '../../handlers/dialogs';

const element = 'group';
const translationKey = 'groups';

const ModelView = ModelViewFactory(element, ['name']);

class GroupView extends ModelView { }

reorderModels(element, routes.groupsReorder);

const getInput = () => ({
  name: $(`#${element}_name`).val(),
});

bindDialogs(element, translationKey, getInput, GroupCollection);

const CollectionView = CollectionViewFactory(element, GroupCollection, GroupView);
export default class GroupsCollectionView extends CollectionView { }
