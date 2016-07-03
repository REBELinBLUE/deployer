import {
  LOADING_DASHBOARD_PROJECT_LIST_ACTION,
  POPULATE_DASHBOARD_PROJECT_LIST_ACTION,
} from '../constants/actionTypes';

const initialState = {
  projects: {
    fetching: false,
    data: [],
  },
  timeline: {
    fetching: false,
    data: []
  },
};

export default function (state = initialState, action) {
  switch (action.type) {
    case POPULATE_DASHBOARD_PROJECT_LIST_ACTION:
      return {
        ...state,
        projects: {
          data: action.projects,
          fetching: false,
        },
      };
    case LOADING_DASHBOARD_PROJECT_LIST_ACTION:
      return {
        ...state,
        projects: {
          data: [],
          fetching: true,
        },
      };
    default:
      return state;
  }
}
