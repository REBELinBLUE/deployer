import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  locale: 'en',
  outdated: false,
  version: null,
  latest: null,
  title: '',
  subtitle: null,
  user: false,
  token: '',
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.SET_PAGE_TITLE:
      return state.merge({
        title: action.title,
        subtitle: action.subtitle,
      });
    case actions.SET_PAGE_SUBTITLE:
      return state.set('subtitle', action.subtitle);
    default:
      return state;
  }
}
