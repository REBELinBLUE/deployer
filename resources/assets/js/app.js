import React from 'react';
import { render } from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import { Provider } from 'react-redux';
import { Router, Route, IndexRoute, hashHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';

// Routes
import { indexRoute, routes } from './routes';

// Containers
import App from './Containers/App';
import Tools from './Containers/DevTools';

import configureStore from './store';

// Setup the app
const store = configureStore({
  app: {
    user: LOGGED_IN_USER, // FIXME:Current comes from the HTML, not sure if this is right
  },
});

const history = syncHistoryWithStore(hashHistory, store);

injectTapEventPlugin();

Lang.setLocale('en');

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
