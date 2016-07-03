import {
  LOADING_GROUPED_PROJECT_LIST_ACTION,
  RECEIVED_GROUPED_PROJECT_LIST_ACTION,
} from '../constants/actionTypes';

function receivedProjects(projects) {
  return {
    type: RECEIVED_GROUPED_PROJECT_LIST_ACTION,
    projects,
  };
}

function loadingProjectList() {
  return {
    type: LOADING_GROUPED_PROJECT_LIST_ACTION,
  };
}

export function getProjectList() {
  return (dispatch) => {
    dispatch(loadingProjectList());

    return fetch('/app/dashboard', { credentials: 'same-origin' })
      .then(response => response.json())
      .then(json =>
        dispatch(receivedProjects(json))
      )
      .catch(error => console.log(error));
  };
}
