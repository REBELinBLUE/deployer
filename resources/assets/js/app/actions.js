import * as actions from './actionTypes';

export function setPageTitle(title, subtitle) {
  return {
    type: actions.SET_PAGE_TITLE,
    title,
    subtitle: subtitle || null,
  };
}

export function setSubTitle(subtitle) {
  return {
    type: actions.SET_PAGE_SUBTITLE,
    subtitle,
  };
}
