import Immutable from 'immutable';

const initialState = Immutable.fromJS({
  running: [],
  pending: [],
  projects: [], // FIXME: Move this to a projects element? add a group element
});

export default function (state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
