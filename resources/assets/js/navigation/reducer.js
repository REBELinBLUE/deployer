import Immutable from 'immutable';

import * as actions from './actionTypes';

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
    case actions.LOADING_GROUPED_PROJECT_LIST:
      return state.merge({
        projects: {
          data: [],
          fetching: true,
        },
      });
    case actions.RECEIVED_GROUPED_PROJECT_LIST:
      return state.merge({
        projects: {
          data: action.projects,
          fetching: false,
        },
      });
    case actions.RECEIVED_RUNNING_PROJECTS:
      return state.merge({
        running: action.running,
        pending: action.pending,
      });
    default:
      return state;
  }
}
