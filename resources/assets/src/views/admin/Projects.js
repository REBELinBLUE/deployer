import $ from 'jquery';
import 'select2';

import ProjectCollection from '../../collections/Projects';
import CollectionViewFactory from '../../factories/CollectionViewFactory';
import ModelViewFactory from '../../factories/ModelViewFactory';
import { dateTimeFormatter } from '../../utils/formatters';
import bindDialogs from '../../handlers/dialogs';
import UserCollection from '../../collections/Users';

const element = 'project';
const translationKey = 'projects';

const ModelView = ModelViewFactory(
  element,
  ['name', 'repository', 'branch', 'group_id', 'builds_to_keep', 'url', 'build_url'],
);


const selectOptions = {
  width: '100%',
  minimumResultsForSearch: Infinity,
};


/**
 * Autocomplete feature for project's members
 */
$('.members_autocomplete').tagsinput({
  allowDuplicates: false,
  freeInput: false,
  itemText: 'text',
  itemValue: 'value',
  typeahead: {
    name: 'users',
    source: query => UserCollection.filter(user =>
      // TODO: Maybe move this to a method on the collection instead, but it would
      //       require the collection to be changed so it isn't simply generated

      user.get('name').toLowerCase().startsWith(query.toLowerCase())).map(user => ({
      value: user.get('id'),
      text: user.get('name'),
    })),
    afterSelect() {
      this.$element[0].value = '';
    },
  },
});

// Needs work to exclude already added users and maybe to change the filter so it is more than just startsWith

// FIXME: Don't want this on every page
$(`#${element}_group_id`).select2(selectOptions);
$(`#${element}_template_id`).select2(selectOptions);

$(`div#${element}.modal`)
  .on('show.bs.modal', (event) => {
    const dialog = $(event.currentTarget);

    $('#template-list', dialog).hide();

    const button = $(event.relatedTarget);
    if (!button.hasClass('btn-edit')) {
      $('#template-list', dialog).show();
    }
  });

class ProjectView extends ModelView {
  viewData() {
    const data = this.model.toJSON();

    return {
      ...data,
      deploy: data.last_run ? dateTimeFormatter(data.last_run) : null,
    };
  }

  editModel() {
    super.editModel();

    $(`#${element}_allow_other_branch`).prop('checked', (this.model.get('allow_other_branch') === true));
    $(`#${element}_include_dev`).prop('checked', (this.model.get('include_dev') === true));
    $(`#${element}_private_key`).val('');

    // Displaying project's managers
    const users = this.model.get('users');
    // FIXME: Remove the for loops
    for (const i in users) { // eslint-disable-line
      if (users[i].pivot.role === 'manager') {
        // This user is a manager
        $(`#${element}_managers`).tagsinput('add', { value: users[i].id, text: users[i].name });
      } else if (users[i].pivot.role === 'user') {
        // This user is a simple user
        $(`#${element}_users`).tagsinput('add', { value: users[i].id, text: users[i].name });
      }
    }
  }
}

const getInput = () => ({
  name: $(`#${element}_name`).val(),
  repository: $(`#${element}_repository`).val(),
  branch: $(`#${element}_branch`).val(),
  group_id: parseInt($(`#${element}_group_id`).val(), 10),
  builds_to_keep: $(`#${element}_builds_to_keep`).val(),
  url: $(`#${element}_url`).val(),
  build_url: $(`#${element}_build_url`).val(),
  template_id: $(`#${element}_template_id`) ? parseInt($(`#${element}_template_id`).val(), 10) : null,
  allow_other_branch: $(`#${element}_allow_other_branch`).is(':checked'),
  include_dev: $(`#${element}_include_dev`).is(':checked'),
  private_key: $(`#${element}_private_key`).val(),
  managers: $(`#${element}_managers`).val(),
  users: $(`#${element}_users`).val(),
});

bindDialogs(element, translationKey, getInput, ProjectCollection);

const CollectionView = CollectionViewFactory(element, ProjectCollection, ProjectView);
export default class ProjectsCollectionView extends CollectionView { }
