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

export function showKey() {
  return {
    type: actions.SHOW_SSH_KEY,
  };
}

export function hideKey() {
  return {
    type: actions.HIDE_SSH_KEY,
  };
}
