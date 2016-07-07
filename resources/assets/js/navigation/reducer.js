import Immutable from 'immutable';

import {
RECEIVED_RUNNING_PROJECTS_ACTION,
LOADING_GROUPED_PROJECT_LIST_ACTION,
RECEIVED_GROUPED_PROJECT_LIST_ACTION,
} from './actionTypes';

const initialState = Immutable.fromJS({
  running: [],
  pending: [],
  projects: {
    fetching: false,
    data: [],
  },
});

export default function (state = initialState, action) {
  switch (action.type) {

    case LOADING_GROUPED_PROJECT_LIST_ACTION:
      return state.merge({
        projects: {
          data: [],
          fetching: true,
        },
      });
    case RECEIVED_GROUPED_PROJECT_LIST_ACTION:
      return state.merge({
        projects: {
          data: action.projects,
          fetching: false,
        },
      });
    case RECEIVED_RUNNING_PROJECTS_ACTION:
      return state.merge({
        running: action.running,
        pending: action.pending,
      });
    default:
      return state;
  }
}
