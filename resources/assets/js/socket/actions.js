import * as actions from './actionTypes';

export function offline(error) {
  return {
    type: actions.SOCKET_CONNECTION_OFFLINE,
    error,
  };
}

export function online() {
  return {
    type: actions.SOCKET_CONNECTION_ONLINE,
  };
}
