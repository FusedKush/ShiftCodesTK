/** @var Object.<string, string> The *Browserify Modules* to be loaded from the `node_modules` folder. */
const BROWSERIFY_MODULES = {
  moment: require('moment'),
  date_fns: require('date-fns')
};

module.exports = BROWSERIFY_MODULES;
