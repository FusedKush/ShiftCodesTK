function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

//*** Load State ***//
var globalFunctionsReady = true;
var pbIntervals = {}; // Error Handling

function thrownTryError(error, behavior) {
  if (behavior == 'silent') {
    console.error(error);
    return false;
  } else if (behavior == 'throw') {
    throw error;
  } else if (behavior == 'ignore') {
    return false;
  } else {
    error.message = "".concat(error.message, "\n\r\n\rAdditionally, the behavior parameter is invalid.\n\rBehavior: ").concat(behavior);
    throw error;
  }
}

function tryParseInt() {
  var _int = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

  var behavior = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'silent';
  var error = new Error();

  (function () {
    error.name = 'parseInt Error';
    error.message = "Not a valid number.\n\rInt: ".concat(_int);
  })();

  var result = parseInt(_int);

  if (!isNaN(result)) {
    return result;
  } else {
    return thrownTryError(error, behavior);
  }
}

function tryJSONParse() {
  var string = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var behavior = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'silent';
  var error = new Error();

  (function () {
    error.name = 'JSONParse Error';
    error.message = "Not a valid JSON string.\n\rString: ".concat(string);
  })();

  try {
    return JSON.parse(string);
  } catch (e) {
    return thrownTryError(error, behavior);
  }
}

function tryToRun(settings) {
  var currentAttempt = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;

  var sets = function () {
    if (currentAttempt) {
      var defaultSettings = {
        "function": function _function() {
          return true;
        },
        attempts: false,
        delay: 250,
        behavior: 'silent',
        logCatch: false,
        customError: false
      };
      return mergeObj(defaultSettings, settings);
    } else {
      return settings;
    }
  }();

  function failed() {
    if (currentAttempt <= sets.attempts || !sets.attempts) {
      setTimeout(function () {
        tryToRun(settings, currentAttempt + 1);
      }, sets.delay);
    } else {
      var error = new Error();
      error.name = 'tryToRun Error';

      if (sets.customError !== false) {
        error.message = sets.customError;
      } else {
        error.message = "Max Tries Exceeded.\r\n\r\nSettings: ".concat(JSON.stringify(sets));
      }

      if (sets.logCatch) {
        error.message += "\r\n\r\nCaught Error: ".concat(e);
      }

      if (sets.behavior == 'throw') {
        throw error;
      } else if (sets.behavior == 'silent') {
        console.error(error);
        return false;
      } else if (sets.behavior == 'ignore') {
        return false;
      }
    }
  }

  try {
    var result = sets["function"]();

    if (!result) {
      failed();
    }
  } catch (e) {
    failed();
  }
}

; // Toggle element states

function disenable(element, state, optTx) {
  var tabIndexes = {
    "true": '-1',
    "false": '0'
  };
  element.disabled = state;
  element.setAttribute('aria-disabled', state);

  if (state === true) {
    element.setAttribute('disabled', '');
  } else {
    element.removeAttribute('disabled');
  }

  if (optTx === true) {
    element.tabIndex = tabIndexes[state];
  }
}

function vishidden(element, state, optTx) {
  var tabIndexes = {
    "true": '-1',
    "false": '0'
  };
  element.hidden = state;
  element.setAttribute('aria-hidden', state);

  if (state === true) {
    element.setAttribute('hidden', '');
  } else {
    element.removeAttribute('hidden');
  }

  if (optTx === true) {
    element.tabIndex = tabIndexes[state];
  }
} // Update ELement Labels


function updateLabel(element, label) {
  element.title = label;
  element.setAttribute('aria-label', label);
} // Handles AJAX Requests


