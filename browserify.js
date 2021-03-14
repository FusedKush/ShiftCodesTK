/**
 * Note: When requiring modules for `browserify`,
 * the `require` declaration **cannot** be used in a loop, 
 * and *must* be fully declared for each module / sub-module.
 */

/** @var {Object} node_modules The *Browserify Modules* loaded from the `node_modules` folder. */
const node_modules = {
  dayjs: (function () {
    let dayjs = require('dayjs');

    /** The `dayjs` plugins to be loaded */
    const pluginList = [
      require('dayjs/plugin/advancedFormat'),
      require('dayjs/plugin/calendar'),
      require('dayjs/plugin/customParseFormat'),
      require('dayjs/plugin/duration'),
      require('dayjs/plugin/isBetween'),
      require('dayjs/plugin/isSameOrAfter'),
      require('dayjs/plugin/isSameOrBefore'),
      require('dayjs/plugin/isToday'),
      require('dayjs/plugin/isTomorrow'),
      require('dayjs/plugin/isYesterday'),
      require('dayjs/plugin/localeData'),
      require('dayjs/plugin/localizedFormat'),
      require('dayjs/plugin/minMax'),
      require('dayjs/plugin/objectSupport'),
      require('dayjs/plugin/pluralGetSet'),
      require('dayjs/plugin/relativeTime'),
      require('dayjs/plugin/timezone'),
      require('dayjs/plugin/toObject'),
      require('dayjs/plugin/updateLocale'),
      require('dayjs/plugin/utc'),
      require('dayjs/plugin/weekday')
    ];
    /** Custom `dayjs` locale data. */
    const customLocales = {
      /** English */
      en: {
        calendar: {
          /** Calendar configurations without *Timezone* support */
          standard: {
            default: {
              lastDay: '[Yesterday at] LT',
              sameDay: '[Today at] LT',
              nextDay: '[Tomorrow at] LT',
              lastWeek: '[Last] dddd [at] LT',
              nextWeek: 'dddd [at] LT',
              sameElse: 'LLLL'
            },
            short: {
              lastDay: '[Yesterday at] LT',
              sameDay: '[Today at] LT',
              nextDay: '[Tomorrow at] LT',
              lastWeek: '[Last] ddd [at] LT',
              nextWeek: 'ddd [at] LT',
              sameElse: 'llll'
            },
            expanded: {
              lastDay: '[Yesterday at] LTS',
              sameDay: '[Today at] LTS',
              nextDay: '[Tomorrow at] LTS',
              lastWeek: '[Last] dddd [at] LTS',
              nextWeek: 'dddd [at] LTS',
              sameElse: 'dddd, MMMM D, YYYY h:mm:ss A'
            }
          },
          /** Calendar configurations with *Timezone* support */
          timezone: {
            default: {
              lastDay: '[Yesterday at] LT z',
              sameDay: '[Today at] LT z',
              nextDay: '[Tomorrow at] LT z',
              lastWeek: '[Last] dddd [at] LT z',
              nextWeek: 'dddd [at] LT z',
              sameElse: 'LLLL z'
            },
            short: {
              lastDay: '[Yesterday at] LT z',
              sameDay: '[Today at] LT z',
              nextDay: '[Tomorrow at] LT z',
              lastWeek: '[Last] ddd [at] LT z',
              nextWeek: 'ddd [at] LT z',
              sameElse: 'llll z'
            },
            expanded: {
              lastDay: '[Yesterday at] LTS zzz',
              sameDay: '[Today at] LTS zzz',
              nextDay: '[Tomorrow at] LTS zzz',
              lastWeek: '[Last] dddd [at] LTS zzz',
              nextWeek: 'dddd [at] LTS zzz',
              sameElse: 'dddd, MMMM D, YYYY h:mm:ss A zzz'
            }
          }
        },
        relativeTime: {
          future: "In %s",
          past: "%s ago",
          s: 'A Few Seconds',
          m: "A Minute",
          mm: "%d Minutes",
          h: "An Hour",
          hh: "%d Hours",
          d: "A Day",
          dd: "%d Days",
          M: "A Month",
          MM: "%d Months",
          y: "A Year",
          yy: "%d Years"
        }
      }
    };

    for (let plugin of pluginList) {
      dayjs.extend(plugin);
    }

    // Locale Customization
    dayjs.updateLocale('en', {
      calendar: customLocales.en.calendar.standard.default,
      relativeTime: customLocales.en.relativeTime
    })

    /** Custom Locale Data provided by ShiftCodesTK */
    dayjs.tkLocales = customLocales;

    return dayjs;
  })()
};

module.exports = node_modules;
