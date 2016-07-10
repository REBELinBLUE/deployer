import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  project: null,
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.SET_ACTIVE_PROJECT:
      return state.set('project', action.project);
    default:
      return state;
  }
}
