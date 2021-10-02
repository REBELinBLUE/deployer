import _ from 'underscore';
import $ from 'jquery';
import Backbone from 'backbone';

import localize from '../utils/localization';
import { timeFormatter, logFormatter } from '../utils/formatters';
import LogCollection from '../collections/Logs';
import listener from '../listener';
import { SERVER_LOG_CHANGED } from '../listener/events';
import { isCurrentProject } from '../utils/target';
import routes from '../routes';

const translationKey = 'deployments';

let fetchingLog = false;
function fetchLog(element, logId) {
  if (fetchingLog) {
    return;
  }

  fetchingLog = true;

  $.ajax({
    type: 'GET',
    url: routes.log(logId),
  }).done((data) => {
    const output = logFormatter(data.output ? data.output : '');

    let atBottom = false;
    if (element.scrollTop() + element.innerHeight() >= element.get(0).scrollHeight) {
      atBottom = true;
    }

    element.html(output);

    if (atBottom) {
      element.scrollTop(element.get(0).scrollHeight);
    }
  }).always(() => {
    fetchingLog = false;
  });
}

function showLogDialog(event) {
  const button = $(event.relatedTarget);
  const dialog = $(event.target);

  const logId = button.attr('id').replace('log_', '');

  const step = $('h3 span', button.parents('.box')).text();
  const log = $('pre', dialog);
  const loader = $('#loading', dialog);

  log.hide();
  loader.show();

  $('#action', dialog).text(step);
  log.text('');

  fetchingLog = true;

  // FIXME: Duplicated
  $.ajax({
    type: 'GET',
    url: routes.log(logId),
  }).done((data) => {
    const output = logFormatter(data.output ? data.output : '');

    log.html(output);

    log.show();
    loader.hide();

    listener.on(`serverlog-${logId}:${SERVER_LOG_CHANGED}`, (changedData) => {
      if (changedData.log_id === parseInt(logId, 10)) {
        fetchLog(log, changedData.log_id);
      }
    });
  }).always(() => {
    fetchingLog = false;
  });
}

function redeploy(event) {
  const button = $(event.relatedTarget);
  const deployment = button.data('deployment-id');

  const tmp = `${button.data('optional-commands')} `.trim(); // FIXME: why is this needed?
  let commands = tmp.split(',');

  if (tmp.length > 0) {
    commands = $.map(commands, value => parseInt(value, 10));
  } else {
    commands = [];
  }

  const dialog = $(event.target);

  $('form', dialog).prop('action', routes.rollback(deployment));

  $('input:checkbox', dialog).each((index, element) => {
    const input = $(element);

    input.prop('checked', false);
    if ($.inArray(parseInt(input.val(), 10), commands) !== -1) {
      input.prop('checked', true);
    }
  });
}

$('#log')
  .on('show.bs.modal', showLogDialog)
  .on('hide.bs.modal', () => {
    fetchingLog = false;
  });


$('#redeploy').on('show.bs.modal', redeploy);

$('.btn-cancel').on('click', (event) => {
  const button = $(event.currentTarget);
  const deployment = button.data('deployment-id');

  $(`form#abort_${deployment}`).trigger('submit');
});

// FIXME: Rename this
class LogView extends Backbone.View {
  constructor(options) {
    super({
      ...options,
      tagName: 'tr',
    });

    this.template = _.template($('#log-template').html());

    this.listeners();
  }

  listeners() {
    this.listenTo(this.model, 'change', this.render);
  }

  viewData() {
    const data = this.model.toJSON();

    let css = 'info';
    let icon = 'clock-o';
    let status = localize.get(`${translationKey}.pending`);

    if (this.model.isCompleted()) {
      css = 'success';
      icon = 'check';
      status = localize.get(`${translationKey}.completed`);
    } else if (this.model.isRunning()) {
      css = 'warning';
      icon = 'spinner fa-pulse';
      status = localize.get(`${translationKey}.running`);
    } else if (this.model.isFailed() || this.model.isCancelled()) {
      css = 'danger';
      icon = 'warning';
      status = localize.get(`${translationKey}.failed`);

      if (this.model.isCancelled()) {
        status = localize.get(`${translationKey}.cancelled`);
      }
    }

    const formattedStartTime = data.started_at ? timeFormatter(data.started_at) : false;
    const formattedEndTime = data.finished_at ? timeFormatter(data.finished_at) : false;

    return {
      ...data,
      formatted_start_time: formattedStartTime,
      formatted_end_time: formattedEndTime,
      status_css: css,
      icon_css: icon,
      status,
    };
  }

  render() {
    this.$el.html(this.template(this.viewData()));

    return this;
  }
}

export default class LogsView extends Backbone.View {
  constructor(options) {
    super({
      ...options,
      el: '#app',
    });

    this.collection = LogCollection;

    this.$containers = [];

    this.setupContainers();
    this.listeners();
    this.render();
  }

  setupContainers() {
    $('.deploy-step tbody').each((index, element) => {
      this.$containers.push({
        step: parseInt($(element).attr('id').replace('step_', ''), 10),
        element,
      });
    });
  }

  listeners() {
    this.listenTo(this.collection, 'add', this.addOne);
    this.listenTo(this.collection, 'reset', this.addAll);
    this.listenTo(this.collection, 'remove', this.addAll);
    this.listenTo(this.collection, 'all', this.render);

    listener.on(`serverlog:${SERVER_LOG_CHANGED}`, (data) => {
      const deployment = this.collection.get(data.log_id);

      if (deployment) {
        deployment.set({
          status: data.status,
          output: data.output,
          runtime: data.runtime,
          started_at: data.started_at ? data.started_at : false,
          finished_at: data.finished_at ? data.finished_at : false,
        });

        // FIXME: If cancelled update all other deployments straight away
        // FIXME: If completed fake making the next model "running" so it looks responsive
      }
    });

    listener.onUpdate('deployment', (data) => {
      if (isCurrentProject(data.model)) {
        if (data.model.repo_failure) {
          $('#repository_error').show();
        }
      }
    });
  }

  addOne(model) {
    const view = new LogView({
      model,
    });

    const found = _.find(this.$containers, element =>
      parseInt(element.step, 10) === parseInt(model.get('deploy_step_id'), 10));

    $(found.element).append(view.render().el);
  }

  addAll() {
    $(this.$containers).each((index, element) => {
      element.html('');
    });

    this.collection.each(this.addOne, this);
  }
}
