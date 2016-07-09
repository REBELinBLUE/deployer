import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { Router, browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import 'babel-polyfill';

import * as app from './app/constants';
import * as dashboard from './dashboard/constants';
import * as navigation from './navigation/constants';
import * as socket from './socket/constants';

import attachStoreToRoutes from './router/routes';
import { selectLocationState } from './router/reducer';
import configureStore from './store';

const PRELOADED = window.__PRELOADED_STATE__; // eslint-disable-line no-underscore-dangle

Lang.setLocale(PRELOADED[app.NAME].locale);

const store = configureStore({
  [app.NAME]: PRELOADED[app.NAME],
  [dashboard.NAME]: PRELOADED[dashboard.NAME],
  [navigation.NAME]: PRELOADED[navigation.NAME],
  [socket.NAME]: PRELOADED[socket.NAME],
});

const routes = attachStoreToRoutes(store);
const history = syncHistoryWithStore(browserHistory, store, selectLocationState);

render(
  <Provider store={store}>
    <Router history={history} routes={routes} />
  </Provider>, document.getElementById('content'));
