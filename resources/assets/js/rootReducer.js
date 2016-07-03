import { combineReducers } from 'redux';
import { reducer as formReducer } from 'redux-form';
import { routerReducer } from 'react-router-redux';

import appReducer from './Reducers/app';
import navigation from './Reducers/navigation';
import dashboard from './Reducers/dashboard';

const rootReducer = combineReducers({
  app: appReducer,
  form: formReducer,
  routing: routerReducer,
  navigation,
  dashboard,
});

export default rootReducer;
