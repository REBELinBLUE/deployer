import {
  LOADING_DASHBOARD_PROJECT_LIST_ACTION,
  POPULATE_DASHBOARD_PROJECT_LIST_ACTION,
} from '../constants/actionTypes';

function loadingProjectList() {
  return {
    type: LOADING_DASHBOARD_PROJECT_LIST_ACTION,
  };
}

function receivedProjects(projects) {
  return {
    type: POPULATE_DASHBOARD_PROJECT_LIST_ACTION,
    projects,
  };
}

export function getProjectList() {
  return (dispatch) => {
    dispatch(loadingProjectList());

    return fetch('/api/projects', { credentials: 'same-origin' })
      .then(response => response.json())
      .then(json =>
        dispatch(receivedProjects(json))
      )
      .catch(error => console.log(error));
  };
}
