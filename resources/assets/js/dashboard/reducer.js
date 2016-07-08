import Immutable from 'immutable';

const initialState = Immutable.fromJS({
  timeline: {},
});

export default function (state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
