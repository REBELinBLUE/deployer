import { LOGGED_IN_ACTION } from '../constants/actionTypes';

export function userLoggedIn() {
  return {
    type: LOGGED_IN_ACTION,
    user: LOGGED_IN_USER,
  };
}

