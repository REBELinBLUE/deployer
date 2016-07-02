import {
  SET_PAGE_TITLE_ACTION,
  SOCKET_CONNECTION_OFFLINE_ACTION,
  SOCKET_CONNECTION_ONLINE_ACTION,
} from '../constants/actionTypes';

const initialState = {
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
};

export default function (state = initialState, action) {
  switch (action.type) {
    case SET_PAGE_TITLE_ACTION:
      return {
        ...state,
        title: action.title,
        subtitle: action.subtitle,
      };
    case SOCKET_CONNECTION_OFFLINE_ACTION:
      return {
        ...state,
        socket: {
          ...state.socket,
          online: false,
        },
        socketOnline: false,
      };
    case SOCKET_CONNECTION_ONLINE_ACTION:
      return {
        ...state,
        socket: {
          ...state.socket,
          online: true,
        },
        socketOnline: true,
      };
    default:
      return state;
  }
}
