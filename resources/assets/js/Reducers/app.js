import { LOGGED_IN_ACTION } from '../constants/actionTypes';

const initialState = {
  user: null,
};

export default function (state = initialState, action) {
  switch (action.type) {
    case LOGGED_IN_ACTION:
      return {
        user: action.user,
      };
    default:
      return state;
  }
}
