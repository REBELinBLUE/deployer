import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  loaded: false,
  locale: 'en',
  outdated: false,
  version: null,
  latest: null,
  title: '',
  subtitle: null,
  user: null,
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.SET_PAGE_TITLE:
      return state.merge({
        title: action.title,
        subtitle: action.subtitle,
      });
    case actions.SET_PAGE_SUBTITLE:
      return state.merge({
        subtitle: action.subtitle,
      });
    default:
      return state;
  }
}
