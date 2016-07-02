const initialState = {
  running: [{}],
  pending: [{}, {}],
};

export default function (state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