function newAjaxRequest(properties) {
  var request = function () {
    if (window.XMLHttpRequest) {
      return new XMLHttpRequest();
    } else if (window.ActiveXObject) {
      return new ActiveXObject('Microsoft.XMLHttp');
    }
  }();

  var defaultProps = {
    type: 'GET',
    file: null,
    callback: function callback(response) {
      return response;
    },
    params: 'none',
    requestHeader: 'default',
    catchErrors: true
  };
  var props = mergeObj(defaultProps, properties);

  function ajaxError(e) {
    var error = new Error();
    error.name = 'newAjaxRequest Error';
    error.message = "An error occurred with Ajax Request \"".concat(props.type, ": ").concat(props.file, "\".\n\rError: ").concat(e);
    throw error;
  }

  if (props.file !== null) {
    var file = function () {
      var regex = new RegExp('\\s+', 'g');
      return props.file.replace(regex, '');
    }(); // Handle Response


    request.onreadystatechange = function () {
      function handleResponse() {
        if (request.readyState === XMLHttpRequest.DONE) {
          if (request.status === 200) {
            props.callback(request.responseText);
          } else if (props.catchErrors) {
            throw "Status Code ".concat(request.status, " returned.");
          }
        }
      }

      if (props.catchErrors) {
        try {
          handleResponse();
        } catch (e) {
          ajaxError(e);
        }
      } else {
        handleResponse();
      }
    };

    request.open(props.type, file, true);

    if (props.requestHeader == 'form') {
      request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    if (props.params == 'none') {
      request.send();
    } else {
      request.send(props.params);
    }
  } else {
    ajaxError("File path was not specified.\n\rProperties: ".concat(JSON.stringify(props)));
  }
} // Handles DateTime Requests


function datetime() {
  var format = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'y-m-d';
  var useDate = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var utc = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'check';

  var base = function () {
    if (!useDate) {
      return new Date();
    } else {
      var d = new Date(useDate);

      if (d != 'Invalid Date') {
        return d;
      } else {
        return false;
      }
    }
  }();

  if (base !== false) {
    var addLeading = function addLeading(val) {
      return "0".concat(val).slice(-2);
    };

    var replaceDate = function replaceDate(match) {
      var str = date[match];

      if (str) {
        return str;
      } else {
        return match;
      }
    };

    var vals = function () {
      if (utc == 'check') {
        // Not Now || Time is Specified                   && Time is not default
        if (!useDate || useDate.search('(\\d{2}\\:)') != -1 && useDate.search('00:00:00') == -1) {
          utc = false;
        } else {
          utc = true;
        }
      }

      if (utc) {
        return {
          year: base.getUTCFullYear(),
          month: base.getUTCMonth(),
          date: base.getUTCDate(),
          day: base.getUTCDay(),
          hour: base.getUTCHours(),
          minute: base.getUTCMinutes(),
          seconds: base.getUTCSeconds()
        };
      } else {
        return {
          year: base.getFullYear(),
          month: base.getMonth(),
          date: base.getDate(),
          day: base.getDay(),
          hour: base.getHours(),
          minute: base.getMinutes(),
          seconds: base.getSeconds()
        };
      }
    }();

    var templates = {
      'tmp-full': 'monthN date, year hour12:minute ampm',
      'tmp-date': 'month/date/year',
      'tmp-time12': 'hour12:minute ampm',
      'tmp-time24': 'hour24:minute'
    };
    var def = {
      // String definitions
      days: {
        0: 'Sunday',
        1: 'Monday',
        2: 'Tuesday',
        3: 'Wednesday',
        4: 'Thursday',
        5: 'Friday',
        6: 'Saturday'
      },
      months: {
        0: 'January',
        1: 'Feburary',
        2: 'March',
        3: 'April',
        4: 'May',
        5: 'June',
        6: 'July',
        7: 'August',
        8: 'September',
        9: 'October',
        10: 'November',
        11: 'December'
      }
    };
    var date = {
      year: vals.year,
      month: addLeading(vals.month + 1),
      monthN: def.months[vals.month].slice(0, 3),
      monthL: def.months[vals.month],
      date: addLeading(vals.date),
      day: vals.day,
      dayN: def.days[vals.day].slice(0, 3),
      dayL: def.days[vals.day],
      hour12: function () {
        var h = vals.hour;

        if (h > 1 && h <= 12) {
          return h;
        } else if (h > 12) {
          return h - 12;
        } else {
          return 12;
        }
      }(),
      hour24: vals.hour,
      minute: addLeading(vals.minute),
      second: addLeading(vals.second),
      ampm: function () {
        var h = vals.hour;

        if (h <= 12) {
          return 'AM';
        } else {
          return 'PM';
        }
      }(),
      js: base,
      // JS Date Object
      iso: base.toISOString() // ISO 8601 String

    };
    var regex = new RegExp("(\\w+)", 'g');

    if (templates[format]) {
      return datetime(templates[format], useDate);
    } else if (format == 'js') {
      return date.js;
    } else {
      return format.replace(regex, replaceDate);
    }
  } else {
    return false;
  }
}

function dateDif(date) {
  var start = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var dates = {};
  var args = {
    'date': date,
    'start': start
  };
  var names = Object.keys(args);

  for (var i = 0; i < names.length; i++) {
    var name = names[i];
    var arg = args[name];
    var d = datetime('js', arg);
    dates[name] = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate());
  }

  var dif = Math.abs(dates.start - dates.date);
  return Math.ceil(dif / (1000 * 3600 * 24));
}

