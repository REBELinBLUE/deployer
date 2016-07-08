import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  server: null,
  online: false,
  jwt: null,
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.SOCKET_CONNECTION_OFFLINE:
      return state.merge({
        online: false,
      });
    case actions.SOCKET_CONNECTION_ONLINE:
      return state.merge({
        online: true,
      });
    default:
      return state;
  }
}
