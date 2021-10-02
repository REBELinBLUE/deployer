import $ from 'jquery';

export function isCurrentTarget(data) {
  const targetType = $('input[name="target_type"]').val();
  const targetId = $('input[name="target_id"]').val();

  return (targetType === data.target_type && parseInt(data.target_id, 10) === parseInt(targetId, 10));
}

export function isCurrentProject(data) {
  return (parseInt(data.project_id, 10) === window.app.getProjectId());
}