function dateRel(date) {
  var start = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var dates = {
    date: datetime('tmp-date', date),
    start: datetime('tmp-date', start)
  };
  var dif = dateDif(dates.date, dates.start);

  if (dif == 0) {
    return 'Today';
  } else if (dif == 1) {
    if (dates.start > dates.date) {
      return 'Yesterday';
    } else {
      return 'Tomorrow';
    }
  } else if (dif < 7 && dif > -7) {
    return datetime('dayL', date);
  } else {
    return false;
  }
} // Generates a random number between two values


function randomNum(min, max) {
  return Math.round(Math.random() * (max - min) + min);
} // DOM shorthands


function modifyClass(element, className, modification) {
  if (element !== null && element !== undefined) {
    if (modification == 'contains') {
      return element.classList[modification](className);
    } else {
      var _element$classList;

      var classes = function () {
        var str = className;
        var regex = new RegExp(' ', 'g');
        str = str.replace(regex, '", "');
        str = "[\"".concat(str, "\"]");
        return tryJSONParse(str);
      }();

      (_element$classList = element.classList)[modification].apply(_element$classList, _toConsumableArray(classes));

      return true;
    }
  } else {
    var error = new Error();
    error.name = "".concat(modification, "Class Error");
    error.message = 'Passed element is undefined.';
    throw error;
  }
}

function hasClass(element, className) {
  return modifyClass(element, className, 'contains');
}

function addClass(element, className) {
  return modifyClass(element, className, 'add');
}

function delClass(element, className) {
  return modifyClass(element, className, 'remove');
}

function toggleClass(element, className) {
  var state = hasClass(element, className);

  if (state) {
    delClass(element, className);
  } else {
    addClass(element, className);
  }
}

function getClass(element, className) {
  return element.getElementsByClassName(className)[0];
}

function getClasses(element, className) {
  return element.getElementsByClassName(className);
}

function getTags(element, tagName) {
  return element.getElementsByTagName(tagName);
}

function getTag(element, tagName) {
  return getTags(element, tagName)[0];
}

function hasAttr(element, attr) {
  return element.getAttribute(attr) !== null;
} // Returns a list of the specified children elements


function getElements(parent, elements) {
  var children = getTags(parent, '*');
  var matches = [];

  var _loop = function _loop(i) {
    var child = children[i];
    var type = child.tagName.toLowerCase();

    function match() {
      matches.push(child);
    } // Keywords


    if (typeof elements == 'string') {
      if (elements == 'focusables') {
        if (type == 'a' || type == 'button' || type == 'input' || type == 'select' || type == 'textarea') {
          match();
        }
      } else if (elements == 'clickables') {
        if (type == 'a' || type == 'button') {
          match();
        }
      } else if (elements == 'inputs') {
        if (type == 'input' || type == 'select' || type == 'textarea') {
          match();
        }
      }
    } // Array
    else if (_typeof(elements) == 'object' && elements.length > 0) {
        for (var x = 0; x < elements.length; x++) {
          var element = elements[x];

          if (type == element) {
            match();
            break;
          }
        }
      } else {
        throw new TypeError('Function "getElements" was called with an invalid element list.');
      }
  };

  for (var i = 0; i < children.length; i++) {
    _loop(i);
  }

  return matches;
} // Copy elements


