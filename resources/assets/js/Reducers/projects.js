import {
  RECEIVED_RUNNING_PROJECTS_ACTION,
} from '../constants/actionTypes';

const initialState = {
  running: [],
  pending: [],
};

export default function (state = initialState, action) {
  switch (action.type) {
    case RECEIVED_RUNNING_PROJECTS_ACTION:
      return {
        ...state,
        running: action.running,
        pending: action.pending,
      };
    default:
      return state;
  }
}
