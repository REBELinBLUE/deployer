import { combineReducers } from 'redux-immutable';
import { reducer as formReducer } from 'redux-form';

import appReducer from './Reducers/app';
import routing from './Reducers/routing';
import navigation from './Reducers/navigation';
import dashboard from './Reducers/dashboard';

const rootReducer = combineReducers({
  app: appReducer,
  form: formReducer,
  routing,
  navigation,
  dashboard,
});

export default rootReducer;
