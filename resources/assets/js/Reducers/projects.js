const project = {
  id: 1,
  name: 'My project',
  branch: 'Master',
  started_at: '10pm'
};

const initialState = {
  running: [project, project],
  pending: [project, project, project, project, project],
};

export default function (state = initialState, action) {
  switch (action.type) {
    default:
      return state;
  }
}
