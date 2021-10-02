import localize from '../utils/localization';

export default (translationKey, element) =>
  (event) => {
    let title = localize.get(`${translationKey}.create`);
    const button = $(event.relatedTarget);
    const dialog = $(event.target);

    $('.btn-danger', dialog).hide();
    $('.callout', dialog).hide();
    $('.existing-only', dialog).hide();
    $('.new-only', dialog).hide();
    $('.has-error', dialog).removeClass('has-error');
    $('.label-danger', dialog).remove();

    if (button.hasClass('btn-edit')) {
      title = localize.get(`${translationKey}.edit`);
      $('.btn-danger', dialog).show();
      $('.existing-only', dialog).show();
    } else {
      const form = dialog.find('form')[0];
      form.reset();

      $(`#${element}_id`).val('');
      $('.new-only', dialog).show();
    }

    dialog.find('.modal-title span').text(title);
  };
