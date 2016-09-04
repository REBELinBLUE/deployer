import 'isomorphic-fetch';

import * as actions from './actionTypes';

function receivedProjectData(response) {
  return {
    type: actions.LOADED_PROJECT,
    ...response,
  };
}

function isFetching() {
  return {
    type: actions.FETCH_PROJECT,
  };
}

export function setProject(project) {
  return {
    type: actions.SET_ACTIVE_PROJECT,
    project,
  };
}

export function clearActiveProject() {
  return {
    type: actions.CLEAR_ACTIVE_PROJECT,
  };
}

export function fetchProject(project) {
  return (dispatch) => {
    dispatch(isFetching());

    return fetch(`/app/projects/${project.id}`, {
      credentials: 'same-origin',
    })
    .then(response => response.json())
    .then(json => dispatch(receivedProjectData(json)))
    .catch(error => console.log(error)); // FIXME: Need some sort of error handler
  };
}
