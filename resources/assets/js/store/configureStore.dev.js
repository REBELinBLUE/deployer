import { createStore, compose, applyMiddleware } from 'redux';
import { persistState } from 'redux-devtools';
import { routerMiddleware } from 'react-router-redux';
import { hashHistory } from 'react-router';
import thunk from 'redux-thunk';

import rootReducers from '../rootReducer';
import { ReduxDevTools } from '../Containers/DevTools';

const router = routerMiddleware(hashHistory);

// Read the key from ?debug_session=<key> in the address bar
const getDebugSessionKey = () => {
  const matches = window.location.href.match(/[?&]debug_session=([^&]+)\b/);
  return (matches && matches.length > 0) ? matches[1] : null;
};

const enhancer = compose(
  // Apply middleware first
  applyMiddleware(router, thunk),
  // Enable Redux DevTools with the monitors
  window.devToolsExtension ? window.devToolsExtension() : ReduxDevTools.instrument(),
  // Lets you write ?debug_session=<key> in address bar to persist debug sessions
  persistState(getDebugSessionKey())
);

export default (initialState) => createStore(rootReducers, initialState, enhancer);
