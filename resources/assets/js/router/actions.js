const selectLocationState = {
  selectLocationState(state) {
    return state.get('routing').toObject();
  },
};

export default selectLocationState;
