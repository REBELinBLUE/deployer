import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import client from 'socket.io-client';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { Router, browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import 'babel-polyfill';

import attachStoreToRoutes from './routes';
import configureStore from './store';
import { socketOffline, socketOnline } from './actions/app';

injectTapEventPlugin();

function deployer(config, mountNode) {
  const store = configureStore({
    app: {
      ...config,
      loaded: false,
    },
  });

  Lang.setLocale(config.locale);

  const socket = client.connect(config.socket.server, {
    query: `jwt=${config.socket.jwt}`,
  });

  socket.on('connect_error', (error) => store.dispatch(socketOffline(error)));
  socket.on('connect', () => store.dispatch(socketOnline()));
  socket.on('reconnect', () => store.dispatch(socketOnline()));

  const routes = attachStoreToRoutes(store);
  const history = syncHistoryWithStore(browserHistory, store, {
    selectLocationState(state) {
      return state.get('routing').toObject();
    },
  });

  // history.listen(location => console.log(location););

  render(
    <Provider store={store}>
      <Router history={history} routes={routes} />
    </Provider>, mountNode);
}

deployer(appConfig, document.getElementById('content'));
