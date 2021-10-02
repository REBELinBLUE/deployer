import { setBusy, clearBusy, showErrors, clearDialog } from '../utils/dialog';

// FIXME: getInput seems a horrible way to do it
export default (Collection, element, getInput) => {
  const Model = Collection.model;

  return (event) => {
    const target = event.currentTarget;

    setBusy(target);

    const instanceId = $(`#${element}_id`).val();

    let instance;
    if (instanceId) {
      instance = Collection.get(instanceId);
    } else {
      instance = new Model();
    }

    instance.save(getInput(), {
      wait: true,
      success: (model, response) => {
        clearDialog(target);
        clearBusy(target, 'save');

        if (!instanceId) {
          Collection.add(response);
        }
      },
      error: (model, response) => {
        showErrors(target, response.responseJSON);
        clearBusy(target, 'save');
      },
    });
  };
};
