//*** Load State ***//
var globalFunctionsReady = true;

// Toggle element states
function disenable (element, state, optTx) {
  let tabIndexes = {
    true: '-1',
    false: '0'
  };

  element.disabled = state;
  element.setAttribute('aria-disabled', state);

  if (state === true) { element.setAttribute('disabled', ''); }
  else                { element.removeAttribute('disabled'); }
  if (optTx === true) { element.tabIndex = tabIndexes[state]; }
}
function vishidden (element, state, optTx) {
  let tabIndexes = {
    true: '-1',
    false: '0'
  };

  element.hidden = state;
  element.setAttribute('aria-hidden', state);

  if (state === true) { element.setAttribute('hidden', ''); }
  else                { element.removeAttribute('hidden'); }

  if (optTx === true) { element.tabIndex = tabIndexes[state]; }
}
// Update ELement Labels
function updateLabel(element, label) {
  element.title = label;
  element.setAttribute('aria-label', label);
}
// Handles AJAX Requests
function newAjaxRequest (properties) {
  let request = (function () {
    if (window.XMLHttpRequest) {
      return new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
      return new ActiveXObject('Microsoft.XMLHttp');
    }
  })();
  let defaultProps = {
    type: 'GET',
    file: null,
    callback: function (response) {
      return response;
    },
    params: 'none',
    requestHeader: 'default'
  };
  let props = mergeObj(defaultProps, properties);

  function ajaxError (e) {
    let error = new Error;
        error.name = 'newAjaxRequest Error';
        error.message = `An error occurred with Ajax Request "${props.type}: ${props.file}".\n\rError: ${e}`;

    throw error;
  }

  if (props.file !== null) {
    // Handle Response
    request.onreadystatechange = function () {
      try {
        if (request.readyState === XMLHttpRequest.DONE) {
          if (request.status === 200) {
            props.callback(request.responseText);
          }
          else {
            throw `Status Code ${request.status} returned.`;
          }
        }
      }
      catch (e) {
        ajaxError(e);
      }
    }

    request.open(props.type, props.file, true);

    if (props.requestHeader == 'form') {
      request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    if (props.params == 'none') { request.send(); }
    else                        { request.send(props.params); }
  }
  else {
    ajaxError(`File path was not specified.\n\rProperties: ${JSON.stringify(props)}`);
  }
}
// Handles Date Requests
function getDate (format = 'y-m-d', separator = '-') {
  let date = {};
    (function () {
      date.base =  new Date();
      date.year =  date.base.getFullYear();
      date.month = ('0' + (date.base.getMonth() + 1)).slice(-2);
      date.day =   ('0' + date.base.getDate()).slice(-2);
    })();
  let formats = {
    'y': 'year',
    'm': 'month',
    'd': 'day'
  };

  return date[formats[format.slice(0, 1)]] + separator +
         date[formats[format.slice(2, 3)]] + separator +
         date[formats[format.slice(4, 5)]];
}
// Generates a random number between two values
function randomNum (min, max) {
  return Math.round(Math.random() * (max - min) + min);
}
// DOM shorthands
function modifyClass (element, className, modification) {
  if (element !== null && element !== undefined) {
    if (modification == 'contains') {
      return element.classList[modification](className);
    }
    else {
      let classes = (function () {
        let str = className;
        let regex = new RegExp(' ', 'g');

        str = str.replace(regex, '", "');
        str = `["${str}"]`;
        return tryJSONParse(str);
      })();

      element.classList[modification](...classes);
      return true;
    }
  }
  else {
    let error = new Error;
      error.name = `${modification}Class Error`;
      error.message = 'Passed element is undefined.';
    throw error;
  }
}
function hasClass (element, className) {
  return modifyClass(element, className, 'contains');
}
function addClass (element, className) {
  return modifyClass(element, className, 'add');
}
function delClass (element, className) {
  return modifyClass(element, className, 'remove');
}
function toggleClass (element, className) {
  let state = hasClass(element, className);

  if (state) { delClass(element, className); }
  else       { addClass(element, className); }
}
function getClass (element, className) {
  return element.getElementsByClassName(className)[0];
}
function getClasses (element, className) {
  return element.getElementsByClassName(className);
}
function getTags (element, tagName) {
  return element.getElementsByTagName(tagName);
}
function getTag (element, tagName) {
  return getTags(element, tagName)[0];
}
function hasAttr(element, attr) {
  return (element.getAttribute(attr) !== null);
}
// Returns a list of the specified children elements
function getElements(parent, elements) {
  let children = getTags(parent, '*');
  let matches = [];

  for (let i = 0; i < children.length; i++) {
    let child = children[i];
    let type = child.tagName.toLowerCase();

    function match () {
      matches.push(child);
    }

    // Keywords
    if (typeof elements == 'string') {
      if (elements == 'focusables') {
        if (type == 'a' || type == 'button' || type == 'input' || type == 'select' || type == 'textarea') {
          match();
        }
      }
      else if (elements == 'clickables') {
        if (type == 'a' || type == 'button') {
          match();
        }
      }
      else if (elements == 'inputs') {
        if (type == 'input' || type == 'select' || type == 'textarea') {
          match();
        }
      }
    }
    // Array
    else if (typeof elements == 'object' && elements.length > 0) {
      for (let x = 0; x < elements.length; x++) {
        let element = elements[x];

        if (type == element) {
          match();
          break;
        }
      }
    }
    else {
      throw new TypeError ('Function "getElements" was called with an invalid element list.');
    }
  }

  return matches;
}
// Retrieve a copy of a template
function getTemplate(templateID, deepClone = true) {
  let e = document.getElementById(templateID);

  if (e !== null && e !== undefined) {
    if (e.tagName == 'TEMPLATE') {
      return e.content.children[0].cloneNode(deepClone);
    }
    else {
      return e.children[0].cloneNode(deepClone);
    }
  }
  else {
    throw ('getTemplate called on an undefined element: ' + templateID);
  }
}
// Traverse the dom until it reaches the specified element
function findAttr (startingPos, direction, type, attribute, match) {
  let result;

  function check(e) {
    if (e.tagName != 'BODY') {
      let attr = e.getAttribute(attribute);

      if (attr !== undefined && attr !== null && attr != '') {
        if (type == 'exist' ||
            type == 'match' && attr == match ||
            type == 'not-match' && attr != match) {
          result = e;
          return true;
        }
      }
      if (direction == 'up') {
        check(e.parentNode);
      }
      else {
        let children = e.children;

        for (let i = 0; i < children.length; i++) {
          let child = children[i];

          check(child);
        }
      }
    }
    else {
      return false;
    }
  }

  check(startingPos.parentNode);
  return result;
}
function findClass (startingPos, direction, className) {
  let result;

  function check(e) {
    if (e.tagName != 'BODY') {
      if (hasClass(e, className) === true) {
        result = e;
        return;
      }
      if (direction == 'up') {
        check(e.parentNode);
      }
      else {
        let children = e.children;

        for (let i = 0; i < children.length; i++) {
          let child = children[i];

          check(child);
        }
      }
    }
    else {
      result = false;
      return;
    }
  }

  check(startingPos.parentNode);
  return result;
}
function reachElement(startingPos, direction, name, type = 'class') {
  let result;

  function check (e) {
    if (type == 'class' && hasClass(e, name) === true ||
    type == 'tag' && e.tagName == name.toUpperCase() ||
    type == 'attr' && e[name] !== undefined && e[name] != '') {
      result = e;
      return true;
    }
    else {
      if (direction == 'up') {
        check(e.parentNode);
      }
      else {
        let children = e.children;

        for (let i = 0; i < children.length; i++) {
          let child = children[i];

          check(child);
        }
      }
    }
  }

  check(startingPos);
  return result;
}
// Merge two or more objects
function mergeObj (objects) {
  let length = arguments.length; // objects.length;
  let result = {};

  function parseVal(base, key, val) {
    if (val !== null && val !== undefined && val.constructor.name == 'Object') {
      let subKeys = Object.keys(val);

      for (let y = 0; y < subKeys.length; y++) {
        let subKey = subKeys[y];
        let subVal = val[subKey];

        if (base[key] === undefined) {
          base[key] = {};
        }

        parseVal(base[key], subKey, subVal);
      }
    }
    else {
      base[key] = val;
    }
  }

  for (let i = 0; i < length; i++) {
    let arg = arguments[i];
    let keys = Object.keys(arg);

    for (let x = 0; x < keys.length; x++) {
      let key = keys[x];
      let val = arg[key];

      parseVal(result, key, val);
    }
  }

  return result;
}
// Get all results of a Regular Expression (matchAll Polyfill)
function regexMatchAll(exp, string) {
  let result = [];
  let regex = (function () {
    if (exp.global === true) { return exp; }
    else                     { return new RegExp(exp, 'g'); }
  })();

  while ((matches = regex.exec(string)) !== null) {
    result.push(matches);
  }

  return result;
}
// Manage Cookies
function getCookie(cookie = 'all') {
  function wrap(array) {
    let obj = {};
    let keys = ['string', 'name', 'value'];

    for (let x = 0; x < keys.length; x++) {
      obj[keys[x]] = array[x];
    }

    return obj;
  }
  function get(match) {
    let result = regexMatchAll('(' + match + ')=([^;]+)(?:;|$)', document.cookie);

    if (result.length == 1) {
      return wrap(result[0]);
    }
    else if (result.length > 1) {
      let array = [];

      for (let i = 0; i < result.length; i++) {
        array.push(wrap(result[i]));
      }

      return array;
    }
    else {
      return false;
    }
  }

  if (cookie == 'all') { return get('[^\\s=]+'); }
  else                 { return get(cookie); }
}
function setCookie(setProps) {
  let dfProps = {
    'name': '',
    'value': '',
    'path': '/',
    'domain': false,
    'max-age': 7890000,
    'expires': false,
    'secure': false,
    'samesite': 'lax'
  }
  let props = mergeObj([dfProps, setProps]);
  let keys = Object.keys(props);
  let str = '';

  // Cookie Name + Value
  (function () {
    // Name
    if (props.name != '') {
      str += props.name + '=';
    }
    else {
      throw new Error('Could not update cookie: Property "name" is required but was not specified.\r\n\r\n' + JSON.stringify(setProps));
    }
    // Value
    str += val = encodeURIComponent(props.value);
  })();
  // Cookie Properties
  (function () {
    for (let i = 2; i < keys.length; i++) {
      let prop = keys[i];
      let val = props[prop];
      let strVal = '';

      if (val !== false) {
        if (typeof val != 'boolean') {
          strVal = '=' + val;
        }

        str += '; ' + prop + strVal;
      }
    }
  })();

  document.cookie = str;
  return str;
}
function deleteCookie(cookie, type = 'immediately') {
  function del (val) {
    setCookie({
      'name': cookie,
      'max-age': val,
      'expires': val
    });
  }

  if (type == 'immediately') { del(0); }
  else                       { del(false); }
}
// Error Handling
function thrownTryError (error, behavior) {
  if (behavior == 'silent') {
    console.error(error);
    return false;
  }
  else if (behavior == 'throw') {
    throw error;
  }
  else {
    error.message = `${error.message}\n\r\n\rAdditionally, the behavior parameter is invalid.\n\rBehavior: ${behavior}`;
    throw error;
  }
}
function tryParseInt (int = null, behavior = 'silent') {
  let error = new Error;
    (function () {
      error.name = 'parseInt Error';
      error.message = `Not a valid number.\n\rInt: ${int}`;
    })();
  let result = parseInt(int);

  if (!isNaN(result)) {
    return result;
  }
  else {
    thrownTryError(error, behavior);
  }
}
function tryJSONParse (string = null, behavior = 'silent') {
  let error = new Error;
    (function () {
      error.name = 'JSONParse Error';
      error.message = `Not a valid JSON string.\n\rString: ${string}`;
    })();

  try {
    return JSON.parse(string);
  }
  catch (e) {
    thrownTryError(error, behavior);
  }
}
// Update a Progress Bar
function updateProgressBar (progressBar = null, value = 100, options = {}) {
  let defaultOptions = {
    interval: false,
    intervalDelay: 1000,
    intervalIncrement: 10
  };

  if (progressBar !== null && progressBar.getAttribute('role') == 'progressbar') {
    let bar = getClass(progressBar, 'progress');
    let opt = Object.assign(defaultOptions, options);
    let now = tryParseInt(progressBar.getAttribute('aria-valuenow'));
    let id = progressBar.id;

    if (value !== 0) {
      // Update Progress Bar
      function change (newVal = value) {
        progressBar.setAttribute('aria-valuenow', newVal);
        bar.style.transform = `translateX(${newVal}%)`;
      }

      if (!pbIntervals[id]) {
        pbIntervals[id] = {};
      }
      else {
        clearInterval(pbIntervals[id].interval);
      }

      // Immediate Change
      if (opt.interval === false) {
        change();
      }
      // Interval Change
      else {
        if (opt.start !== null && now < opt.start) {
          change(opt.start);
        }
        else {
          change(now + opt.intervalIncrement);
        }
        // Check for ID
        if (progressBar.id == '') {
          id = `progressbar_${randomNum(100, 1000)}`;
          progressBar.id = id;
        }

        pbIntervals[id] = {};
        pbIntervals[id].end = value;
        pbIntervals[id].increment = opt.intervalIncrement;
        pbIntervals[id].interval = setInterval(function () {
          let id = progressBar.id;
          let now = tryParseInt(progressBar.getAttribute('aria-valuenow'), 'throw');
          let nextVal = now + pbIntervals[id].increment;
          let end = pbIntervals[id].end;

          if (nextVal <= end) {
            updateProgressBar(progressBar, nextVal);
          }
          else {
            updateProgressBar(progressBar, end);
            clearInterval(pbIntervals[id].interval);
            pbIntervals[id] = {};
          }
        }, opt.intervalDelay);
      }
    }
    // Reset Progress Bar
    else {
      bar.style.removeProperty('transform');
    }
  }
  else {
    let error = new Error;
      (function () {
        error.name = 'updateProgressBar Error';
        error.message = `A valid progress bar was not passed.\n\rProgress Bar: ${progressBar}`;
      })();

    throw error;
  }
}
