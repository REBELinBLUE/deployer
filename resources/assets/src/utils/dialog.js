import $ from 'jquery';

function getIcon(target) {
  return target.find('i');
}

function getDialog(target) {
  return target.parents('.modal');
}

export function setBusy(target) {
  const icon = getIcon($(target));
  const dialog = getDialog($(target));

  icon.addClass('fa-refresh fa-spin').removeClass('fa-save').removeClass('fa-trash');
  dialog.find('input').attr('disabled', 'disabled');
  $('button.close', dialog).hide();
}

export function clearBusy(target, iconClass) {
  const icon = getIcon($(target));
  const dialog = getDialog($(target));

  icon.removeClass('fa-refresh fa-spin').addClass(`fa-${iconClass}`);
  $('button.close', dialog).show();
  dialog.find('input').removeAttr('disabled');
}

export function clearDialog(target) {
  const dialog = getDialog($(target));

  dialog.modal('hide');
  $('.callout-danger', dialog).hide();
}

export function showErrors(target, errors) {
  const dialog = getDialog($(target));

  $('.callout-danger', dialog).show();

  $('.has-error', dialog).removeClass('has-error');
  $('.label-danger', dialog).remove();

  $('form input', dialog).each((index, element) => {
    const field = $(element);
    const name = field.attr('name');

    if (typeof errors[name] !== 'undefined') {
      const parent = field.parents('div.form-group');
      parent.addClass('has-error');

      $.each(errors[name], (idx) => {
        parent.append($('<span>').attr('class', 'label label-danger').text(errors[name][idx]));
      });
    }
  });
}