function copyElm(element) {
  var deepClone = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  return element.cloneNode(deepClone);
}

function getTemplate(templateID) {
  var deepClone = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var e = document.getElementById(templateID);

  if (e !== null && e !== undefined) {
    if (e.tagName == 'TEMPLATE') {
      return copyElm(e.content.children[0].cloneNode(deepClone));
    } else {
      return e.children[0].cloneNode(deepClone);
    }
  } else {
    throw 'getTemplate called on an undefined element: ' + templateID;
  }
} // Traverse the dom until it reaches the specified element


function findAttr(startingPos, direction, type, attribute, match) {
  var result;

  function check(e) {
    if (e.tagName != 'BODY') {
      var attr = e.getAttribute(attribute);

      if (attr !== undefined && attr !== null && attr != '') {
        if (type == 'exist' || type == 'match' && attr == match || type == 'not-match' && attr != match) {
          result = e;
          return true;
        }
      }

      if (direction == 'up') {
        check(e.parentNode);
      } else {
        var children = e.children;

        for (var i = 0; i < children.length; i++) {
          var child = children[i];
          check(child);
        }
      }
    } else {
      return false;
    }
  }

  check(startingPos.parentNode);
  return result;
}

function findClass(startingPos, direction, className) {
  var result;

  function check(e) {
    if (e.tagName != 'BODY') {
      if (hasClass(e, className) === true) {
        result = e;
        return;
      }

      if (direction == 'up') {
        check(e.parentNode);
      } else {
        var children = e.children;

        for (var i = 0; i < children.length; i++) {
          var child = children[i];
          check(child);
        }
      }
    } else {
      result = false;
      return;
    }
  }

  check(startingPos.parentNode);
  return result;
}

function reachElement(startingPos, direction, name) {
  var type = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'class';
  var result;

  function check(e) {
    if (type == 'class' && hasClass(e, name) === true || type == 'tag' && e.tagName == name.toUpperCase() || type == 'attr' && e[name] !== undefined && e[name] != '') {
      result = e;
      return true;
    } else {
      if (direction == 'up') {
        check(e.parentNode);
      } else {
        var children = e.children;

        for (var i = 0; i < children.length; i++) {
          var child = children[i];
          check(child);
        }
      }
    }
  }

  check(startingPos);
  return result;
} // Merge two or more objects


function mergeObj(objects) {
  var length = arguments.length; // objects.length;

  var result = {};

  function parseVal(base, key, val) {
    if (val && val.constructor.name == 'Object') {
      var subKeys = Object.keys(val);

      for (var y = 0; y < subKeys.length; y++) {
        var subKey = subKeys[y];
        var subVal = val[subKey];

        if (!base[key]) {
          base[key] = {};
        }

        parseVal(base[key], subKey, subVal);
      }
    } else {
      base[key] = val;
    }
  }

  for (var i = 0; i < length; i++) {
    var arg = arguments[i];
    var keys = Object.keys(arg);

    for (var x = 0; x < keys.length; x++) {
      var key = keys[x];
      var _val = arg[key];
      parseVal(result, key, _val);
    }
  }

  return result;
} // Get all results of a Regular Expression (matchAll Polyfill)


function regexMatchAll(exp, string) {
  var result = [];

  var regex = function () {
    if (exp.global === true) {
      return exp;
    } else {
      return new RegExp(exp, 'g');
    }
  }();

  while ((matches = regex.exec(string)) !== null) {
    result.push(matches);
  }

  return result;
} // Manage Cookies


function getCookie() {
  var cookie = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'all';

  function wrap(array) {
    var obj = {};
    var keys = ['string', 'name', 'value'];

    for (var x = 0; x < keys.length; x++) {
      obj[keys[x]] = array[x];
    }

    return obj;
  }

  function get(match) {
    var result = regexMatchAll('(' + match + ')=([^;]+)(?:;|$)', document.cookie);

    if (result.length == 1) {
      return wrap(result[0]);
    } else if (result.length > 1) {
      var array = [];

      for (var i = 0; i < result.length; i++) {
        array.push(wrap(result[i]));
      }

      return array;
    } else {
      return false;
    }
  }

  if (cookie == 'all') {
    return get('[^\\s=]+');
  } else {
    return get(cookie);
  }
}

