import Immutable from 'immutable';
import { combineReducers } from 'redux-immutable';
import { reducer as formReducer } from 'redux-form';
import {
  LOCATION_CHANGE,
} from 'react-router-redux';

import appReducer from './app/reducer';
import navigation from './navigation/reducer';
import dashboard from './dashboard/reducer';


const initialState = Immutable.fromJS({
  locationBeforeTransitions: null,
});

const routing = (state = initialState, action) => {
  if (action.type === LOCATION_CHANGE) {
    return state.set('locationBeforeTransitions', action.payload);
  }

  return state;
}

const rootReducer = combineReducers({
  app: appReducer,
  form: formReducer,
  routing,
  navigation,
  dashboard,
});

export default rootReducer;
