import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import client from 'socket.io-client';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { Router, Route, IndexRoute, hashHistory } from 'react-router'; // TODO: Replace hashHistory with browserHistory if possible
import { syncHistoryWithStore } from 'react-router-redux';

import routes from './routes';
import configureStore from './store';
import { socketOffline, socketOnline } from './Actions/app';

injectTapEventPlugin();

function deployer(config, mountNode) {
  const store = configureStore({
    app: config,
  });

  Lang.setLocale(config.locale);

  const socket = client.connect(config.socket.server, {
    query: `jwt=${config.socket.jwt}`,
  });

  socket.on('connect_error', (error) => store.dispatch(socketOffline(error)));
  socket.on('connect', () => store.dispatch(socketOnline()));
  socket.on('reconnect', () => store.dispatch(socketOnline()));

  const history = syncHistoryWithStore(hashHistory, store);

  render(
    <Provider store={store}>
      <Router history={history} routes={routes} />
    </Provider>, mountNode);
}

deployer(appConfig, document.getElementById('content'));
