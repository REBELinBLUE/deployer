import $ from 'jquery';

import UserCollection from '../../collections/Users';
import CollectionViewFactory from '../../factories/CollectionViewFactory';
import ModelViewFactory from '../../factories/ModelViewFactory';
import { dateTimeFormatter } from '../../utils/formatters';
import bindDialogs from '../../handlers/dialogs';

const element = 'user';
const translationKey = 'users';

const ModelView = ModelViewFactory(element, ['name', 'email']);

class UserView extends ModelView {
  viewData() {
    const data = this.model.toJSON();

    return {
      ...data,
      created: dateTimeFormatter(data.created_at),
    };
  }

  editModel() {
    super.editModel();

    $(`#${element}_password`).val('');
    $(`#${element}_is_admin`).prop('checked', (this.model.get('is_admin') === true));

    // Cannot remove admin role to first user (otherwise looses all access)
    $(`#${element}_is_admin`).prop('disabled', (this.model.get('id') === 1));
  }
}

const getInput = () => ({
  name: $(`#${element}_name`).val(),
  email: $(`#${element}_email`).val(),
  password: $(`#${element}_password`).val(),
  password_confirmation: $(`#${element}_password_confirmation`).val(),
  is_admin: $(`#${element}_is_admin`).is(':checked'),
});

bindDialogs(element, translationKey, getInput, UserCollection);

const CollectionView = CollectionViewFactory(element, UserCollection, UserView);
export default class UsersCollectionView extends CollectionView { }
