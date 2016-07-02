// This is a simple wrapper file to stop PHPStorm complaining
// about the class being missing, it comes from js-localization

const Lang = {
  addMessages: (_messages) => {},
  locale: () => {},
  setLocale: (localeId) => {},
  has: (messageKey) => {},
  get: (messageKey, replacements, forceLocale) => {},
  choice: (messageKey, count, replacements) => {}
};

export default Lang;
