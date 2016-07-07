import React from 'react';
import client from 'socket.io-client';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { Router, browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import 'babel-polyfill';

import attachStoreToRoutes from './router/routes';
import configureStore from './store';
import { socketOffline, socketOnline } from './app/actions';

// FIXME: Clean this up, it still seems messy

const store = configureStore({
  app: {
    ...appConfig,
    loaded: false,
  },
});

Lang.setLocale(appConfig.locale);

const socket = client.connect(appConfig.socket.server, {
  query: `jwt=${appConfig.socket.jwt}`,
});

socket.on('connect_error', (error) => store.dispatch(socketOffline(error)));
socket.on('connect', () => store.dispatch(socketOnline()));
socket.on('reconnect', () => store.dispatch(socketOnline()));

const routes = attachStoreToRoutes(store);
const selectLocationState = {
  selectLocationState(state) {
    return state.get('routing').toObject();
  },
};

const history = syncHistoryWithStore(browserHistory, store, selectLocationState);

render(
  <Provider store={store}>
    <Router history={history} routes={routes} />
  </Provider>, document.getElementById('content'));
