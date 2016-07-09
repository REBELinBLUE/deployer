import Immutable from 'immutable';

const initialState = Immutable.fromJS({
  running: [],
  pending: [],
  projects: [],
  groups: [],
});

export default function (state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
