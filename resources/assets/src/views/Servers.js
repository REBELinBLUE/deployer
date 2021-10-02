import $ from 'jquery';

import localize from '../utils/localization';
import ServerCollection from '../collections/Servers';
import CollectionViewFactory from '../factories/CollectionViewFactory';
import ModelViewFactory from '../factories/ModelViewFactory';
import { logFormatter } from '../utils/formatters';
import routes from '../routes';
import reorderModels from '../handlers/reorderModels';
import bindDialogs from '../handlers/dialogs';


const element = 'server';
const translationKey = 'servers';
const fields = ['name', 'ip_address', 'port', 'user', 'path'];

const ModelView = ModelViewFactory(
  element,
  fields,
  {
    'click .btn-test': 'testConnection',
    'click .btn-view': 'showLog',
  },
);

$(`#${element} #${element}_name`).typeahead({
  autoSelect: false,
  source: (query, process) => $.ajax({
    type: 'GET',
    url: routes.serversAutocomplete,
    data: { query },
  }).done(response => process($.map(response.suggestions, dataItem => ({
    name: `${dataItem.name} (${dataItem.user}@${dataItem.ip_address})`,
    data: dataItem,
  })))),
  afterSelect: (suggestion) => {
    fields.forEach((field) => {
      $(`#${element}_${field}`).val(suggestion.data[field]);
    });

    $(`#${element}_deploy_code`).prop('checked', suggestion.data.deploy_code);
  },
});


class ServerView extends ModelView {
  viewData() {
    const data = this.model.toJSON();

    let css = 'primary';
    let icon = 'question';
    let status = localize.get(`${translationKey}.untested`);
    let hasLog = false;

    if (this.model.isSuccessful()) {
      css = 'success';
      icon = 'check';
      status = localize.get(`${translationKey}.successful`);
    } else if (this.model.isTesting()) {
      css = 'warning';
      icon = 'spinner fa-pulse';
      status = localize.get(`${translationKey}.testing`);
    } else if (this.model.isFailed()) {
      css = 'danger';
      icon = 'warning';
      status = localize.get(`${translationKey}.failed`);
      hasLog = !!data.connect_log;
    }

    return {
      ...data,
      status_css: css,
      icon_css: icon,
      status,
      has_log: hasLog,
    };
  }

  editModel() {
    super.editModel();

    $(`#${element}_deploy_code`).prop('checked', (this.model.get('deploy_code') === true));
  }

  showLog() {
    const modal = $('div.modal#result');

    modal.find('pre').html(logFormatter(this.model.get('connect_log')));
    modal.find('.modal-title span').text(localize.get(`${translationKey}.log_title`));
  }

  testConnection() {
    if (this.model.isTesting()) {
      return;
    }

    this.model.testConnection();
  }
}

reorderModels(element, routes.serversReorder);

const getInput = () => ({
  name: $(`#${element}_name`).val(),
  ip_address: $(`#${element}_ip_address`).val(),
  port: $(`#${element}_port`).val(),
  user: $(`#${element}_user`).val(),
  path: $(`#${element}_path`).val(),
  deploy_code: $(`#${element}_deploy_code`).is(':checked'),
  project_id: parseInt($('input[name="project_id"]').val(), 10),
  add_commands: $(`#${element}_commands`).is(':checked'),
});

bindDialogs(element, translationKey, getInput, ServerCollection);

const CollectionView = CollectionViewFactory(element, ServerCollection, ServerView);
export default class ServersCollectionView extends CollectionView { }
