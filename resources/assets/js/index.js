import React from 'react';
import client from 'socket.io-client';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { Router, browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import 'babel-polyfill';

import attachStoreToRoutes from './router/routes';
import configureStore from './store';
import * as actions from './socket/actions';

// FIXME: Clean this up, it still seems messy

const PRELOADED = window.__PRELOADED_STATE__; // eslint-ignore-line no-underscore-dangle

Lang.setLocale(PRELOADED.deployer.locale);

// FIXME: deployer/socket should come from the constants, is there a better place for all of this? and so we don't have to key redefining everything!
const store = configureStore({
  deployer: {
    ...PRELOADED.deployer,
    title: Lang.get('app.name'),
  },
  socket: {
    ...PRELOADED.socket,
    online: false,
  },
  navigation: {
    ...PRELOADED.navigation,
  },
  dashboard: {
    ...PRELOADED.dashboard,
  },
});

// FIXME: Really don't like all this here, should be somewhere else and the variables should be coming from state
const socket = client.connect(PRELOADED.socket.server, {
  query: `jwt=${PRELOADED.socket.jwt}`,
});

socket.on('connect_error', (error) => store.dispatch(actions.offline(error)));
socket.on('connect', () => store.dispatch(actions.online()));
socket.on('reconnect', () => store.dispatch(actions.online()));

const routes = attachStoreToRoutes(store);
const selectLocationState = {
  selectLocationState(state) {
    return state.get('routing').toObject(); // FIXME: Use the NAME from routing, maybe move this there?
  },
};

const history = syncHistoryWithStore(browserHistory, store, selectLocationState);

render(
  <Provider store={store}>
    <Router history={history} routes={routes} />
  </Provider>, document.getElementById('content'));
