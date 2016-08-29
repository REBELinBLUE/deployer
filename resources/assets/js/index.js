/* eslint-disable no-underscore-dangle */
import 'babel-polyfill';
import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { Router, browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';

import attachStoreToRoutes from './router/routes';
import selectLocationState from './router/actions';
import configureStore from './store';
import preloadState from './store/preloader';

const initialState = preloadState(window.__PRELOADED_STATE__);
const store = configureStore(initialState);
const routes = attachStoreToRoutes(store);
const history = syncHistoryWithStore(browserHistory, store, selectLocationState);

render(
  <Provider store={store}>
    <Router history={history} routes={routes} />
  </Provider>, document.getElementById('content'));
