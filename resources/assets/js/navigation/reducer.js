import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  running: [],
  pending: [],
  projects: [],
  groups: [],
  buttons: [],
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.SET_BUTTONS:
      return state.set('buttons', Immutable.fromJS(action.buttons));
    default:
      return state;
  }
}
