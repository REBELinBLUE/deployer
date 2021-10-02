import $ from 'jquery';

import localize from '../utils/localization';
import CheckUrlCollection from '../collections/CheckUrls';
import CollectionViewFactory from '../factories/CollectionViewFactory';
import ModelViewFactory from '../factories/ModelViewFactory';
import { dateTimeFormatter } from '../utils/formatters';
import bindDialogs from '../handlers/dialogs';

const element = 'checkurl';
const translationKey = 'checkUrls';

const ModelView = ModelViewFactory(
  element,
  ['name', 'url', 'match'],
  {
    'click .btn-view': 'showLog',
  },
);

class CheckUrlView extends ModelView {
  viewData() {
    const data = this.model.toJSON();

    let css = 'primary';
    let icon = 'question';
    let status = localize.get(`${translationKey}.untested`);
    let hasRun = false;
    let hasLog = false;

    if (this.model.isOffline()) {
      css = 'danger';
      icon = 'warning';
      status = localize.get(`${translationKey}.failed`);
      hasRun = !!data.last_seen;
      hasLog = !!data.last_log;
    } else if (this.model.isOnline()) {
      css = 'success';
      icon = 'check';
      status = localize.get(`${translationKey}.successful`);
      hasRun = true;
    }

    return {
      ...data,
      status_css: css,
      icon_css: icon,
      status,
      has_run: hasRun,
      has_log: hasLog,
      interval_label: localize.get(`${translationKey}.length`, { time: data.period }),
      formatted_date: hasRun ? dateTimeFormatter(data.last_seen) : null,
    };
  }

  showLog() {
    const modal = $('div.modal#result');

    modal.find('pre').text(this.model.get('last_log'));
    modal.find('.modal-title span').text(localize.get(`${translationKey}.log_title`));
  }

  editModel() {
    super.editModel();

    $(`#${element}_period_${this.model.get('period')}`).prop('checked', true);
  }
}

const getInput = () => ({
  name: $(`#${element}_name`).val(),
  url: $(`#${element}_url`).val(),
  match: $(`#${element}_match`).val(),
  period: parseInt($('input[name="period"]:checked').val(), 10),
  project_id: parseInt($('input[name="project_id"]').val(), 10),
});

bindDialogs(element, translationKey, getInput, CheckUrlCollection);

const CollectionView = CollectionViewFactory(element, CheckUrlCollection, CheckUrlView);
export default class CheckUrlsCollectionView extends CollectionView { }
