import Immutable from 'immutable';

const initialState = Immutable.fromJS({
  project: null,
});

export default function (state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
