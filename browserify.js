/**
 * Note: When requiring modules for `browserify`,
 * the `require` declaration **cannot** be used in a loop, 
 * and *must* be fully declared for each module / sub-module.
 */

/** @var Object.<string, string> The *Browserify Modules* to be loaded from the `node_modules` folder. */
const BROWSERIFY_MODULES = {
  moment: require('moment'),
  dayjs: (function () {
    let dayjs = require('dayjs');

    const pluginList = [
      require(`dayjs/plugin/advancedFormat`),
      require(`dayjs/plugin/calendar`),
      require(`dayjs/plugin/customParseFormat`),
      require(`dayjs/plugin/duration`),
      require(`dayjs/plugin/isBetween`),
      require(`dayjs/plugin/isSameOrAfter`),
      require(`dayjs/plugin/isSameOrBefore`),
      require(`dayjs/plugin/isToday`),
      require(`dayjs/plugin/isTomorrow`),
      require(`dayjs/plugin/isYesterday`),
      require(`dayjs/plugin/localeData`),
      require(`dayjs/plugin/localizedFormat`),
      require(`dayjs/plugin/minMax`),
      require(`dayjs/plugin/objectSupport`),
      require(`dayjs/plugin/pluralGetSet`),
      require(`dayjs/plugin/relativeTime`),
      require(`dayjs/plugin/timezone`),
      require(`dayjs/plugin/toObject`),
      require(`dayjs/plugin/utc`),
      require(`dayjs/plugin/weekday`)
    ];

    for (let plugin of pluginList) {
      dayjs.extend(plugin);
    }

    return dayjs;
  })()
};

module.exports = BROWSERIFY_MODULES;
