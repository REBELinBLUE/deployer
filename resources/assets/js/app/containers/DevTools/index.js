/* eslint-disable global-require */
if (process.env.NODE_ENV === 'production') {
  module.exports = require('./DevTools.prod.jsx');
} else {
  module.exports = require('./DevTools.dev.jsx');
}
