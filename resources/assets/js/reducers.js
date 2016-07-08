import { combineReducers } from 'redux-immutable';
import { reducer as formReducer } from 'redux-form';

import app from './app';
import dashboard from './dashboard';
import navigation from './navigation';
import routerReducer from './router';
import socket from './socket';

export default combineReducers({
  form: formReducer,
  routing: routerReducer,
  [app.constants.NAME]: app.reducer,
  [dashboard.constants.NAME]: dashboard.reducer,
  [navigation.constants.NAME]: navigation.reducer,
  [socket.constants.NAME]: socket.reducer,
});
