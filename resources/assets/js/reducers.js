import { combineReducers } from 'redux-immutable';

import app from './app';
import dashboard from './dashboard';
import formReducer from './form';
import dialogs from './dialogs';
import project from './project';
import navigation from './navigation';
import routerReducer from './router';
import socket from './socket';

export default combineReducers({
  form: formReducer,
  routing: routerReducer,
  [app.constants.NAME]: app.reducer,
  [dashboard.constants.NAME]: dashboard.reducer,
  [dialogs.constants.NAME]: dialogs.reducer,
  [project.constants.NAME]: project.reducer,
  [navigation.constants.NAME]: navigation.reducer,
  [socket.constants.NAME]: socket.reducer,
});
