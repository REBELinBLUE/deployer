import * as actions from '../navigation/actionTypes'; // FIXME: Shouldn't be loading actions from another module

function receivedProjects(projects) {
  return {
    type: actions.RECEIVED_GROUPED_PROJECT_LIST,
    projects,
  };
}

function loadingProjectList() {
  return {
    type: actions.LOADING_GROUPED_PROJECT_LIST,
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
