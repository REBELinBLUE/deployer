import 'isomorphic-fetch';

import * as actions from './actionTypes';
import * as constants from './constants';

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

function getResource(dialog) {
  switch (dialog) {
    case constants.SERVER_DIALOG:
      return 'servers';
    default:
      throw new Error(`Unknown resource ${dialog}`);
  }
}

export function saveObject(dialog, data, dispatch) {
  const form = data;
  const token = form.token;
  const project = form.project_id;

  let uri = `/app/projects/${project}/${getResource(dialog)}`;
  let method = 'POST';
  if (data.id) {
    uri = `${uri}/${data.id}`;
    method = 'PUT';
  }

  delete form.token;

  // https://github.com/erikras/redux-form/issues/119
  // http://redux-form.com/5.3.3/#/examples/submit-validation?_k=dpefhq
  return new Promise((resolve, reject) => {
    fetch(uri, {
      method,
      credentials: 'include',
      body: JSON.stringify(form),
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
      },
    })
      .then(response => {
        const json = response.json();
        if (!response.ok) {
          reject(json);
          return false;
        }

        console.log(json); // FIXME: Update object in store
        dispatch(hideDialog());

        resolve(json);
        return true;
      })
      .catch(error => console.log(error));
  });

}
