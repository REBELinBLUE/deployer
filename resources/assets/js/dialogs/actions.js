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

function getResourcePath(dialog) {
  switch (dialog) {
    case constants.SERVER_DIALOG:
      return 'servers';
    default:
      throw new Error(`Unknown resource ${dialog}`);
  }
}

function makeSaveRequest(uri, method, data, token, dispatch) {
  return new Promise((resolve, reject) => {
    fetch(uri, {
      method,
      credentials: 'include',
      body: JSON.stringify(data),
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
      },
    })
      .then(response => {
        if (response.ok) {
          // FIXME: Update object in store - https://facebook.github.io/react/docs/update.html
          dispatch(hideDialog());

          return resolve(response.json());
        }

        return response.json();
      })
      .then(json => {
        const errors = {};
        Object.keys(json).map(key => (errors[key] = json[key][0]));

        return reject(errors);
      })
      .catch(error => console.log(error));
  });
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

export function saveObject(dialog, data, dispatch) {
  const form = data;
  const project = form.project_id;
  const token = form.token;

  let uri = `/app/projects/${project}/${getResourcePath(dialog)}`;
  let method = 'POST';
  if (data.id) {
    uri = `${uri}/${data.id}`;
    method = 'PUT';
  }

  delete form.token;

  return makeSaveRequest(url, method, data, token, dispath);
}
