import $ from 'jquery';

import listener from '../listener';
import routes from '../routes';
import localize from '../utils/localization';
import Project from '../models/Project';
import { dateTimeFormatter } from '../utils/formatters';

function updateTimeline() {
  $.ajax({
    type: 'GET',
    url: routes.timeline,
  }).success((response) => {
    $('#timeline').html(response);
  });
}

export default () => {
  listener.onUpdate('deployment', updateTimeline);

  // FIXME: Change to use an actual model
  listener.onUpdate('project', (data) => {
    const project = new Project(data.model);

    const container = $(`#project_${data.model.id}`);

    if (container.length > 0) {
      let icon = 'question-circle';
      let css = 'primary';
      let label = localize.get('projects.not_deployed');

      if (project.isFinished()) {
        icon = 'check';
        css = 'success';
        label = localize.get('projects.finished');
      } else if (project.isDeploying()) {
        icon = 'spinner fa-pulse';
        css = 'warning';
        label = localize.get('projects.deploying');
      } else if (project.isFailed()) {
        icon = 'warning';
        css = 'danger';
        label = localize.get('projects.failed');
      } else if (project.isPending()) {
        icon = 'clock-o';
        css = 'info';
        label = localize.get('projects.pending');
      }

      const status = $('td:nth-child(3) span.label', container);

      $('td:first a', project).text(data.model.name);
      $('td:nth-child(2)', project).text(dateTimeFormatter(data.model.last_run)); // FIXME: Error if not yet run
      status.attr('class', `label label-${css}`);
      $('i', status).attr('class', `fa fa-${icon}`);
      $('span', status).text(label);
    }
  });
};
