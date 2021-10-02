import $ from 'jquery';
import toastr from 'toastr';
import Backbone from 'backbone';
import io from 'socket.io-client';
import 'bootstrap';
import 'admin-lte';
import 'select2';

import localize from './utils/localization';
import './messages';
import Navigation from './views/Navigation';

// Needed for Backbone debugger
if (typeof window.__backboneAgent === 'undefined') {
  window.__backboneAgent = { handleBackbone: () => {} };
}

// Backbone debugger and Socket.io debugger require these
window.io = io;
window.__backboneAgent.handleBackbone(Backbone);

// FIXME: test this actually works
toastr.options = {
  ...toastr.options,
  closeButton: true,
  progressBar: true,
  preventDuplicates: true,
  closeMethod: 'fadeOut',
  closeDuration: 300,
  closeEasing: 'swing',
  positionClass: 'toast-bottom-right',
  timeOut: 5000,
  extendedTimeOut: 7000,
};

const locale = $('meta[name="locale"]').attr('content');
localize.setLocale(locale || 'en');

const token = $('meta[name="csrf-token"]').attr('content');
$.ajaxPrefilter((options, originalOptions, jqXHR) => jqXHR.setRequestHeader('X-CSRF-Token', token));

Navigation();
