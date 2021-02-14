var globalFunctionsReady = true; // Load state
var pbIntervals = {};

/** Reserved HTML Characters and their HTML Entity Equivalents */
const RESERVED_HTML_CHARACTERS = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;'
}

// Error Handling
/**
 * Returns an error determined by the value of behavior
 *
 * @param {Error} error The error object returned by a try function
 * @param {'silent'|'throw'|'ignore'} behavior The requested behavior
 * @returns {?number} Returns false if behavior is set to 'silent' or 'ignore'
 */
function tryError (error, behavior) {
  if (behavior == 'silent') {
    console.error(error);
    return false;
  }
  else if (behavior == 'throw') {
    throw error;
  }
  else if (behavior == 'ignore') {
    return false;
  }
  else {
    error.message = `${error.message}\n\r\n\rAdditionally, the behavior parameter is invalid.\n\rBehavior: ${behavior}`;
    throw error;
  }
}
/**
 * Attempts to execute a function
 *
 * @param {Object} settings Execution settings and preferences
    * @param {Function} settings.function The function to be executed
    * @param {number|boolean} [settings.attempts=false] The number of times the function will be executed, or false to retry until success
    * @param {number} [settings.delay=250] The time between attempts in miliseconds
    * @param {string} [settings.behavior='silent'] How errors are handled if the attempts threshold is reached
    * @param {boolean} [settings.logCatch=false] Whether or not to log errors caught by the try...catch block 
    * @param {string} [settings.customError] A custom error message to use if an error is returned
 * @param {number} [currentAttempt=1] The number of times the function has attempted to execute
 * @returns {*} Returns the return value of the passed function on success or false if the attempts threshold was reached and behavior is set to 'silent' or 'ignore'
 */
function tryToRun(settings, currentAttempt = 1) {
  let sets = (function () {
    if (currentAttempt) {
      let defaultSettings = {
        function: function () {
          return true;
        },
        attempts: false,
        delay: 250,
        behavior: 'silent',
        logCatch: false,
        customError: false
      };

      return mergeObj(defaultSettings, settings);
    }
    else {
      return settings;
    }
  })();

  function failed() {
    if (currentAttempt <= sets.attempts || !sets.attempts) {
      setTimeout(function () {
        tryToRun(settings, currentAttempt + 1);
      }, sets.delay);
    }
    else {
      let error = new Error;
          error.name = 'tryToRun Error';

      if (sets.customError !== false) {
        error.message = sets.customError;
      }
      else {
        error.message = `Max Tries Exceeded.\r\n\r\nSettings: ${JSON.stringify(sets)}`;
      }
      if (sets.logCatch) {
        error.message += `\r\n\r\nCaught Error: ${e}`;
      }

      if (sets.behavior == 'throw') {
        throw error;
      }
      else if (sets.behavior == 'silent') {
        console.error(error);
        return false;
      }
      else if (sets.behavior == 'ignore') {
        return false;
      }
    }
  }

  try {
    let result = sets.function();

    if (result === false) {
      failed();
    }
    else {
      return result;
    }
  }
  catch (e) {
    failed();
  }
}
/**
 * Attempts to parse a string and convert it to an integer
 *
 * @param {(string|number)} [int=null] The string or number to parse for the integer
 * @param {'silent'|'throw'|'ignore'} [behavior='silent'] How the error is handled if the integer cannot be parsed
 * @returns {(number|boolean)} Returns the parsed integer on success, false on failure if behavior is set to 'silent' or 'ignore'
 */
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
    return tryError(error, behavior);
  }
}
/**
 * Attempts to parse a JSON string and convert it to an object
 *
 * @param {string} [string=null] The string to be parsed
 * @param {'silent'|'throw'|'ignore'} [behavior='silent'] How the error is handled if the string cannot be parsed
 * @returns {(Object|boolean)} Returns the JSON Object on success, false on failure if behavior is set to 'silent' or 'ignore'
 */
function tryJSONParse (string = null, behavior = 'silent') {
  let error = new Error;
    (function () {
      error.name = 'JSONParse Error';
      error.message = `Not a valid JSON string.\n\rString: ${string}`;
    })();

  try {
    let skipAttempts = 0;
    let trimmedString = '';
    let parsedString = string;
    let parsedJSON = false;

    if (parsedString == '') {
      parsedString = '{}';
    }

    while (parsedJSON === false) {
      try {
        parsedJSON = JSON.parse(parsedString);
      }
      catch (error) {
        let bracketPos = parsedString.indexOf('{');
      
        trimmedString = parsedString.slice(0, bracketPos);
        parsedString = parsedString.slice(bracketPos);
        skipAttempts++;
      }

      if (skipAttempts == 10) {
        throw error;
      }
    }

    if (skipAttempts > 0) {
      console.warn(`tryJSONParse Warning: Invalid content was found before the JSON Data and has been trimmed.\n\rTrimmed Content: ${trimmedString}\n\rOriginal String: ${string}`);
    }

    return parsedJSON;
  }
  catch (e) {
    return tryError(error, behavior);
  }
}

// Scripting
/**
 * Combines multiple objects into a single object
 *
 * @param {Object} objects The objects that you want to merge
 * @returns {Object} Returns an object made up of all provided objects
 */