function setCookie(setProps) {
  var dfProps = {
    'name': '',
    'value': '',
    'path': '/',
    'domain': false,
    'max-age': 7890000,
    'expires': false,
    'secure': false,
    'samesite': 'lax'
  };
  var props = mergeObj([dfProps, setProps]);
  var keys = Object.keys(props);
  var str = ''; // Cookie Name + Value

  (function () {
    // Name
    if (props.name != '') {
      str += props.name + '=';
    } else {
      throw new Error('Could not update cookie: Property "name" is required but was not specified.\r\n\r\n' + JSON.stringify(setProps));
    } // Value


    str += val = encodeURIComponent(props.value);
  })(); // Cookie Properties


  (function () {
    for (var i = 2; i < keys.length; i++) {
      var prop = keys[i];
      var _val2 = props[prop];
      var strVal = '';

      if (_val2 !== false) {
        if (typeof _val2 != 'boolean') {
          strVal = '=' + _val2;
        }

        str += '; ' + prop + strVal;
      }
    }
  })();

  document.cookie = str;
  return str;
}

function deleteCookie(cookie) {
  var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'immediately';

  function del(val) {
    setCookie({
      'name': cookie,
      'max-age': val,
      'expires': val
    });
  }

  if (type == 'immediately') {
    del(0);
  } else {
    del(false);
  }
} // Update a Progress Bar


function updateProgressBar() {
  var progressBar = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 100;
  var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var defaultOptions = {
    interval: false,
    intervalDelay: 1000,
    intervalIncrement: 5,
    start: null,
    resetOnZero: false,
    useWidth: false
  };

  if (progressBar !== null && progressBar.getAttribute('role') == 'progressbar') {
    var bar = getClass(progressBar, 'progress');
    var opt = mergeObj(defaultOptions, options);
    var id = progressBar.id;

    if (!opt.resetOnZero || value > 0) {
      // Update Progress Bar
      var change = function change() {
        var newVal = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : value;
        progressBar.setAttribute('data-progress', newVal);
        progressBar.setAttribute('aria-valuenow', newVal);

        if (!opt.useWidth) {
          bar.style.transform = "translateX(".concat(newVal, "%)");
        } else {
          bar.style.width = "".concat(newVal, "%");
        }
      };

      if (pbIntervals[id]) {
        clearInterval(pbIntervals[id].interval);
      } // Immediate Change


      if (opt.interval === false) {
        change();
      } // Interval Change
      else {
          var now = tryParseInt(progressBar.getAttribute('data-progress'), 'ignore');

          if (opt.start !== null && now < opt.start) {
            change(opt.start);
          } else {
            change(now + opt.intervalIncrement);
          } // Check for ID


          if (progressBar.id == '') {
            id = "progressbar_".concat(randomNum(100, 1000));
            progressBar.id = id;
          }

          pbIntervals[id] = {};
          pbIntervals[id].end = value;
          pbIntervals[id].increment = opt.intervalIncrement;
          pbIntervals[id].interval = setInterval(function () {
            var id = progressBar.id;
            var now = tryParseInt(progressBar.getAttribute('data-progress'), 'throw');
            var nextVal = now + pbIntervals[id].increment;
            var end = pbIntervals[id].end;

            if (nextVal <= end) {
              change(nextVal);
            } else {
              change(end);
              clearInterval(pbIntervals[id].interval);
              delete pbIntervals[id];
            }
          }, opt.intervalDelay);
        }
    } // Reset Progress Bar
    else {
        progressBar.setAttribute('data-progress', 0);
        bar.style.removeProperty('transform');
      }
  } else {
    var error = new Error();

    (function () {
      error.name = 'updateProgressBar Error';
      error.message = "A valid progress bar was not passed.\n\rProgress Bar: ".concat(progressBar);
    })();

    throw error;
  }
} // Determine if a plural letter is needed


function checkPlural(val) {
  var letter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 's';

  if (val != 1) {
    return 's';
  } else {
    return '';
  }
}