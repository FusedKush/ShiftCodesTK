function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

//*** Load State ***//
var globalFunctionsReady = true; // Toggle element states

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


function newAjaxRequest(type, file, callback, parameters, requestHeader) {
  var request = function () {
    if (window.XMLHttpRequest) {
      return new XMLHttpRequest();
    } else if (window.ActiveXObject) {
      return new ActiveXObject('Microsoft.XMLHttp');
    }
  }();

  function handleResponse() {
    function processResponse() {
      if (request.readyState === XMLHttpRequest.DONE) {
        if (request.status === 200) {
          callback(request.responseText);
        } else {
          console.error('Ajax "' + type + '" Request Failed. Status Code: ' + request.status + '. Requested File: ' + file, 'error');
        }
      }
    }

    if ((typeof devTools === "undefined" ? "undefined" : _typeof(devTools)) == 'object' && devTools.suppressAjaxErrorCatching === true) {
      processResponse();
    } else {
      try {
        processResponse();
      } catch (e) {
        console.error('Caught Exception in Ajax ' + type + ' Request: ' + e + '. Requested File: ' + file);
      }
    }
  }

  request.onreadystatechange = handleResponse;
  request.open(type, file, true);

  if (requestHeader == 'form') {
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  }

  if (typeof parameters == 'undefined') {
    request.send();
  } else {
    request.send(parameters);
  }
} // Handles Date Requests


function getDate() {
  var format = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'y-m-d';
  var separator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '-';
  var date = {};

  (function () {
    date.base = new Date();
    date.year = date.base.getFullYear();
    date.month = ('0' + (date.base.getMonth() + 1)).slice(-2);
    date.day = ('0' + date.base.getDate()).slice(-2);
  })();

  var formats = {
    'y': 'year',
    'm': 'month',
    'd': 'day'
  };
  return date[formats[format.slice(0, 1)]] + separator + date[formats[format.slice(2, 3)]] + separator + date[formats[format.slice(4, 5)]];
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
} // Retrieve a copy of a template


function getTemplate(templateID) {
  var deepClone = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var e = document.getElementById(templateID);

  if (e !== null && e !== undefined) {
    if (e.tagName == 'TEMPLATE') {
      return e.content.children[0].cloneNode(deepClone);
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
    if (val !== null && val !== undefined && val.constructor.name == 'Object') {
      var subKeys = Object.keys(val);

      for (var y = 0; y < subKeys.length; y++) {
        var subKey = subKeys[y];
        var subVal = val[subKey];

        if (base[key] === undefined) {
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
} // Error Handling


function thrownTryError(error, behavior) {
  if (behavior == 'silent') {
    console.error(error);
    return false;
  } else if (behavior == 'throw') {
    throw error;
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
    thrownTryError(error, behavior);
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
    thrownTryError(error, behavior);
  }
} // Update a Progress Bar


function updateProgressBar() {
  var progressBar = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 100;
  var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var defaultOptions = {
    interval: false,
    intervalDelay: 1000,
    intervalIncrement: 10
  };

  if (progressBar !== null && progressBar.getAttribute('role') == 'progressbar') {
    var bar = getClass(progressBar, 'progress');
    var opt = Object.assign(defaultOptions, options);
    var now = tryParseInt(progressBar.getAttribute('aria-valuenow'));
    var id = progressBar.id;

    if (value !== 0) {
      // Update Progress Bar
      var change = function change() {
        var newVal = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : value;
        progressBar.setAttribute('aria-valuenow', newVal);
        bar.style.transform = "translateX(".concat(newVal, "%)");
      };

      if (!pbIntervals[id]) {
        pbIntervals[id] = {};
      } else {
        clearInterval(pbIntervals[id].interval);
      } // Immediate Change


      if (opt.interval === false) {
        change();
      } // Interval Change
      else {
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
            var now = tryParseInt(progressBar.getAttribute('aria-valuenow'), 'throw');
            var nextVal = now + pbIntervals[id].increment;
            var end = pbIntervals[id].end;

            if (nextVal <= end) {
              updateProgressBar(progressBar, nextVal);
            } else {
              updateProgressBar(progressBar, end);
              clearInterval(pbIntervals[id].interval);
              pbIntervals[id] = {};
            }
          }, opt.intervalDelay);
        }
    } // Reset Progress Bar
    else {
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
}