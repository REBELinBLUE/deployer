const messages = {};
let locale;

function applyReplacements(message, replacements) {
  let result = message;

  if (replacements && Object.keys(replacements).length > 0) {
    Object.entries(replacements).forEach(([token, replacement]) => {
      result = result.replace(new RegExp(`:${token}`, 'g'), replacement);
    });
  }

  return result;
}

// FIXME: This needs to be export for webpack but it shouldn't need to be
export function addMessages(newMessages) {
  Object.entries(newMessages).forEach(([key, value]) => {
    messages[key] = {
      ...messages[key],
      ...value,
    };
  });
}

function has(messageKey) {
  return typeof messages[locale][messageKey] !== 'undefined';
}

function setLocale(localeId) {
  if (!messages[localeId]) {
    throw new Error(`No messages defined for locale: "${localeId}".`);
  }

  locale = localeId;
}

function getLocale() {
  return locale;
}

export function trans(messageKey, replacements) {
  if (typeof messages[locale][messageKey] === 'undefined') {
    const result = {};
    Object.keys(messages[locale]).forEach((prop) => {
      const prefix = `${messageKey}.`;

      if (prop.indexOf(prefix) > -1) {
        result[prop.replace(prefix, '')] = messages[locale][prop];
      }
    });

    if (Object.keys(result).length > 0) {
      return result;
    }

    return messageKey;
  }

  return applyReplacements(messages[locale][messageKey], replacements);
}

export function transChoice(messageKey, count, replacements) {
  if (typeof messages[locale][messageKey] === 'undefined') {
    return messageKey;
  }

  const parts = messages[locale][messageKey].split('|');

  if (count === 1) {
    return applyReplacements(parts[0], replacements);
  }

  return applyReplacements(parts[1], replacements);
}

export default {
  get: trans,
  choice: transChoice,
  addMessages,
  has,
  setLocale,
  getLocale,
};

