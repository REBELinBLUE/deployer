import React from 'react';
import { render } from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import { Provider } from 'react-redux';
import { Router, Route, IndexRoute, browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import client from 'socket.io-client';

// Routes
import { indexRoute, routes } from './routes';

// Containers
import App from './Containers/App';
import Tools from './Containers/DevTools';

import configureStore from './store';

import { socketOffline, socketOnline } from './Actions/app';

// Setup the app
const store = configureStore({
  app: appConfig
});

Lang.setLocale(appConfig.locale);

const socket = client.connect(appConfig.socket.server, {
  query: `jwt=${appConfig.socket.jwt}`,
});

socket.on('connect_error', (error) => store.dispatch(socketOffline(error)));
socket.on('connect', () => store.dispatch(socketOnline()));
socket.on('reconnect', () => store.dispatch(socketOnline()));

const history = syncHistoryWithStore(browserHistory, store);

injectTapEventPlugin();

render((
  <Provider store={store}>
    <div>
      <Router history={history}>
        <Route path="/" component={App}>
          <IndexRoute component={indexRoute.component} />
          {
            routes.map((route, index) => (
              <Route path={route.url} component={route.component} key={index} />
            ))
          }
        </Route>
      </Router>
      <Tools />
    </div>
  </Provider>
), document.getElementById('content'));
