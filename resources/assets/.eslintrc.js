module.exports = {
  extends: 'airbnb-base',
  parserOptions: {
    ecmaVersion: 2017
  },
  rules: {
    'max-len': ['error', 120],
    'no-underscore-dangle': [2, {
      allow: ['__backboneAgent']
    }],
  },
  env: {
    browser: true,
    jquery: true,
    node: true,
    mocha: true
  }
};
