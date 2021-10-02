let projectId = null;

export function getProjectId() {
  return projectId;
}

export function setProjectId(newId) {
  projectId = parseInt(newId, 10);
}
