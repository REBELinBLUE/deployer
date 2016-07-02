import {
  SET_PAGE_TITLE_ACTION,
  SOCKET_CONNECTION_OFFLINE_ACTION,
  SOCKET_CONNECTION_ONLINE_ACTION,
  RECEIVED_RUNNING_PROJECTS_ACTION,
} from '../constants/actionTypes';

export function setPageTitle(title, subtitle) {
  return {
    type: SET_PAGE_TITLE_ACTION,
    title,
    subtitle,
  };
}

export function socketOffline(error) {
  return {
    type: SOCKET_CONNECTION_OFFLINE_ACTION,
    error,
  };
}

export function socketOnline() {
  return {
    type: SOCKET_CONNECTION_ONLINE_ACTION,
  };
}

export function receivedProjects(response) {
  return {
    type: RECEIVED_RUNNING_PROJECTS_ACTION,
    ...response,
  };
}
