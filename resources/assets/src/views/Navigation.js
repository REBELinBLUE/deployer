import $ from 'jquery';
import _ from 'underscore';
import toastr from 'toastr';

import listener from '../listener';
import { timeFormatter } from '../utils/formatters';
import localize from '../utils/localization';
import Deployment, { RUNNING, PENDING } from '../models/Deployment';
import { getProjectId } from '../utils/projectId';

function updateNotificationsMenus() {
  if ($('#pending_menu ul.menu li').length > 0) {
    $('#pending_menu').show();
  }

  if ($('#running_menu ul.menu li').length > 0) {
    $('#running_menu').show();
  }
}

function updateMenu(template, model, element, status) {
  $(`#${element}_menu`).show();

  const html = template(model);

  if (model.status === status) {
    $(`#${element}_menu ul.menu`).append(html);
  }

  const count = $(`#${element}_menu ul.menu li`).length;

  if (count === 0) {
    $(`#${element}_menu`).hide();
  }

  $(`#${element}_menu span.label-warning`).html(count);
  $(`#${element}_menu .header`).text(localize.choice(`dashboard.${element}`, count, { count }));
}

function updateNavBar(data) {
  const model = {
    ...data.model,
    time: timeFormatter(data.model.started_at),
    url: `/deployment/${data.model.id}`,
  };

  $(`#deployment_info_${model.id}`).remove();

  const template = _.template($('#deployment-list-template').html());

  updateMenu(template, model, 'pending', PENDING);
  updateMenu(template, model, 'running', RUNNING);
}

function getMessage(title, translation) {
  return `${title} - ${localize.get(translation)}`;
}

// FIXME: Convert to class
export default () => {
  $(document).ready(updateNotificationsMenus);

  listener.onTrash('project', (data) => {
    $(`#sidebar_project_${data.model.id}`).parent('li').remove();

    // FIXME: Maybe send an alert
    if (parseInt(data.model.id, 10) === getProjectId()) {
      window.location.href = '/';
    }
  });

  listener.onUpdate('group', (data) => {
    $(`#sidebar_group_${data.model.id}`).html(data.model.name);
  });

  listener.onUpdate('project', (data) => {
    $(`#sidebar_project_${data.model.id}`).html(data.model.name);
  });

  listener.onUpdate('deployment', (data) => {
    updateNavBar(data);

    if ($('#timeline').length === 0) {
      const deployment = new Deployment(data.model);

      const title = localize.get('dashboard.deployment_number', {
        id: data.model.id,
      });

      if (deployment.isCompleted()) {
        toastr.success(getMessage(title, 'deployments.completed'), data.model.project_name);
      } else if (deployment.isFailed()) {
        toastr.error(getMessage(title, 'deployments.failed'), data.model.project_name);
      } else if (deployment.isCompleteWithErrors()) {
        toastr.warning(getMessage(title, 'deployments.completed_with_errors'), data.model.project_name);
      } // FIXME: Add cancelled
    }
  });
};
