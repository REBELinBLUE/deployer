import fetch from 'isomorphic-fetch';

import * as actions from './actionTypes';

function receivedProjects(response) {
  return {
    type: actions.RECEIVED_RUNNING_PROJECTS,
    ...response,
  };
}

export function getRunningProjects() {
  return dispatch => (
    fetch('/app/running', { credentials: 'same-origin' })
      .then(response => response.json())
      .then(json =>
        dispatch(receivedProjects(json))
      )
      .catch(error => console.log(error))
  );
}
