/*********************************
  Global Functions Script
*********************************/
//*** Load State ***//
var globalFunctionsReady = true;
var pbIntervals = {}; // Holds information about Progress Bar Intervals

//*** Functions ***//
// Updates Disabled State of elements
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
// Updates Hidden State of Elements
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
function newAjaxRequest (type, file, callback, parameters, requestHeader) {
  let request = (function () {
    if (window.XMLHttpRequest) {
      return new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
      return new ActiveXObject('Microsoft.XMLHttp');
    }
  })();

  function handleResponse () {
    function processResponse () {
      if (request.readyState === XMLHttpRequest.DONE) {
        if (request.status === 200) {
          callback(request.responseText);
        }
        else {
          console.error(('Ajax "') + type + ('" Request Failed. Status Code: ') + request.status + ('. Requested File: ') + file, 'error');
        }
      }
    }

    if (typeof devTools == 'object' && devTools.suppressAjaxErrorCatching === true) {
      processResponse();
    }
    else {
      try {
        processResponse();
      }
      catch(e) {
        console.error(('Caught Exception in Ajax ') + type + (' Request: ') + e + ('. Requested File: ') + file);
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
  }
  else {
    request.send(parameters);
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
      element.classList[modification](className);
    }
  }
  else {
    throw (modification + ('Class called on an undefined element with a className of "') + className + ('"'));
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
