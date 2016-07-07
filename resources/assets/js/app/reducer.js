import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  loaded: false,
  locale: 'en',
  outdated: false,
  version: null,
  latest: null,
  title: null,
  subtitle: null,
  user: null,
  socket: {
    server: null,
    online: false,
    jwt: null,
  },
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.APP_PRELOAD_COMPLETE:
      return state.merge({
        loaded: true,
      });
    case actions.SET_PAGE_TITLE:
      return state.merge({
        title: action.title,
        subtitle: action.subtitle,
      });
    case actions.SET_PAGE_SUBTITLE:
      return state.merge({
        subtitle: action.subtitle,
      });
    case actions.SOCKET_CONNECTION_OFFLINE:
      return state.setIn(['socket', 'online'], false);
    case actions.SOCKET_CONNECTION_ONLINE:
      return state.setIn(['socket', 'online'], true);
    default:
      return state;
  }
}
