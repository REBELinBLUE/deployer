import $ from 'jquery';
import 'select2';
import 'cropper';

import routes from '../routes';
import Uploader from '../handlers/uploader';

const cropperData = {};

function changeSkin(event) {
  const skin = $(event.currentTarget).find(':selected').val();

  $('body').removeClass();
  $('body').addClass(`skin-${skin}`);
}

function twoFactorAuth(event) {
  const container = $('.auth-code');

  if ($(event.currentTarget).is(':checked')) {
    container.removeClass('hide');
  } else {
    container.addClass('hide');
  }
}

function changeEmail(event) {
  const box = $(event.currentTarget).parents('.box');

  box.children('.overlay').removeClass('hide');

  $.post(routes.profileEmail).done((result) => {
    if (result === 'success') {
      box.children('.overlay').addClass('hide');
      box.find('.help-block').removeClass('hide');
    }
  });
}

function saveAvatar() {
  $('#upload-overlay').removeClass('hide');
  $('.avatar-message .alert').addClass('hide');

  $.post(routes.profileImage, cropperData).success((response) => {
    $('#upload-overlay').addClass('hide');

    if (response.image) {
      $('.avatar-message .alert.alert-success').removeClass('hide');
      $('#use-gravatar').removeClass('hide');
    } else {
      $('.avatar-message .alert.alert-danger').removeClass('hide');
    }
  });
}

function useGravatar() {
  $('#upload-overlay').removeClass('hide');
  $('.avatar-message .alert').addClass('hide');

  $.post(routes.profileGravatar).success((response) => {
    $('#upload-overlay').addClass('hide');

    // if (resp.image) {
    $('.avatar-message .alert.alert-success').removeClass('hide');
    $('.avatar-preview').addClass('hide');
    $('.current-avatar-preview').removeClass('hide');
    $('.current-avatar-preview').attr('src', response.image);
    $('#use-gravatar').addClass('hide');
    $('#avatar-save-buttons button').addClass('hide');
    // } else {
    //     $('.avatar-preview').addClass('hide');
    //     $('#use-gravatar').removeClass('hide');
    // }
  });
}

function cropImage(data) {
  cropperData.dataX = Math.round(data.x);
  cropperData.dataY = Math.round(data.y);
  cropperData.dataHeight = Math.round(data.height);
  cropperData.dataWidth = Math.round(data.width);
  cropperData.dataRotate = Math.round(data.rotate);
}

function uploadError(file) {
  if (file.responseJSON.file) {
    alert(file.responseJSON.file.join(''));
  } else if (file.responseJSON.error) {
    alert(file.responseJSON.error.message);
  }

  $('#upload-overlay').addClass('hide');
}

function uploadSuccess(response) {
  if (response.message === 'success') {
    $('.avatar>img').cropper('replace', response.image);
    cropperData.path = response.path;

    $('.current-avatar-preview').addClass('hide');
    $('.avatar-preview').removeClass('hide');
    $('#save-avatar').removeClass('hide');
  }
}

// FIXME: Convert to class
export default () => {
  const selectOptions = {
    minimumResultsForSearch: Infinity,
  };

  $('#skin').select2(selectOptions);
  $('#scheme').select2(selectOptions);
  $('#language').select2(selectOptions);

  $('#skin').on('change', changeSkin);
  $('#two-factor-auth').on('change', twoFactorAuth);
  $('#request-change-email').on('click', changeEmail);
  $('#save-avatar').on('click', saveAvatar);
  $('#use-gravatar').on('click', useGravatar);

  $('.avatar>img').cropper({
    aspectRatio: 1 / 1,
    preview: '.avatar-preview',
    crop: cropImage,
    ready: () => {
      $('#upload-overlay').addClass('hide');
    },
  });

  const uploader = new Uploader({
    trigger: '#upload',
    name: 'file',
    action: routes.profileUpload,
    accept: 'image/*',
    data: {
      _token: $('meta[name="csrf-token"]').attr('content'),
    },
    multiple: false,
    error: uploadError,
    success: uploadSuccess,
    change: () => {
      $('#upload-overlay').removeClass('hide');

      uploader.submit();
    },
  });
};
