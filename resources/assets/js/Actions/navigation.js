import {
  RECEIVED_RUNNING_PROJECTS_ACTION,
} from '../constants/actionTypes';

export function receivedProjects(response) {
  return {
    type: RECEIVED_RUNNING_PROJECTS_ACTION,
    ...response,
  };
}
