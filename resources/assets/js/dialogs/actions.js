import * as actions from './actionTypes';

export function showDialog(dialog) {
  return {
    type: actions.SHOW_DIALOG,
    dialog,
  };
}

export function hideDialog() {
  return {
    type: actions.HIDE_DIALOG,
  };
}

export function addObject(dialog) {
  return (dispatch) => {
    // dispatch({
    //   type: actions.ADD_OBJECT,
    // });

    dispatch(showDialog(dialog));
  };
}

export function editObject(dialog, instance) {
  return (dispatch) => {
    dispatch({
      type: actions.EDIT_OBJECT,
      instance,
    });

    dispatch(showDialog(dialog));
  };
}

// FIXME: Actually do something
export function saveObject(data, dispatch) {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      dispatch(hideDialog());
      resolve();
    }, 10000);
  });
}
