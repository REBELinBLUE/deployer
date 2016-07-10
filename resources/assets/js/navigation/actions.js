import * as actions from './actionTypes';

export function setButtons(buttons = []) {
  return {
    type: actions.SET_BUTTONS,
    buttons,
  };
}
