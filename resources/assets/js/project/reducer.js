import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  active: null,
  showKey: false,
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.HIDE_SSH_KEY:
      return state.set('showKey', false);
    case actions.SHOW_SSH_KEY:
      return state.set('showKey', true);
    case actions.SET_ACTIVE_PROJECT:
      return state.set('active', action.project);
    default:
      return state;
  }
}