function mergeObj (objects) {
  let length = arguments.length; // objects.length;
  let result = {};

  function parseVal(base, key, val) {
    if (val && val.constructor.name == 'Object') {
      let subKeys = Object.keys(val);

      if (!base[key]) {
        base[key] = {};
      }

      for (let y = 0; y < subKeys.length; y++) {
        let subKey = subKeys[y];
        let subVal = val[subKey];

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
/**
 * Retrieves and returns all of the matches of a regular expression
 *
 * @param {string} exp The expression to search the string with
 * @param {string} string The string that is being searched
 * @returns {Array} Returns an array of all matches on success, an empty array if no matches are found
 */
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
/**
 * Facilitates an AJAX GET or POST Request
 *
 * @param {Object} requestProperties The settings and preferences of the request
  * @param {string} requestProperties.file The path of the requested resource.
  * @param {"GET"|"POST"} [requestProperties.type="GET"] The *Request Type* of the request.
  * @param {object} [requestProperties.params={}] Request parameters, provided as key=value pairs.
  * - *Note: The required request token is automatically sent with the request if it is not explicitly provided.*
  * @param {function|false} [requestProperties.callback] The callback function to be invoked when the request has completed.
  * - The *response text* is provided as the **first argument**.
  * @param {object} [requestProperties.headers] Additional request headers to be sent with the request, provided as key=value pairs.
  * - *Note: **POST** requests automatically send a **Content-Type** header of `application/x-www-form-urlencoded` if it is not explicitly provided.*
  * @param {object} [requestProperties._tokenRefreshed] **Internal** — Indicates that a prior request already attempted to refresh an invalid token.
 * @returns {boolean} Returns **true** if the provided configuration is valid, or **false** if an error occurred. 
 * - *To access the request response, you must specify a callback function using the `callback` property of the `requestProperties` argument.*
 */
function newAjaxRequest (requestProperties) {
  /** The Ajax Request */
  let request = (function () {
    if (window.XMLHttpRequest) {
      return new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
      return new ActiveXObject('Microsoft.XMLHttp');
    }
  })();
  let defaultProperties = {
    file: null,
    type: 'GET',
    params: {},
    callback: false,
    headers: {},
    _tokenRefreshed: false
  };

  /** The resolved Request Properties */
  let properties = (function () {
    let props = mergeObj(defaultProperties, requestProperties);

    props.type = props.type.toUpperCase();

    if (props.type == 'POST' && !props.headers['Content-Type']) {
      props.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }
    if (!props.params._token && !props.params._auth_token) {
      props.params._token = requestToken.get();
    }

    return props;
  })();

  /**
   * Open and Send the Ajax Request
   * 
   * @param {string|null} body The request body if any is provided.
   */
  function openAndSend (body = null) {
    request.open(properties.type, properties.file, true);

    for (let header in properties.headers) {
      let value = properties.headers[header];

      try {
        request.setRequestHeader(header, value);
      }
      catch (e) {
        console.warn(`newAjaxRequest: ${header} is not a valid header or ${value} is not a valid header value.`);
        continue;
      }
    }

    request.send(body);
  }

  // Missing File
  if (properties.file === null) {
    console.error(`newAjaxRequest Error: A file path was not specified.\r\n${JSON.stringify(requestProperties)}`);
    return false;
  }
  // Invalid Request Type
  if (properties.type != 'GET' && properties.type != 'POST') {
    console.error(`newAjaxRequest Error: "${properties.type}" is not a valid Request Type.\r\n${JSON.stringify(requestProperties)}`);
    return false;
  }

  // Handle Response
  request.onreadystatechange = function () {
    if (request.readyState === XMLHttpRequest.DONE) {
      if (request.status == 401) {
        let response = tryJSONParse(request.responseText);

        // Try to retrieve updated request token
        if (response && response.statusMessage == 'Missing or Invalid Request Token' && !properties._tokenRefreshed) {
          properties._tokenRefreshed = true;
          requestToken.check(function () {
            newAjaxRequest(properties);
          });
          
          return;
        }
      }
      if (properties.callback) {
        properties.callback(request.responseText);
      }
    }
  };

  // Build & Send the Ajax Request
  (function () {
    /** The Request Parameters String */
    let paramString = (function () {
      let str = '';

      for (let param in properties.params) {
        let value = properties.params[param];
  
        // Parameter is an Array
        if (value.constructor.name == 'Array') {
          for (let arrayValue of value) {
            if (param.indexOf('[]') == -1) {
              param += '[]';
            }

            str += `${param}=${arrayValue}&`;
          }
        }
        // Parameter is a String
        else {
          str += `${param}=${value}&`;
        }
      }
  
      // Remove trailing ampersand
      return str.slice(0, -1);
    })();

    if (properties.type == "GET") {
      if (!properties._tokenRefreshed) {
        if (properties.file.indexOf('?') == -1) {
          properties.file += '?';
  
        }
        else {
          properties.file += '&';
        }
  
        properties.file += paramString;
      }
      
      openAndSend();
    }
    else if (properties.type == "POST") {
      openAndSend(paramString);
    }
  })();
}
// Global Helper Functions
/**
 * A group of properties and methods for Global Helper Function Stat Collection
 */
const globalFuncStats = {
  /**
   * Record a helper function call
   * 
 *
   * 
   * @param {string} funcName The name of the function.
   * @param {undefined|boolean} funcResult Indicates the result of the function execution. 
   * - **True** indicates that the function executed successfully, without errors.
   * - **False** indicates that the function executed unsuccessfully due to errors.
   * - Omitting this argument will record the function call itself, but not the result. 
   * @param {*} funcReturnValue The return value *type* of the function, if applicable.
   */
  record (funcName, funcResult, funcReturnValue) { 
    if (this.statCollectionEnabled) {
      if (typeof funcResult == 'undefined') {
        if (this.functionData.uses[funcName] === undefined) {
          this.functionData.uses[funcName] = 0;
        }
  
        this.total++;
        this.functionData.uses[funcName]++;
      }
      else {
        if (funcResult) {
          const returnValues = this.functionData.returnValues;
          const returnValueType = (function () {
            if (funcReturnValue === false || funcReturnValue === true) {
              return funcReturnValue;
            }
            else if (Array.isArray(funcReturnValue)) {
              return 'array';
            }
            else {
              return typeof funcReturnValue;
            }
          })();
    
          if (returnValues[returnValueType] === undefined) {
            returnValues[returnValueType] = {
              TOTAL: 0
            };
          }
          if (returnValues[returnValueType][funcName] === undefined) {
            returnValues[returnValueType][funcName] = 0;
          }
    
          returnValues[returnValueType].TOTAL++;
          returnValues[returnValueType][funcName]++
          this.success++;
        }
        else {
          this.errors++;
        }
      }
    }
  },
  updateModal () {
    if (this.statCollectionEnabled) {
      const currentStats = Object.assign({}, globalFuncStats);

      function getCounter (counter, newCount, ignoredTypes = '') {
        let oldValue = (function () {
          let oldValue = counter.innerHTML;

          oldValue = (function () {
            let regex = new RegExp('^<span class="value">([\\d,]+)</span>');
            let matches = oldValue.match(regex);

            if (matches) {
              return matches[1];
            }

            return "0";
          })();
          // oldValue = oldValue.match(new RegExp('^<span class="value">([\\d,]+)</span>'))[1];
          oldValue = oldValue.replace(/,/g, '');
          oldValue = tryParseInt(oldValue);

          return oldValue;
        })();
        let newValue = newCount.toLocaleString();
        let counterStr = '';
        
        counterStr += `<span class="value">${newValue}</span>`;
        
        if (ignoredTypes.indexOf('percentage') == -1) {
          let percentage = ((newCount / currentStats.total) * 100).toFixed(2);
          counterStr += `<span class="percentage">${percentage}%</span>`;
        }
        if (ignoredTypes.indexOf('speed') == -1) {
          let speed = Math.round((newCount - oldValue) / 5);
          counterStr += `<span class="speed">${speed} / sec</span>`;
        }

        return counterStr;
      }
  
      edit.class(globalFuncStats.modal, 'add', 'updating-stats');
  
      setTimeout(() => {
        // Totals
        (function () {
          const section = dom.find.child(globalFuncStats.modal, 'class', 'section totals');
          const totals = [ 'total', 'success', 'errors' ];
    
          for (let count of totals) {
            let field = dom.find.child(section, 'class', count);
            let fieldValue = dom.find.child(field, 'tag', 'dd');
            // let oldValue = tryParseInt(fieldValue.innerHTML.replace(/(^\d+) .+$/, '$1'));
    
            // fieldValue.innerHTML = currentStats[count];
            // fieldValue.innerHTML += `<br>(${Math.round(currentStats[count] - oldValue) / 5} /sec)`;
            fieldValue.innerHTML = getCounter(fieldValue, currentStats[count], 'percentage');
  
            // if (count != 'total') {
            //   // updateCounter(fieldValue, currentStats[count]);
            //   fieldValue.innerHTML += ` (${((currentStats[count] / currentStats.total) * 100).toFixed(2)}%)`;
            // }
          }
        })();
        // Function Calls
        (function () {
          const section = dom.find.child(globalFuncStats.modal, 'class', 'section calls');
          const wrapper = section.childNodes[0];
          const uses = (function () {
            const uses = currentStats.functionData.uses;
            let sortedUses = [];
    
            for (let funcName in uses) {
              sortedUses.push([funcName, uses[funcName]]);
            }
    
            sortedUses.sort((a, b) => {
              return b[1] - a[1];
            });
    
            return sortedUses;
          })();

          if (uses.length > 0) {
            for (let useIndex in uses) {
              let use = uses[useIndex];
              let funcName = use[0];
              let useCount = use[1];
              let log = (function () {
                const existingEntry = wrapper.childNodes[(tryParseInt(useIndex) + 1)];

                if (existingEntry && dom.has(existingEntry, 'tag', 'dl')) {
                  return existingEntry;
                }
                else {
                  let dl = document.createElement('dl');  
                  const dt = document.createElement('dt'); 
                  const dd = document.createElement('dd'); 

                  dd.innerHTML = `<span class="value">0</span>
                                  <span class="percentage">0.00%</span>
                                  <span class="speed">0 / sec</span>`;

                  dl.appendChild(dt);
                  dl.appendChild(dd);
                  dl = wrapper.appendChild(dl);

                  if (useIndex == 0) {
                    const placeholder = dom.find.child(section, 'class', 'placeholder');

                    if (placeholder) {
                      deleteElement(placeholder);
                    }
                  }
    
                  return dl;
                }
              })();
    
              dom.find.child(log, 'tag', 'dt').innerHTML = funcName.replace(/^([a-z]+)/, "<code>$1</code>");
              (function () {
                const dd = dom.find.child(log, 'tag', 'dd');
    
                dd.innerHTML = getCounter(dd, useCount);
                // dd.innerHTML = `<span>${useCount.toLocaleString()}</span> <span>${((useCount / currentStats.total) * 100).toFixed(2)}%</span>`;
              })();
            }
          }
        })();
        // Return Values
        (function () {
          const section = dom.find.child(globalFuncStats.modal, 'class', 'section returns');
          const wrapper = section.childNodes[0];
          const returnValues = (function () {
            const values = currentStats.functionData.returnValues;
            let sortedValues = [];
    
            for (let valueType in values) {
              sortedValues.push([valueType, values[valueType].TOTAL, values[valueType]]);
            }
    
            sortedValues.sort((a, b) => {
              return b[1] - a[1];
            });
    
            return sortedValues;
          })();

          if (returnValues.length > 0) {
            if (wrapper.childNodes[0] && dom.has(wrapper.childNodes[0], 'class', 'placeholder')) {
              deleteElement(wrapper.childNodes[0]);
            }
    
            for (let returnValueIndex in returnValues) {
              let returnValue = returnValues[returnValueIndex];
              let returnValueType = returnValue[0];
              let totalOccurrences = returnValue[1];
              let funcOccurrences = returnValue[2];
              let log = (function () {
                const existingEntry = wrapper.childNodes[(tryParseInt(returnValueIndex) + 1)];
    
                if (existingEntry && dom.has(existingEntry, 'tag', 'dl')) {
                  return existingEntry;
                }
                else {
                  let dl = document.createElement('dl');  
                  const dt = document.createElement('dt'); 
                  const dd = document.createElement('dd'); 
    
                  dd.innerHTML = `<span class="value">0</span>
                                  <span class="percentage">0.00%</span>
                                  <span class="speed">0 / sec</span>`;

                  dl.appendChild(dt);
                  dl.appendChild(dd);
    
                  dl = wrapper.appendChild(dl);

                  if (returnValueIndex == 0) {
                    const placeholder = dom.find.child(section, 'class', 'placeholder');

                    if (placeholder) {
                      deleteElement(placeholder);
                    }
                  }
    
                  return dl;
                }
              })();
    
              dom.find.child(log, 'tag', 'dt').innerHTML = returnValueType;

              (function () {
                const dd = dom.find.child(log, 'tag', 'dd');
    
                // dd.innerHTML = `<span>${totalOccurrences}</span>`;
                dd.innerHTML = getCounter(dd, totalOccurrences, 'speed');
                // dd.innerHTML = `<span>${totalOccurrences.toLocaleString()}</span> <span>${((totalOccurrences / currentStats.total) * 100).toFixed(2)}%</span>`;
                
                (function () {
                  const funcOccurrenceList = (function () {
                    let list = [];

                    for (let funcName in funcOccurrences) {
                      list.push([funcName, funcOccurrences[funcName]]);
                    }

                    list.sort((a, b) => {
                      return b[1] - a[1];
                    });

                    return list;
                  })();
                  let funcStats = '';

                  funcStats += '<div class="function-stats">';

                  for (let funcOccurrence of funcOccurrenceList) {
                    let funcName = funcOccurrence[0];
                    let funcOccurrenceCount = funcOccurrence[1];

                    if (funcName == 'TOTAL') {
                      continue;
                    }

                    funcStats += `<dl>
                                    <dt>${funcName.replace(/^([a-z]+)/, "<code>$1</code>")}<dt>
                                    <dd>${getCounter(dd, funcOccurrenceCount, 'speed, percentage')}</dd>
                                  </dl>`;
                    // dd.innerHTML += `<dl><dt>${funcName.replace(new RegExp('^(dom|edit)'), "<code>$1</code>")}</dt><dd>${funcOccurrences[funcName].toLocaleString()}</dd></dl>`;
                  }

                  funcStats += '</div>';

                  dd.innerHTML += funcStats;
                })();
              })();
            }
          }
        })();
  
        edit.class(globalFuncStats.modal, 'remove', 'updating-stats');
      }, 50);
    }
  },
};
(function () {
  /** Indicates if stat collection is enabled or disabled. */
  Object.defineProperty(globalFuncStats, 'statCollectionEnabled', {
    writable: false,
    value: getQueryParameters().show_global_function_stats !== undefined
  });

  if (globalFuncStats.statCollectionEnabled) {
    /** The total number of attempted function calls */
    globalFuncStats.total = 0;
    /** The number of *successful* function calls */
    globalFuncStats.success = 0;
    /** The number of *errored* function calls */
    globalFuncStats.errors = 0;
    /** Function call data and information */
    globalFuncStats.functionData = {
      uses: {},
      returnValues: {}
    };
    globalFuncStats.modalUpdateInterval = 0;
    
    (function () {
      const interval = setInterval(() => {
        if (typeof ShiftCodesTK != 'undefined' && typeof ShiftCodesTK.modals != 'undefined') {
          clearInterval(interval);

          /** The Stat Modal */
          globalFuncStats.modal = dom.find.id('global_function_stats_modal');
          // Setup Modal
          globalFuncStats.modal = ShiftCodesTK.modals.setupModal(globalFuncStats.modal);
          // Modal Listeners
          ShiftCodesTK.modals.registerCallback('global_function_stats_modal', 'on_open', (modal) => {
            globalFuncStats.updateModal();
            globalFuncStats.modalUpdateInterval = setInterval(() => {
              globalFuncStats.updateModal();
            }, 5000);
          });
          ShiftCodesTK.modals.registerCallback('global_function_stats_modal', 'on_close_any', (modal) => {
            clearInterval(globalFuncStats.modalUpdateInterval);
          });
        }
      }, 250);
    })();
  }
})();
/**
 * A group of methods related to finding and accessing elements
 */
const dom = {
  /**
   * Parameter validation for the DOM functions
   *
   * @param {string} func The name of the requested function
   * @param {Element} elm The provided element
   * @param {string} type The provided type
   * @param {string} name The provided name
   * @param {Function} callback The requested function
   * @param {array} validTypes The permitted type strings
   * @returns Returns the return value of the requested function on success, or false on failure
   */
  try: function (func, elm, type, name, callback, validTypes = ['class', 'tag', 'attr']) {
    let checks = [
      elm !== undefined && elm && typeof elm.getElementsByTagName != 'undefined',
      validTypes.indexOf(type) != -1,
      typeof name == 'string' || typeof name == 'number'
    ];
    const stats = this.stats;

    globalFuncStats.record(func);

    if (checks.indexOf(false) == -1) {
      const callbackResult = callback();

      globalFuncStats.record(func, true, callbackResult);

      return callbackResult;
    }
    else {
      try {
        let error = new Error();
            error.name = `${func} Error`;
            error.message = `is not a valid`;
  
        if (!checks[0]) {
          error.message = `Argument 1 ${error.message} element: ${elm}`;
        }
        else if (!checks[1]) {
          error.message = `Argument 2 ${error.message} type: ${type}`;
        }
        else if (!checks[2]) {
          error.message = `Argument 3 ${error.message} class, tag, or attribute: ${name}`;
        }

        globalFuncStats.record(func, false);
        throw error;
      }
      catch (error) {
        console.error(error);
        return false;
      }
    }
  },
  /**
   * Retrieve a property value from an element
   *
   * @param {Element} elm The element to be accessed.
   * @param {"class"|"tag"|"attr"} type The type of property to retrieve.
   * @param {string} name If `type` is set to **attr**, this is the name of the attribute to be retrieved. Otherwise, this parameter has no effect.
   * @returns {Array|string|false} On success, returns a *ClassList* array, the *tagname* of the element, or the *value* of an attribute depending on the value of `type`. Returns **false** if the property value could not be retrieved or if an error occurred.
   */
  get: function (elm, type, name = '') {
    let types = {
      'class': elm.classList    
               ? elm.classList              
               : false,
      'tag': elm.tagName  
             ? elm.tagName.toLowerCase()  
             : false,
      'attr': elm.getAttribute !== undefined && elm.getAttribute(name) !== null
              ? elm.getAttribute(name)     
              : false
    };

    return dom.try('domGet', elm, type, name, function () {
      return types[type];
    });
  },
  /**
   * Determine if an element possesses a certain *class*, *tagname*, or *attribute*
   * 
   * @param {Element} elm The element to be accessed.
   * @param {"class"|"tag"|"attr"} type The type of property to retrieve.
   * @param {string} name The *class*, *tagname*, or *attribute* to check for. If `type` is set to **"class"**, a list of classes can be checked for by separating them with a space.
   * @param {string} value If `type` is set to **attr**, this is the value the attribute must be set to. Otherwise, this parameter has no effect.
   * @param {boolean} checkParent Indicates if the parent(s) of the element should be searched if the element does not possess the provided *class*, *tagname*, or *attribute*.
   * @returns {HTMLElement|boolean} Returns a different set of values depending on the value of `checkParent`:
   * - If `checkParent` is **false**: Returns **true** if the element has the provided *class*, *tagname*, or *attribute*. Otherwise, returns **false**.
   * - If `checkParent` is **true**: Returns the *matching element or parent* if either has the provided *class*, *tagname*, or *attribute*. Otherwise, returns **false**.
   */
  has: function (elm, type, name, value = null, checkParent = false) {
    let types = {
      'class': function () {
        let classList = dom.get(elm, 'class');
        let requiredClasses = name.split(' ');

        checkRequiredClass: for (let requiredClassIndex = 0; requiredClassIndex < requiredClasses.length; requiredClassIndex++) {
          let requiredClassName = requiredClasses[requiredClassIndex];

          for (let classListIndex = 0; classListIndex < classList.length; classListIndex++) {
            let classListName = classList[classListIndex];

            if (classListName == requiredClassName) {
              continue checkRequiredClass;
            }
          }

          return false;
        }

        return true;
      },
      'tag': function () {
        return dom.get(elm, 'tag') == name;
      },
      'attr': function () {
        if (value === null) {
          return dom.get(elm, 'attr', name) !== false
        }
        else {
          return dom.get(elm, 'attr', name) == value
        }
      }
    };

    return dom.try('domHas', elm, type, name, function () {
      const result = types[type]();

      if (!checkParent) {
        return result;
      }
      else {
        if (result) {
          return elm;
        }
        else {
          const parentResult = dom.find.parent(elm, type, name, value);
  
          if (parentResult) {
            return parentResult;
          }
        }
        
        return false;
      }
    });
  }, 
  /** 
   * Retrieve an element or group of elements
   */
  find: {
    /**
     * Retrieve a variable number of parent elements.
     * 
     * @param {Element} elm The element acting as the base of the search.
     * @param {"class"|"tag"|"attr"|"group"} type The type of criteria to search by.
     * @param {string|"inputs"|"clickables"|"focusables"} name A *class*, *tagname*, *attribute*, or *group type* to search for.
     * - If `type` is set to **"class"**, you can specify multiple classes to match as a space-separated list.
     * - If `type` is set to **"group"**, the following options are available:
     * - - **inputs** will match all `input`, `select`, and `textarea` elements.
     * - - **clickables** will match all `a` and `button` elements.
     * - - **focusables** will match all `input`, `select`, `textarea`, `a`, and `button` elements.
     * @param {string} value An *attribute value* or *group viability status* to search for:
     * - If `type` is set to **attr**, this is the value the attribute must be set to in order to be matched. 
     * - If `type` is set to **attr** and this parameter is omitted, all elements with the provided attribute will be matched. 
     * - If `type` is set to **group**, passing **true** will require elements to be *visible* and *enabled* to be matched.
     * @returns {array|false} Returns an array of matches. The array will be empty if no results were found. Returns **false** if an error occurred.
     */
    parents: function (elm, type, name, value = null) {
      return dom.try('domFindParents', elm, type, name, function () {
        let element = elm.parentNode;
        let results = [];
        let groupTypes = {};
            groupTypes.inputs = [ 'input', 'select', 'textarea' ];
            groupTypes.clickables = [ 'a', 'button' ];
            groupTypes.focusables = groupTypes.inputs.concat(groupTypes.clickables);

        while (element) {
          if (typeof element.getElementsByTagName == 'undefined') {
            break;
          }

          const isMatch = type != 'group' 
                            && dom.has(element, type, name, value) 
                          || type == 'group' 
                            && (groupTypes[name].indexOf(dom.get(element, 'tag')) != -1
                              && !element.hidden
                              && !dom.get(element, 'attr', 'disabled'));

          if (isMatch) {
            results.push(element);
          }
          else if (dom.has(element, 'tag', 'body') || dom.has(element, 'tag', 'html')) {
            break;
          }

          element = element.parentNode;
        }

        return results;
      }, [ 'class', 'tag', 'attr', 'group' ]);
    },
    /**
     * Retrieve a specific parent element
     * 
     * @param {Element} elm The element acting as the base of the search.
     * @param {"class"|"tag"|"attr"|"group"} type The type of criteria to search by.
     * @param {string|"inputs"|"clickables"|"focusables"} name A *class*, *tagname*, *attribute*, or *group type* to search for.
     * - If `type` is set to **"class"**, you can specify multiple classes to match as a space-separated list.
     * - If `type` is set to **"group"**, the following options are available:
     * - - **inputs** will match any `input`, `select`, or `textarea` element.
     * - - **clickables** will match any `a` or `button` element.
     * - - **focusables** will match any `input`, `select`, `textarea`, `a`, or `button` element.
     * @param {string} value If `type` is set to **attr**, this is the value the attribute must be set to in order to be matched. If `type` is set to **attr** and this parameter is omitted, any element with the provided attribute will be matched. Otherwise, this parameter has no effect.
     * @returns {Element|false} On success, returns the matched element. Returns **false** if no matches were found or if an error occurred.
     */
    parent: function (elm, type, name, value) {
      return dom.try('domFindParent', elm, type, name, function () {
        let result = dom.find.parents(elm, type, name, value);

        if (result.length > 0) { return result[0]; }
        else                   { return false; }
      }, [ 'class', 'tag', 'attr', 'group' ]);
    },
    /**
     * Retrieve a variable number of child elements.
     * 
     * @param {Element} elm The element acting as the base of the search.
     * @param {"class"|"tag"|"attr"|"group"} type The type of criteria to search by.
     * @param {string|"inputs"|"clickables"|"focusables"} name A *class*, *tagname*, *attribute*, or *group type* to search for.
     * - If `type` is set to **"class"**, you can specify multiple classes to match as a space-separated list.
     * - If `type` is set to **"group"**, the following options are available:
     * - - **inputs** will match all `input`, `select`, and `textarea` elements.
     * - - **clickables** will match all `a` and `button` elements.
     * - - **focusables** will match all `input`, `select`, `textarea`, `a`, and `button` elements.
     * @param {string} value An *attribute value* or *group viability status* to search for:
     * - If `type` is set to **attr**, this is the value the attribute must be set to in order to be matched. 
     * - If `type` is set to **attr** and this parameter is omitted, all elements with the provided attribute will be matched. 
     * - If `type` is set to **group**, passing **true** will require elements to be *visible* and *enabled* to be matched.
     * - Otherwise, this parameter has no effect.
     * @returns {array|false} Returns an array of matches. The array will be empty if no results were found. Returns **false** if an error occurred.
     */
    children: function (elm, type, name, value = null) {
      let types = {
        class: function () {
          return elm.getElementsByClassName(name);
        },
        tag: function () {
          return elm.getElementsByTagName(name);
        },
        attr: function () {
          let elements = elm.getElementsByTagName('*');
          let results = [];

          for (let element of elements) {
            if (dom.has(element, 'attr', name, value)) {
              results.push(element);
            }
          }

          return results;
        },
        group: function () {
          let names = {};
              names.inputs = ['input', 'select', 'textarea'];
              names.clickables = ['a', 'button'];
              names.focusables = names.inputs.concat(names.clickables);

          let elements = elm.getElementsByTagName('*');
          let results = [];
          let valid = names[name];

          if (valid) {
            for (let element of elements) {
              if (valid.indexOf(dom.get(element, 'tag')) != -1) {
                if (value != true || (!element.hidden && !dom.get(element, 'attr', 'disabled'))) {
                  results.push(element);
                }
              }
            }

            return results;
          }
          else {
            console.error(`domFindChildren Error: Argument 4 is not a valid name: ${name}`);
            return false;
          }
        }
      };

      return dom.try('domFindChildren', elm, type, name, function () {
        let results = types[type]();

        return results;
      }, [ 'class', 'tag', 'attr', 'group' ]);
    },
    /**
     * Retrieve a specific child element
     * 
     * @param {Element} elm The element acting as the base of the search.
     * @param {"class"|"tag"|"attr"|"group"} type The type of criteria to search by.
     * @param {string|"inputs"|"clickables"|"focusables"} name A *class*, *tagname*, *attribute*, or *group type* to search for.
     * - If `type` is set to **"class"**, you can specify multiple classes to match as a space-separated list.
     * - If `type` is set to **"group"**, the following options are available:
     * - - **inputs** will match any `input`, `select`, or `textarea` element.
     * - - **clickables** will match any `a` or `button` element.
     * - - **focusables** will match any `input`, `select`, `textarea`, `a`, or `button` element.
     * @param {string} value If `type` is set to **attr**, this is the value the attribute must be set to in order to be matched. If `type` is set to **attr** and this parameter is omitted, any element with the provided attribute will be matched. Otherwise, this parameter has no effect.
     * @returns {Element|false} On success, returns the matched element. Returns **false** if no matches were found or if an error occurred.
     */
    child: function (elm, type, name, value = null) {
      return dom.try('domFindChild', elm, type, name, function () {
        let result = dom.find.children(elm, type, name, value);

        if (result.length > 0) { return result[0]; }
        else                   { return false; }
      }, [ 'class', 'tag', 'attr', 'group' ]);
    },
    /**
     * Retrieve a specific element using its unique ID
     * 
     * @param {string} id The id of the element.
     * @returns {Element|false} On success, returns the matched element. Returns **false** if the element could not be found or if an error occurred.
     */
    id: function (id) {
      let e = document.getElementById(id);

      if (e !== null) { return e; }
      else            { return false; }
    }
  }
};
/** Methods for editing HTML Elements */
const edit = {
  try: function (options) {
    let defaultOptions = {
      func: '',
      callback: function () {
        console.warning('No callback was passed to editTry.');
      },
      elm: false,
      type: '',
      name: '',
      val: '',
      validTypes: ['add', 'remove']
    };
    let o = mergeObj(defaultOptions, options);
    let checks = [
      o.elm !== undefined && o.elm,
      o.validTypes.indexOf(o.type) != -1,
      typeof o.name == 'string' || typeof o.name == 'number',
      typeof o.val == 'string' || typeof o.val == 'number' || typeof o.val == 'boolean'
    ];

    try {
      globalFuncStats.record(o.func);

      if (checks.indexOf(false) == -1) {
        let callbackResult = o.callback();

        globalFuncStats.record(o.func, true, callbackResult);

        return callbackResult;
      }
      else {
        globalFuncStats.record(o.func, false);

        if (!checks[0]) {
          throw `Invalid element: ${o.elm}`;
        }
        else if (!checks[1]) {
          throw `Invalid type: ${o.type}`;
        }
        else if (!checks[2]) {
          throw `Invalid class or attribute: ${typeof o.name}`;
        }
        else if (!checks[3]) {
          throw `Invalid class name or attribute value: ${typeof o.val}`;
        }
      }
    }
    catch (error) {
      console.error(`${o.func} Error: ${error}`);
      return false;
    }
  },
  /**
   * Add or Remove a class from a given element
   * 
   * @param {Element} elm The element to be updated.
   * @param {"add"|"remove"|"toggle"} type Indicates if the provided class is to be *added* or *removed* from the element. The keyword **toggle** is used to toggle the prescence of a particular class.
   * @param {string} name The name of the class to be added or removed. Multiple classes can be added or removed at once by separating them with a *space*. 
   * - *Note: Removing a non-existent class **will not** throw an error.*
   * @returns {boolean} Returns **true** on success, or **false** if an error occurred.
   */
  class: function (elm, type, name) {
    return edit.try({
      'func': 'editClass',
      'elm': elm,
      'type': type,
      'name': name,
      'validTypes': [ 'add', 'remove', 'toggle' ],
      'callback': function () {
        let classes = name.split(' ');

        for (let className of classes) {
          let action = type;

          if (action == 'toggle') {
            action = !dom.has(elm, 'class', className) ? 'add' : 'remove';
          }

          dom.get(elm, 'class')[action](className);
        }

        return true;
      }
    });
  },
  /**
   * Add, Update, or Remove an attribute from a given element
   * 
   * @param {Element} elm The element to be updated.
   * @param {"add"|"update"|"remove"|"toggle"|"list"} type Indicates how the element is to be updated.
   * - **add**, **update**: Add the attribute to the element or update the existing value. (`element.setAttribute()`)
   * - **remove**: Remove the attribute from the element. (`element.removeAttribute()`)
   * - **toggle**: Adds or Removes the attribute depending on its current state.
   * - **list**: Adds, updates, or removes an attribute with a comma-separated list as the value.
   * @param {string} name The name of the attribute to be added or removed. Multiple classes can be added or removed at once by separating them with a *space*. 
   * - *Note: Removing a non-existent attribute **will not** throw an error.*
   * @param {string} val The value of the attribute to be set. This value does not need to be set for boolean attributes, such as *disabled*, and has no effect if `type` is set to **remove**.
   * @returns {boolean} Returns **true** on success, or **false** if an error occurred.
   */
  attr: function (elm, type, name, val = '') {
    return edit.try({
      'func': 'editAttr',
      'elm': elm,
      'type': type,
      'name': name,
      'val': val,
      'validTypes': [ 'add', 'update', 'remove', 'toggle', 'list' ],
      'callback': function () {
        const existingAttr = dom.get(elm, 'attr', name);

        if (type == 'toggle') {
          type = existingAttr === false ? 'add' : 'remove';
        }

        if (type == 'add' || type == 'update') {
          elm.setAttribute(name, val);
        }
        else if (type == 'remove') {
          elm.removeAttribute(name);
        }
        else if (type == 'list') {
          // Add new attribute
          if (!existingAttr) {
            edit.attr(elm, 'add', name, val);
          }
          else {
            // Add new value
            if (existingAttr.indexOf(val) == -1) {
              edit.attr(elm, 'update', name, `${existingAttr}, ${val}`);
            }
            // Remove value
            else {
              const values = existingAttr.split(', ');

              values.splice(values.indexOf(val), 1);

              if (values.length > 0) {
                edit.attr(elm, 'update', name, values.join(', '));
              }
              else {
                edit.attr(elm, 'remove', name);
              }
            }
          }
        }

        return true;
      }
    });
  },
  /**
   * Clone a given HTML Element
   * - *Note: A **deep clone** is performed, meaning the entire subtree of the node is cloned.*
   * 
   * @param {Element} elm The element to be cloned.
   * @param {boolean} deepClone Indicates if a *deep clone* is to be performed.
   * - When set to **true**, the `node` and its entire subtree, including `text` nodes, are all cloned.
   * - When set to **false**, only the `node` will be cloned.
   * @returns {Element|false} Returns the cloned element on success, or **false** if an error occurred.
   */
  copy: function (elm, deepClone = true) {
    return edit.try({
      'func': 'editCopy',
      'elm': elm,
      'type': 'add',
      'callback': function () {
        let element;

        if (dom.get(elm, 'tag') == 'template') {
          element = elm.content.children[0];
        }
        else {
          element = elm;
        }

        return element.cloneNode(deepClone);
      }
    });
  }
};

// Class & Attribute manipulation
/**
 * Retrieve the value of a HTML meta tag
 * 
 * @param {string} name The name of the tag to find and retrieve
 * @returns {Element|boolean} Returns the meta tag element on success. Returns false if the meta tag could not be found.
 */
function getMetaTag (name) {
  let meta = dom.find.child(document.head, 'attr', 'name', name);

  return meta ? meta : false;
}
// Element manipulation
/**
 * Create an HTMLElement from an HTML string
 * 
 * @param {string} html A string of html to build the element from
 * @return {object} Returns an HTMLElement interface to manipulate
 */
function createElementFromHTML (html) {
  let div = document.createElement('div');
      div.innerHTML = html;

  return div.firstChild;
}
/**
 * Remove an element from the DOM
 * 
 * @param {Element} element The element to be deleted.
 * @returns {Element|false} Returns the *deleted element* on success, and **false** on failure.
 */
function deleteElement (element) {
  try {
    if (!element) {
      throw 'A valid element to delete must be provided.';
    }
    else if (!element.parentNode) {
      throw 'Top-Level Nodes cannot be deleted.';
    }

    return element.parentNode.removeChild(element);
  }
  catch (error) {
    console.error(`deleteElement Error: ${error}`);
    return false;
  }
}

function setElementState (affectedState, element, state, setTabIndex) {
  let validStates = ['disabled', 'hidden'];

  function error (message) {
    let error = new Error();
        error.name = 'setElementStateError';
        error.message = message;

    throw error;
  }

  if (validStates.indexOf(affectedState) != -1) {
    if (element) {
      let hasProp = element[affectedState];

      // Toggle state
      if (state == 'toggle') {
        if (hasProp) { state = !element[affectedState]; }
        else         { state = !dom.has(element, 'attr', affectedState); }
      }
      // Update element
      // if (hasProp) {
      //   element[affectedState] = state;
      //   // edit.attr(element, 'remove', `aria-${affectedState}`);
      // }
      // else {
        if (state) { edit.attr(element, 'add', affectedState, ''); }
        else       { edit.attr(element, 'remove', affectedState); }

        edit.attr(element, 'add', `aria-${affectedState}`, state);
      // }
      // Tabindex
      if (setTabIndex) {
        let indexes = {
          true: -1,
          false: 0
        };

        element.tabIndex = indexes[state];
      }

      return true;
    }
    else {
      error(`Provided element is ${element}.`);
    }
  }
  else {
    error(`${affectedState} is not a valid state.`);
  }
}
function isDisabled (element, state = 'toggle', setTabIndex = false) {
  return setElementState('disabled', element, state, setTabIndex);
}
function isHidden (element, state = 'toggle', setTabIndex = false) {
  return setElementState('hidden', element, state, setTabIndex);
}
/**
 * Update the label of an element
 * 
 * @param {Element} element The element to update. 
 * @param {string} label The new label.
 * @param {array} pieces An array of label pieces to be added to the element.
 * - Valid pieces: `title`, `aria`, `tooltip`
 * - If omitted, only the `title` and `aria` pieces will be added.
 * @returns {boolean} Returns **true** on success, or **false** if an error occurred.
 */
function updateLabel (element, label, pieces = [ 'title', 'aria' ]) {
  let components = {
    title () {
      element.title = label;
    },
    aria () {
      edit.attr(element, 'update', 'aria-label', 'label');
    },
    tooltip () {
      let tooltip = (function () {
        let attrName = 'data-layer-target';
        let tooltipAttr = dom.get(element, 'attr', attrName);
        
        if (tooltipAttr) {
          let attrSearch = dom.find.id(tooltipAttr);

          if (attrSearch) {
            return attrSearch;
          }
        }

        if (element.id) {
          let targetAttrSearch = dom.find.child(document.body, 'attr', attrName, element.id);
    
          if (targetAttrSearch) {
            return targetAttrSearch;
          }
        }

        for (let sibling of [element.nextElementSibling, element.previousElementSibling]) {
          if (sibling && dom.has(sibling, 'class', 'tooltip')) {
            return sibling;
          }
        }

        let classSearch = element.parentNode
                          ? dom.find.child(element.parentNode, 'class', 'tooltip')
                          : false;

        if (classSearch) {
          return classSearch;
        }
  
        return false;
      })();

      // Create new tooltip
      if (!tooltip) {
        (function () {
          let newTooltip = (function () {
            let newTooltip = document.createElement('div');
  
            edit.class(newTooltip, 'add', 'layer tooltip');
            edit.attr(newTooltip, 'add', 'data-layer-delay', 'medium');

            return newTooltip;
          })();
          
          tooltip = element.insertAdjacentElement('afterend', newTooltip);
          edit.class(element, 'add', 'layer-target');
          ShiftCodesTK.layers.setupLayer(tooltip);
        })();
      }
      
      dom.find.child(tooltip, 'class', 'content-container').innerHTML = label;
    }
  };

  for (component in components) {
    if (pieces.indexOf(component) != -1) {
      components[component]();
    }
  }

  return true;
}
function updateProgressBar (progressBar = null, value = 100, options = {}) {
  let defaultOptions = {
    interval: false,
    intervalDelay: 1000,
    intervalIncrement: 5,
    start: null,
    resetOnZero: false,
    useWidth: false
  };
  
  if (progressBar !== null && dom.get(progressBar, 'attr', 'role') == 'progressbar') {
    let bar = dom.find.child(progressBar, 'class', 'progress');
    let count = dom.find.child(progressBar, 'class', 'progress-count');
    let opt = mergeObj(defaultOptions, options);
    let id = progressBar.id;

    if (!opt.resetOnZero || value > 0) {
      // Update Progress Bar
      function change (newVal = value) {
        progressBar.setAttribute('data-progress', newVal);
        progressBar.setAttribute('aria-valuenow', newVal);

        // if (!opt.useWidth) {
        //   bar.style.transform = `translateX(${newVal}%)`;
        // }
        // else {
        //   bar.style.width = `${newVal}%`;
        // }
        bar.style.width = `${newVal}%`;

        if (count) {
          let offset = 100 - newVal;
          let regex = new RegExp('\\d{1,3}');

          count.style.transform = `translateX(${offset}%)`;
          updateLabel(count, count.title.replace(regex, newVal));
          count.innerHTML = count.innerHTML.replace(regex, newVal);
        }
      }

      if (pbIntervals[id]) {
        clearInterval(pbIntervals[id].interval);
      }

      // Immediate Change
      if (opt.interval === false) {
        change();
      }
      // Interval Change
      else {
        let now = tryParseInt(progressBar.getAttribute('data-progress'), 'ignore');

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
          let now = tryParseInt(progressBar.getAttribute('data-progress'), 'throw');
          let nextVal = now + pbIntervals[id].increment;
          let end = pbIntervals[id].end;

          if (nextVal <= end) {
            change(nextVal);
          }
          else {
            change(end);
            clearInterval(pbIntervals[id].interval);
            delete pbIntervals[id];
          }
        }, opt.intervalDelay);
      }
    }
    // Reset Progress Bar
    else {
      progressBar.setAttribute('data-progress', 0);
      bar.style.removeProperty('width');

      if (count) {
        count.style.removeProperty('transform');
      }
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
// String Manipulation
/**
 * Encodes an html string by converting reserved HTML Characters into their HTML Entity Equivalents.
 * 
 * @param {string} html The HTML String to encode.
 * @returns {string} Returns the encoded HTML string.
 */
function encodeReservedHTML (html) {
  let encoded = html;

  for (let character in RESERVED_HTML_CHARACTERS) {
    let entity = RESERVED_HTML_CHARACTERS[character];

    encoded = encoded.replace(new RegExp(character, 'g'), entity);
  }

  return encoded;
}
/**
 * Decodes an html string by converting reserved HTML Characters from their HTML Entity Equivalents.
 * 
 * @param {string} html The HTML String to decode.
 * @returns {string} Returns the decoded HTML string.
 */
function decodeReservedHTML (html) {
  let decoded = html;

  for (let character in RESERVED_HTML_CHARACTERS) {
    let entity = RESERVED_HTML_CHARACTERS[character];

    decoded = decoded.replace(new RegExp(entity, 'g'), character);
  }

  return decoded;
}
function deleteElement (elm) {
  if (elm) {
    return elm.parentNode.removeChild(elm);
  }
  else {
    let error = new Error();
        error.name = 'deleteElement Error';
        error.message = `Argument 1 is not a valid element: ${elm}`;

    console.error(error);
    return false;
  }
}
// Cookies
var cookie = {
  get: function (cookie = false) {
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
  
    if (!cookie) { return get('[^\\s=]+'); }
    else         { return get(cookie); }
  },
  set: function (setProps = {}) {
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
  },
  remove: function (cookie, immediately = true) {
    function del (val) {
      setCookie({
        'name': cookie,
        'max-age': val,
        'expires': val
      });
    }
  
    if (immediately) { del(0); }
    else             { del(false); }
  }
};
// Misc.
/**
 * Returns a random value between two values
 *
 * @param {number} min The minimum value to be generated
 * @param {number} max The maximum value to be generated
 * @returns {number} Returns a random number between the values of min and max
 */
function randomNum (min, max) {
  return Math.round(Math.random() * (max - min) + min);
}
/**
 * Generate a random ID
 * 
 * @param {string} prefix The prefix of the ID
 * @param {number} min The minimum numerical value of the ID
 * @param {number} max The maximum numerical value of the ID
 * @returns {string|false} Returns the *new ID* on success, or **false** if a suitable ID could not be generated.
 */
function randomID (prefix = "", min = 100, max = 9999) {
  let maxAttempts = 20;
  let idInt = 0;
  let idStr = "";

  for (let i = 1; i <= maxAttempts; i++) {
    idInt = randomNum(min, max);
    idStr = `${prefix}${idInt}`;
  
    if (!dom.find.id(idStr)) {
      return idStr;
    }
  }

  return false;
}
/**
 * Determines if a plural letter is needed based on a value
 *
 * @param {number} val The value to be evaluated
 * @param {string} [letter='s'] The letter to be returned if a plural is needed
 * @returns {string} Returns the specified letter if val is 1 or an empty string if number is any other value
 */
function checkPlural (val, letter = 's') {
  if (val != 1) { return 's'; }
  else          { return ''; }
}
/**
 * Convert the first letter of each word in a string to uppercase.
 * 
 * @param {string} string The string to convert to uppercase.
 * @returns {string} Returns the converted string/ 
 */
function ucWords (string) {
  let pieces = string.split(' ');

  for (let i = 0; i < pieces.length; i++) {
    let piece = pieces[i];

    pieces[i] = piece.charAt(0).toUpperCase() + piece.substring(1);
  }

  return pieces.join(' ');
}
/**
 * Retrieve the query parameters from a query string
 * 
 * @param {string} queryString The query string to parse. If omitted, the current URL will be used.
 * @returns {object} Returns an object made up of the query parameters.
 */
function getQueryParameters (queryString = window.location.search) {
  let parameters = {};

  if (queryString.indexOf('?') == 0) {
    queryString = queryString.slice(1);
  }

  for (let parameter of queryString.split('&')) {
    let isArray = false;
    const pieces = (function () {
      const slices = parameter.split('=');
      let pieces = {
        key: slices[0],
        value: decodeURIComponent(slices[1])
      };

      if (pieces.key.indexOf('[]') == pieces.key.length - 2) {
        isArray = true;
        pieces.key = pieces.key.slice(0, -2);
      }

      return pieces;
    })();

    if (isArray) {
      if (!parameters[pieces.key]) {
        parameters[pieces.key] = [];
      }

      parameters[pieces.key].push(pieces.value);
    }
    else {
      parameters[pieces.key] = pieces.value;
    }
  }

  return parameters;
}