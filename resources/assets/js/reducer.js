import { combineReducers } from 'redux-immutable';
import { reducer as formReducer } from 'redux-form';

import app from './app';
import navigation from './navigation';
import dashboard from './dashboard';
import router from './router';

const rootReducer = combineReducers({
  form: formReducer,
  [app.constants.NAME]: app.reducer,
  [router.constants.NAME]: router.reducer,
  [navigation.constants.NAME]: navigation.reducer,
  [dashboard.constants.NAME]: dashboard.reducer,
});

export default rootReducer;
