import * as actions from './actionTypes';

export function appHasLoaded() {
  return {
    type: actions.APP_PRELOAD_COMPLETE,
  };
}
export function setPageTitle(title, subtitle) {
  return {
    type: actions.SET_PAGE_TITLE,
    title,
    subtitle: subtitle || null,
  };
}

export function setSubTitle(subtitle) {
  return {
    type: actions.SET_PAGE_SUBTITLE,
    subtitle,
  };
}

export function socketOffline(error) {
  return {
    type: actions.SOCKET_CONNECTION_OFFLINE,
    error,
  };
}

export function socketOnline() {
  return {
    type: actions.SOCKET_CONNECTION_ONLINE,
  };
}

