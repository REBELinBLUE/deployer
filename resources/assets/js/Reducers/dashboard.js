import Immutable from 'immutable';

const initialState = Immutable.fromJS({
  timeline: {
    fetching: false,
    data: [],
  },
});

export default function (state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
