import * as actions from './actionTypes';

export function setProject(project) {
  return {
    type: actions.SET_ACTIVE_PROJECT,
    project,
  };
}

export function fetchProject(project) {
  return {
    type: actions.FETCH_PROJECT,
    project,
  };
}
