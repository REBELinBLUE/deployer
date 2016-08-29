import Immutable from 'immutable';

import * as actions from './actionTypes';

const initialState = Immutable.fromJS({
  visible: false,
  instance: {},
});

export default function (state = initialState, action) {
  switch (action.type) {
    case actions.ADD_OBJECT:
      return state.merge({
        instance: {},
      });
    case actions.EDIT_OBJECT:
      return state.merge({
        instance: action.instance,
      });
    case actions.HIDE_DIALOG:
      return state.merge({
        visible: false,
        instance: {},
      });
    case actions.SHOW_DIALOG:
      return state.set('visible', action.dialog);
    default:
      return state;
  }
}
