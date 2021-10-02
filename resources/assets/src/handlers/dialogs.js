import $ from 'jquery';

import showDialog from './showDialog';
import deleteModel from './deleteModel';
import saveModel from './saveModel';

export default (element, translationKey, getInput, Collection) => {
  const modal = `div#${element}.modal`;

  $(modal).on('show.bs.modal', showDialog(translationKey, element));
  $(`${modal} button.btn-delete`).on('click', deleteModel(Collection, element));
  $(`${modal} button.btn-save`).on('click', saveModel(Collection, element, getInput));
};
