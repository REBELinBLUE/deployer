import NotificationCollection from '../collections/Notifications';
import CollectionViewFactory from '../factories/CollectionViewFactory';
import ModelViewFactory from '../factories/ModelViewFactory';
import bindDialogs from '../handlers/dialogs';
import localize from '../utils/localization';
import { HIPCHAT, TWILIO, MAIL, SLACK } from '../models/Notification';

const element = 'notification';
const translationKey = 'channels';

const ModelView = ModelViewFactory(element, ['name', 'type']);

function getIcon(type) {
  if (type === SLACK) {
    return 'slack';
  } else if (type === HIPCHAT) {
    return 'comment-o';
  } else if (type === MAIL) {
    return 'envelope-o';
  } else if (type === TWILIO) {
    return 'mobile';
  }

  return 'cogs';
}

function setTitleWithIcon(type, action) {
  const modal = `div#${element}.modal`;

  $(`${modal} .modal-title span`).text(localize.get(`channels.${action}_${type}`));

  const iconElement = $(`${modal} .modal-title i`).removeClass().addClass('fa');
  const icon = getIcon(type);

  if (type === HIPCHAT) {
    iconElement.addClass('fa-flip-horizontal');
  }

  iconElement.addClass(`fa-${icon}`);

  $(`${modal} .modal-footer`).show();
  $(`${modal} .channel-config`).hide();
  $(`${modal} #channel-type`).hide();
  $(`${modal} #channel-name`).show();
  $(`${modal} #channel-triggers`).show();
  $(`${modal} #channel-config-${type}`).show();
  $(`${modal} #notification_type`).val(type);
}

class NotificationView extends ModelView {
  viewData() {
    const data = this.model.toJSON();

    let icon = getIcon(this.model.get('type'));
    let label = localize.get('channels.custom');

    if (this.model.isSlack()) {
      label = localize.get('channels.slack');
    } else if (this.model.isHipchat()) {
      icon += ' fa-flip-horizontal';
      label = localize.get('channels.hipchat');
    } else if (this.model.isMail()) {
      label = localize.get('channels.mail');
    } else if (this.model.isTwilio()) {
      label = localize.get('channels.twilio');
    }

    return {
      ...data,
      icon,
      label,
    };
  }

  editModel() {
    super.editModel();

    const type = this.model.get('type');
    $.each(this.model.get('config'), (field, value) => {
      $(`div#${element}.modal #channel-config-${type} #${element}_config_${field}`).val(value);
    });

    const properties = [
      'on_deployment_success', 'on_deployment_failure', 'on_link_down', 'on_link_still_down', 'on_link_recovered',
      'on_heartbeat_missing', 'on_heartbeat_still_missing', 'on_heartbeat_recovered',
    ];

    properties.forEach((field) => {
      $(`#${element}_${field}`).prop('checked', (this.model.get(field) === true));
    });

    setTitleWithIcon(this.model.get('type'), 'edit');
  }
}

$(`div#${element}.modal`).on('show.bs.modal', (event) => {
  const button = $(event.relatedTarget);
  if (!button.hasClass('btn-edit')) {
    $('#notification :input[id^="notification_config"]').val('');
    $('#notification .channel-config input[type="checkbox"]').prop('checked', true);
    $('#notification .modal-footer').hide();
    $('.channel-config').hide();
    $('#channel-type').show();
  }
});

$(`div#${element}.modal #channel-type a.btn-app`).on('click', (event) => {
  const button = $(event.currentTarget);
  const dialog = button.parents('.modal');

  if (button.attr('disabled')) {
    $('.callout-warning', dialog).show();
    return;
  }

  $('.callout-warning', dialog).hide();
  setTitleWithIcon(button.data('type'), 'create');
});

const getInput = () => {
  const data = {
    config: null,
    name: $(`#${element}_name`).val(),
    type: $(`#${element}_type`).val(),
    project_id: parseInt($('input[name="project_id"]').val(), 10),
    on_deployment_success: $(`#${element}_on_deployment_success`).is(':checked'),
    on_deployment_failure: $(`#${element}_on_deployment_failure`).is(':checked'),
    on_link_down: $(`#${element}_on_link_down`).is(':checked'),
    on_link_still_down: $(`#${element}_on_link_still_down`).is(':checked'),
    on_link_recovered: $(`#${element}_on_link_recovered`).is(':checked'),
    on_heartbeat_missing: $(`#${element}_on_heartbeat_missing`).is(':checked'),
    on_heartbeat_recovered: $(`#${element}_on_heartbeat_recovered`).is(':checked'),
    on_heartbeat_still_missing: $(`#${element}_on_heartbeat_still_missing`).is(':checked'),
  };

  $(`#${element} #channel-config-${data.type} :input[id^="notification_config"]`).each((key, field) => {
    const name = $(field).attr('name');
    data[name] = $(field).val();
  });

  return data;
};

bindDialogs(element, translationKey, getInput, NotificationCollection);

const CollectionView = CollectionViewFactory(element, NotificationCollection, NotificationView);
export default class NotificationsCollectionView extends CollectionView { }
