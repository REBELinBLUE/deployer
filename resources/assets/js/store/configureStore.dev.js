/* eslint-disable no-console */
import { createStore, compose, applyMiddleware } from 'redux';
import { persistState } from 'redux-devtools';
import { routerMiddleware } from 'react-router-redux';
import reduxUnhandledAction from 'redux-unhandled-action';
import { browserHistory } from 'react-router';
import thunk from 'redux-thunk';
import Immutable from 'immutable';

import rootReducers from '../reducers';
import { ReduxDevTools } from '../app/containers/DevTools';

const router = routerMiddleware(browserHistory);
const unhandledAction = reduxUnhandledAction((action) => {
  console.groupCollapsed(`"${action.type}" did not lead to creation of a new state object`);
  console.error(action);
  console.groupEnd();
});

// Read the key from ?debug_session=[key] in the address bar
const getDebugSessionKey = () => {
  const matches = window.location.href.match(/[?&]debug_session=([^&]+)\b/);
  return (matches && matches.length > 0) ? matches[1] : null;
};

const enhancer = compose(
  // Apply middleware first
  applyMiddleware(router, thunk, unhandledAction),
  // Enable Redux DevTools with the monitors
  window.devToolsExtension ? window.devToolsExtension() : ReduxDevTools.instrument(),
  // Lets you write ?debug_session=[key] in address bar to persist debug sessions
  persistState(getDebugSessionKey())
);

export default (initialState) => {
  const store = createStore(rootReducers, Immutable.fromJS(initialState), enhancer);

  // Hot reload reducers
  if (module.hot) {
    // eslint-disable-next-line global-require
    module.hot.accept('../reducers', () => store.replaceReducer(require('../reducers').default));
  }

  return store;
};
