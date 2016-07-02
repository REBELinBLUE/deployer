import { createStore, compose, applyMiddleware } from 'redux';
import { routerMiddleware } from 'react-router-redux';
import { hashHistory } from 'react-router';

import rootReducers from '../rootReducer';

const router = routerMiddleware(hashHistory);

const enhancer = compose(
  // Apply middleware
  applyMiddleware(router)
);

export default (initialState) => createStore(rootReducers, initialState, enhancer);
