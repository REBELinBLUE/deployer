import React from 'react';
import { render } from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import { Provider } from 'react-redux';
import { Router, Route, IndexRoute, hashHistory } from 'react-router'; // TODO: Replace hashHistory with browserHistory if possible
import { syncHistoryWithStore } from 'react-router-redux';
import client from 'socket.io-client';

// Routes
import { indexRoute, routes } from './routes';

// Containers
import App from './Containers/App';
import Tools from './Containers/DevTools';

import configureStore from './store';

import { socketOffline, socketOnline } from './Actions/app';

injectTapEventPlugin();

function Deployer(config, mountNode) {
  const store = configureStore({
    app: config
  });

  Lang.setLocale(config.locale);

  const socket = client.connect(config.socket.server, {
    query: `jwt=${config.socket.jwt}`,
  });

  socket.on('connect_error', (error) => store.dispatch(socketOffline(error)));
  socket.on('connect', () => store.dispatch(socketOnline()));
  socket.on('reconnect', () => store.dispatch(socketOnline()));

  const history = syncHistoryWithStore(hashHistory, store);

  render((
    <Provider store={store}>
      <div>
        <Router history={history}>
          <Route path="/" component={App}>
            <IndexRoute component={indexRoute.component}/>
            {
              routes.map((route, index) => (
                <Route path={route.url} component={route.component} key={index}/>
              ))
            }
          </Route>
        </Router>
        <Tools />
      </div>
    </Provider>
  ), mountNode);
};

// FIXME: How do I do this properly from the HTML file?
Deployer(appConfig, document.getElementById('content'));
