import $ from 'jquery';
import { clearDialog, setBusy, clearBusy } from '../utils/dialog';

export default (Collection, element) =>
  (event) => {
    const target = event.currentTarget;

    setBusy(target);

    const instanceId = $(`#${element}_id`).val();
    const instance = Collection.get(instanceId);

    instance.destroy({
      wait: true,
      success: () => {
        clearDialog(target);
        clearBusy(target, 'trash');
      },
      error: () => {
        clearBusy(target, 'trash');
      },
    });
  };
