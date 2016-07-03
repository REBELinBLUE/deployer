import {
  SET_PAGE_TITLE_ACTION,
  SET_PAGE_SUBTITLE_ACTION,
  SOCKET_CONNECTION_OFFLINE_ACTION,
  SOCKET_CONNECTION_ONLINE_ACTION,
} from '../constants/actionTypes';

export function setPageTitle(title, subtitle) {
  return {
    type: SET_PAGE_TITLE_ACTION,
    title,
    subtitle: subtitle || null,
  };
}

export function setSubTitle(subtitle) {
  return {
    type: SET_PAGE_SUBTITLE_ACTION,
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

