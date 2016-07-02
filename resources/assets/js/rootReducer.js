import { combineReducers } from 'redux';
import { reducer as formReducer } from 'redux-form';
import { routerReducer } from 'react-router-redux';

import appReducer from './Reducers/app';
import projects from './Reducers/projects';

const rootReducer = combineReducers({
  app: appReducer,
  form: formReducer,
  routing: routerReducer,
  projects,
});

export default rootReducer;
