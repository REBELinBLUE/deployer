import { combineReducers } from 'redux';
import { reducer as formReducer } from 'redux-form';
import { routerReducer } from 'react-router-redux';

import appReducer from './Reducers/app';

const rootReducer = combineReducers({
  app: appReducer,
  form: formReducer,
  routing: routerReducer,
});

export default rootReducer;
