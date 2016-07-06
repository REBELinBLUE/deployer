import fetch from 'isomorphic-fetch';

import {
  RECEIVED_RUNNING_PROJECTS_ACTION,
} from '../constants/actionTypes';

function receivedProjects(response) {
  return {
    type: RECEIVED_RUNNING_PROJECTS_ACTION,
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
