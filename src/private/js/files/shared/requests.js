// Asynchronous request scripts
ShiftCodesTK.requests = {};
ShiftCodesTK.requests.isLoaded = false;

(function () {
  const interval = setInterval(() => {
    if (typeof globalFunctionsReady != 'undefined') {
      clearInterval(interval);

      /** The requests object */
      ShiftCodesTK.requests = {
        /** @property Indicates if the `requests` module has been completely loaded. */
        isLoaded: true,
        /** Default properties used for outgoing requests. See `request()` for more information on these parameters. */
        DEFAULT_REQUEST_PROPERTIES: {
          path: null,
          type: 'GET',
          params: {},
          callback: null,
          parseResultJSON: true,
          headers: {}
        },
        /** Manages active requests */
        activeRequests: {
          /**
           * A list of active requests
           * - Requests are indexed by their *Request ID*.
           * - Requests are removed from this list once they have been completed.
           */
          requestList: {},
          /**
           * Retrieve an *Active Request*.
           *
           * @param {string} requestID The *Request ID* of the *Active Request* to retrieve.
           * @param {boolean} throwMissingRequestError Indicates if an error should be thrown if the Active Request was not found.
           * @returns {object|false} Returns the *Active Request `Object`* on success, or **false** if the Active Request was not found.
           */
          getRequest (requestID, throwMissingRequestError = false) {
            const requestObject = this.requestList[requestID];

            if (requestObject === undefined) {
              if (throwMissingRequestError) {
                throw `Request "${requestID}" is not currently active.`;
              }

              return false;
            }

            return requestObject;
          },
          /**
           * Add an *Active Request*.
           *
           * @param {string} requestID The *Request ID* of the *Active Request* to add.
           * @param {boolean} request The *Active Request `Object`* to be added.
           * @returns {object|false} Returns the updated *Active Requests `Object`* on success, or **false** on failure.
           */
          addRequest (requestID, request) {
            try {
              const requestList = this.requestList;

              if (requestList[requestID] !== undefined) {
                throw `Request "${requestID}" is already active.`;
              }
              if (request === undefined) {
                throw `No Request Object was provided for Request "${requestID}".`;
              }

              requestList[requestID] = request;

              return requestList;
            }
            catch (error) {
              console.error(`requests.${arguments.callee.name} Error: ${error}`);
              return false;
            }
          },
          /**
           * Remove an *Active Request*.
           *
           * @param {string} requestID The *Request ID* of the *Active Request* to remove.
           * @param {boolean} request The *Active Request `Object`* to be removed.
           * @returns {object|false} Returns the updated *Active Requests `Object`* on success, or **false** on failure.
           */
          removeRequest (requestID) {
            try {
              const requestList = this.requestList;

              if (requestList[requestID] === undefined) {
                throw `Request "${requestID}" is not active.`;
              }

              delete requestList[requestID];

              return requestList;
            }
            catch (error) {
              console.error(`requests.${arguments.callee.name} Error: ${error}`);
              return false;
            }
          },
          /**
           * Clear all active requests
           *
           * @returns {int} Returns the number of *Active Requests* that were removed.
           */
          clearAllRequests () {
            const requestCount = Object.keys(this.requestList).length;

            for (let requestID in this.requestList) {
              this.removeRequest(requestID);
            }

            return requestCount;
          }
        },
        /** Allows you to save request Properties, Parameters, & Results for re-use */
        savedRequests: {
          /**
           * A list of saved *Request Configurations* & *Request Parameters*
           * - Saved Requests are indexed by their *Request Name*.
           */
          requestList: {},
          /**
           * Save a request so that it can be used again in the future.
           *
           * @param {string} name The name of the request, used to retrieve the request in the future.
           * @param {object} configuration The *Request Configuration `Object`*.
           * - *type* `"standard"|"pagination"` - Indicates the type of request that will be performed:
                   * - _"standard"_ - Performs a standard request. No additional functionality is enabled.
                   * - _"pagination"_ - Performs a *Pagination Request*, enabling the following functionality:
                   * - - *property* `syncParameters`
                   * - - *property* `controls.sortAndFilter`
                   * - - *property* `controls.pager`
                   * - - *property* `controls.controlsState`
                   * - - *property* `controls.syncControls`
                   * - - *property* `parameters.limit`
                   * - - *property* `parameters.page`
                   * - - *property* `results.currentPage`
                   * - - *property* `results.totalPages`
                   * - - *property* `results.totalItems`
                   * - - *method* `previousPage()`
                   * - - *method* `nextPage()`
                   * - - *method* `firstPage()`
                   * - - *method* `lastPage()`
                   * - - *method* `toPage()`
           * - *request* `object` - Properties used for outgoing request. See `request()` for more details.
           * - - Note that the `params` property of this object can be used to set request parameters that will be provided with every request.
           * - - - These parameters are not `Request Parameters`, and cannot be retrieved or changed using methods such as `setRequestParameter()` or `getAllRequestParameters()`. See the `parameters` property of the main `Saved Request Object` for dynamic parameters.
           * - - - These parameters will overwrite those of the same name that were provided with the request.
           * - *syncParameters* `boolean` - Indicates if and how the *Request Parameters* should be synced with the *Query Parameters*. Requires `type` to be set to **"pagination"**. 
           * - *State String* - A `string` indicating how the *Request Parameters* should be synchronized to the *Query Parameters*.
           * - - This can be any value permitted by the `method` argument of `updateQueryParameters()`.
           * - **false** - Indicates that the *Request Parameters* will not be automatically synchronized with the *Query Parameters*. If `syncParameters` is omitted, defaults to this value.
           * - *controls* `object` - `Elements` that are controlled by the request. Requires `type` to be set to **"pagination"**.
           * - - *sortAndFilter* `HTMLFormElement|false` - The *form* used to sort and filter the request properties.
           * - - *pager* `Element|false` - The *pager* used to navigate through the request results.
           * - - *controlsState* `Array` - A list of `Elements` that can be toggled by the request.
           * - - *syncControls* `boolean` - Indicates if the *Provided Controls* are to be automatically synced with the *Request Parameters*. Requires `type` to be set to **"pagination"**.
           * @param {object} parameters The *Request Confirmation Parameters* used in submitting the request.
           * @param {array} readOnlyParameters A list of `parameters` that cannot be modified once they have been saved with the request.
           * @returns {object|false} Returns the new *Saved Request `Object`* on success, or **false** on failure.
           */
          saveRequest (name, configuration, parameters = {}, readOnlyParameters = []) {
            const funcName = arguments.callee.name;
            const requestsObj = ShiftCodesTK.requests;

            try {
              const savedRequests = this.requestList;

              if (savedRequests[name] !== undefined) {
                throw `Request "${name}" has already been saved.`;
              }
              if (configuration === undefined) {
                throw 'No Request Configuration Object was provided.';
              }

              /** The *Saved Request `Object`* */
              savedRequests[name] = {
                /** Properties used for setup and request configuration */
                configuration: {
                  /**
                   * Indicates the type of request that will be performed:
                   * - *"standard"* - Performs a standard request. No additional functionality is enabled.
                   * - *"pagination"* - Performs a *Pagination Request*, enabling the following functionality:
                   * - - *property* `controls.sortAndFilter`
                   * - - *property* `controls.pager`
                   * - - *property* `controls.controlsState`
                   * - - *property* `parameters.limit`
                   * - - *property* `parameters.page`
                   * - - *property* `results.currentPage`
                   * - - *property* `results.totalPages`
                   * - - *property* `results.totalItems`
                   * - - *method* `previousPage()`
                   * - - *method* `nextPage()`
                   * - - *method* `firstPage()`
                   * - - *method* `lastPage()`
                   * - - *method* `toPage()`
                   */
                  type: 'standard',
                  /** 
                   * Properties used for outgoing request. See `request()` for more details.
                   * - Note that the `params` property of this object can be used to set request parameters that will be provided with every request.
                   * - - These parameters are not `Request Parameters`, and cannot be retrieved or changed using methods such as `setRequestParameter()` or `getAllRequestParameters()`. See the `parameters` property of the main `Saved Request Object` for dynamic parameters.
                   * - - These parameters will overwrite those of the same name that were provided with the request.
                   **/
                  request: (function () {
                    let properties = {};

                    for (property of Object.keys(requestsObj.DEFAULT_REQUEST_PROPERTIES)) {
                      properties[property] = null;
                    }

                    return properties;
                  })()
                },
                /** The list of *Saved Request Parameters* */
                parameters: {},
                /** Properties related to the results of the request. */
                results: {
                  /**
                   * The last result of the request. If a request has not been performed yet, returns **null**.
                   * - *state `boolean`* - The *state* of the last request, indicating if the request was successful or not.
                   * - *response `object|string|false|null`* - The previous response `Object` or `string`, depending on the value of `parseJSONResponse`.
                   **/
                  lastResult: null,
                },
                /** The active *Request `Object`*, or **false** if the request is not currently being performed. */
                activeRequest: false
              };
              
              if (configuration.type == 'pagination') {
                savedRequests[name] = mergeObj(savedRequests[name], {
                  configuration: {
                    /** `Elements` that are controlled by the request. */
                    controls: {
                      /**
                       * The *form* used to sort and filter the request properties.
                       */
                      sortAndFilter: false,
                      /**
                       * The *pager* used to navigate through the request properties.
                       */
                      pager: false,
                      /**
                       * A list of `Elements` that can be toggled by the request.
                       */
                      controlsState: [],
                      /** Indicates if the *Provided Controls* are to be automatically synced with the *Request Parameters*. */
                      syncControls: true
                    },
                   /** 
                    * Indicates if and how the *Request Parameters* should be synced with the *Query Parameters*. 
                    * - *State String* - A `string` indicating how the *Request Parameters* should be synchronized to the *Query Parameters*.
                    * - - This can be any value permitted by the `method` argument of `updateQueryParameters()`.
                    * - **false** - Indicates that the *Request Parameters* will not be automatically synchronized with the *Query Parameters*. If `syncParameters` is omitted, defaults to this value.
                    **/
                   syncParameters: false,
                  },

                  parameters: {
                    /** The maximum number of results from the result set to be returned with each page. */
                    limit: 10,
                    /** The current page number in the result set. Starts at **1**. */
                    page: 1
                  },

                  results: {
                    /** The maximum size of the `ResultSetChunk`. If unspecified, returns the value of `totalChunkSize`. */
                    maxChunkSize: 0,
                    /** The size of the current `ResultSetChunk`. */
                    currentChunkSize: 0,
                    /** The total size of all `ResultSetChunks`. */
                    totalChunkSize: 0,
                    /** The current `ResultSetChunk` index. Starts at **1**. */
                    currentChunk: 1,
                    /** The total number of `ResultSetChunks`. */
                    totalChunks: 1,
                    /** Indicates if a previous `ResultSetChunk` is available. */
                    hasPreviousChunk: false,
                    /** Indicates if a following `ResultSetChunk` is available. */
                    hasNextChunk: false,
                    /** An `array` of items that are included in the current `ResultSetChunk`. */
                    chunkContents: []
                  }
                });
              }

              // Configuration
              (function () {
                const defaultConfiguration = {
                  request: (function () {
                    let properties = {};

                    for (property of Object.keys(requestsObj.DEFAULT_REQUEST_PROPERTIES)) {
                      properties[property] = null;
                    }

                    return properties;
                  })(),
                  sortAndFilter: false,
                  pager: false
                };
                let newConfiguration = mergeObj(savedRequests[name].configuration, configuration);

                if (newConfiguration.request.path === null) {
                  throw `A resource path was not provided for request "${name}".`;
                }
                if (newConfiguration.controls) {
                  if (newConfiguration.controls.sortAndFilter) {
                    const element = newConfiguration.controls.sortAndFilter;

                    if (!dom.has(element, 'tag', 'form')) {
                      console.warn(`requests.${funcName} Warning: The Provided Sorting & Filtering Form is not a valid form.`);
                      newConfiguration.controls.sortAndFilter = false;
                    }
                    else if (!dom.has(element, 'class', 'configured')) {
                      console.warn(`requests.${funcName} Warning: The Provided Sorting & Filtering Form has not been configured.`);
                      newConfiguration.controls.sortAndFilter = false;
                    }
                  }
                  if (newConfiguration.controls.pager) {
                    const element = newConfiguration.controls.pager;

                    (function () {
                      if (!dom.has(element, 'class', 'pager')) {
                        console.warn(`requests.${funcName} Warning: The Provided Pager is not a valid Pager.`);
                        newConfiguration.controls.pager = false;
                        return;
                      }
                      if (!dom.has(element, 'class', 'configured')) {
                        console.warn(`requests.${funcName} Warning: The Provided Pager has not been configured.`);
                        newConfiguration.controls.pager = false;
                        return;
                      }

                      addPagerListener(element, (page) => {
                        const requestsObj = ShiftCodesTK.requests;
                        const savedRequestsObj = requestsObj.savedRequests;
                        const requestList = savedRequestsObj.getAllRequests();

                        for (let requestName in requestList) {
                          let request = requestList[requestName];
  
                          if (request.configuration.controls) {
                            let pager = request.configuration.controls.pager;
  
                            if (pager) {
                              // shiftObj.setResultProp('page', page);
                              savedRequestsObj.setRequestParameter(requestName, 'page', page);

                              if (request.configuration.syncParameters) {
                                savedRequestsObj.syncQueryParams(requestName, 'query');
                              }
                              // shiftObj.syncQueryParams('query');
                              // shiftObj.fetchShiftCodes();
                              requestsObj.request(requestName);
                            }
                          }
                        }
                      });
                    })();

                  }
                }

                savedRequests[name].configuration = newConfiguration;
              })();
              // Parameters
              (function () {
                savedRequests[name].parameters = {};

                if (parameters !== undefined) {
                  for (let paramName in parameters) {
                    let param = parameters[paramName];

                    requestsObj.savedRequests.setRequestParameter(name, paramName, param, readOnlyParameters.indexOf(paramName) != -1);
                  }
                }
              })();

              if (configuration.type == 'pagination') {
                if (savedRequests[name].configuration.syncParameters) {
                  requestsObj.savedRequests.syncQueryParams(name, 'params');
                }
                if (savedRequests[name].configuration.controls.syncControls) {
                  requestsObj.savedRequests.syncControls(name);
                }
              }

              return savedRequests[name];
            }
            catch (error) {
              console.error(`requests.${funcName} Error: ${error}`);
              return false;
            }
          },
          /**
           * Retrieve a *Saved Request*.
           *
           * @param {string} requestName The name of the *Saved Request* to retrieve.
           * @param {boolean} throwMissingRequestError Indicates if an error should be thrown if the Saved Request was not found.
           * @returns {object|false} Returns the *Saved Request `Object`* on success, or **false** if the Saved Request was not found.
           */
          getRequest (requestName, throwMissingRequestError = false) {
            const requestData = this.requestList[requestName];

            if (requestData === undefined) {
              if (throwMissingRequestError) {
                throw `Request "${requestName}" has not been saved.`;
              }

              return false;
            }

            return requestData;
          },
          /**
           * Retrieve all *Saved Requests*.
           *
           * @returns {object} Returns an `Object` made up of all *Saved Requests*.
           */
          getAllRequests () {
            return this.requestList;
          },
          /**
           * Retrieve the value of a *Saved Request Parameter*
           *
           * @param {string} requestName The name of the *Saved Request* the parameter belongs to.
           * @param {string} parameterName The name of the *Request Parameter* to retrieve.
           * @param {"Default"|"DefaultValue"|"FullData"} returnValue Indicates what the *return value* of the function is:
           * - `Default` - The *Value* of the parameter is returned.
           * - `DefaultValue` - The *Original Value* of the parameter is returned.
           * - `FullData` - The full `Saved Request Parameter Data Object` of the parameter is returned.
           * @returns {any} Returns a value determined by value of `returnValue`. If an error occurs, returns **null**.
           */
          getRequestParameter (requestName, parameterName, returnValue = "Default") {
            try {
              const requestData = this.getRequest(requestName, true);
              const parameterList = requestData.parameters;

              if (Object.keys(parameterList).length == 0) {
                throw `No properties have been assigned to "${requestName}"`;
              }

              const parameterData = parameterList[parameterName];

              if (parameterData === undefined) {
                throw `Parameter "${parameterName}" has not been assigned to "${requestName}".`;
              }

              if (returnValue == 'Default') {
                return parameterData.value;
              }
              else if (returnValue == 'DefaultValue') {
                return parameterData.defaultValue;
              }
              else if (returnValue == 'FullData') {
                return parameterData;
              }

              throw `"${returnValue}" is not a valid value for the return value`;
            }
            catch (error) {
              console.error(`requests.${arguments.callee.name} Error: ${error}`);
              return null;
            }
          },
          /**
           * Retrieve the *Saved Request Parameters* for a given request.
           *
           * @param {string} requestName The name of the *Saved Request* the parameters belong to.
           * @param {"Default"|"DefaultValue"|"FullData"} returnValue Indicates what the *return value* of the function is:
           * - `Default` - The *Value* of the parameter is returned.
           * - `DefaultValue` - The *Original Value* of the parameter is returned.
           * - `FullData` - The full `Saved Request Parameter Data Object` of the parameter is returned.
           * @returns {object|false} Returns an `Object` made up of the return values determined by `returnValue`. If an error occurs, returns **null**.
           */
          getAllRequestParameters (requestName, returnValue = "Default") {
            try {
              const requestData = this.getRequest(requestName);

              if (requestData) {
                let parameters = {};
  
                if (returnValue != 'FullData') {
                  for (let parameterName in requestData.parameters) {
                    parameters[parameterName] = this.getRequestParameter(requestName, parameterName, returnValue);
                  }
                }
                else {
                  parameters = requestData.parameters;
                }
  
                return parameters;
              }

              return false;
            }
            catch (error) {
              console.error(`requests.${arguments.callee.name} Error: ${error}`);
              return false;
            }
          },
          /**
           * Add a new *Saved Request Parameter*
           *
           * @param {string} requestName The name of the *Saved Request* the parameter will belong to.
           * @param {string} parameterName The name of the *Request Parameter* to add.
           * @param {string} parameterValue The initial *Request Parameter Value*.
           * @param {boolean} isReadOnly Indicates if the parameter can be modified once it has been assigned to the *Saved Request*:
           * - Defaults to **false**
           * @returns {object|boolean} Returns the new *Saved Request Parameter `Object`* on success and **false** on failure.
           * 
           * * Update the value of a *Saved Request Parameter*
           *
           * @param {string} requestName The name of the *Saved Request* the parameter belongs to.
           * @param {string} parameterName The name of the *Request Parameter* to update.
           * @param {string} parameterValue The new *Request Parameter Value*.
           * @returns {object|boolean} Returns the new *Saved Request Parameter `Object`* on success and **false** on failure.
           * 
           * Add or Update the value of a *Saved Request Parameter*
           * 
           * @param {string} requestName The name of the *Saved Request* the parameter belongs to.
           * @param {string} parameterName The name of the *Request Parameter* to be added or updated.
           * - If `parameterName` matches a parameter assigned to the *Saved Request*, it will be updated.
           * - If `parameterName` does not match any parameters assigned to the *Saved Request*, it will be added.
           * @param {string} parameterValue The value of the *Request Parameter*.
           * @param {boolean} isReadOnly Indicates if the parameter can be modified once it has been assigned to the *Saved Request*.
           * - Only valid if the parameter is being *added* (see `parameterName`)
           * - Defaults to **false**
           * @returns {object|boolean} Returns the new *Saved Request Parameter `Object`* on success and **false** if an error occurred.
           */
          setRequestParameter (requestName, parameterName, parameterValue = null, isReadOnly = false) {
            const savedRequestsObj = ShiftCodesTK.requests.savedRequests;

            try {
              // const requestData = savedRequestsObj.getRequest(requestName, true);
              const parameterList = (function () {
                const parameterList = savedRequestsObj.getAllRequestParameters(requestName, 'FullData');

                if (parameterList) {
                  return parameterList;
                }
                else {
                  return {};
                }
              })();
              const operation = parameterList[parameterName] === undefined
                                ? 'add'
                                : 'update';

              if (operation == 'add') {
                parameterList[parameterName] = {
                  value: parameterValue,
                  defaultValue: parameterValue,
                  isReadOnly: isReadOnly
                }; 
              }
              else if (operation == 'update') {
                let parameterData = parameterList[parameterName];

                if (parameterData.isReadOnly) {
                  throw `Parameter "${parameterName}" is read-only.`;
                }

                parameterData.value = parameterValue;
              }

              return savedRequestsObj.getAllRequestParameters(requestName);
            }
            catch (error) {
              console.error(`requests.savedRequests.${arguments.callee.name} Error: ${error}`);
              return false;
            }
          },
          /**
           * Retrieve the *Request Data* for a *Saved Request*.
           *
           * @param {string} requestName The name of the *Saved Request* to retrieve.
           * @returns {object|false} Returns the *Saved Request Data `Object`* on success, or **false** if an error occurred.
           */
          getResultData (requestName) {
            try {
              const requestData = this.getRequest(requestName, true);
              const resultData = requestData.results;

              if (resultData === undefined) {
                throw `Request "${requestName}" has no result data.`;
              }

              return resultData;
            }
            catch (error) {
              console.error(`requests.savedRequests.${arguments.callee.name} Error: ${error}`);
              return false;
            }
          },
          /**
           * Synchronize any applicable controls with the Request Data.
           *
           * @param {string} requestName The name of the *Saved Request* to sync.
           * @returns {boolean} Returns **true** on success, or **false** on failure
           */
          syncControls (requestName) {
            const requestsObj = ShiftCodesTK.requests;
            const formsObject = ShiftCodesTK.forms;

            try {
              const savedRequest = this.getRequest(requestName, true);
              let controlsWereUpdated = false;

              if (savedRequest.configuration.type != 'pagination') {
                throw `Controls cannot be synced for ${ucWords(savedRequest.configuration.type)} Requests.`;
              }

              const controls = savedRequest.configuration.controls;

              if (!controls) {
                throw `No controls have been assigned to Request "${requestName}".`;
              }

              if (controls.sortAndFilter) {
                const parameters = requestsObj.savedRequests.getAllRequestParameters(requestName);

                for (let parameterName in parameters) {
                  let parameter = parameters[parameterName];
                  let field = formsObject.getField(controls.sortAndFilter, parameterName);

                  if (field) {
                    let updateResult = formsObject.updateField(
                      Array.isArray(field)
                        ? field[0]
                        : field,
                      parameter !== null
                        ? parameter
                        : '',
                      {
                        source: 'RequestControlsSync'
                      });

                      if (updateResult) {
                        controlsWereUpdated = true;
                      }
                  }
                }
              }
              if (controls.pager) {
                const savedRequestsObj = requestsObj.savedRequests;
                const resultData = savedRequestsObj.getResultData('FetchShiftCodes');
                const pagerStats = {
                  limit: resultData.maxChunkSize,
                  page: resultData.currentChunk,
                  total: resultData.totalChunks
                };

                if (Object.values(pagerStats).indexOf(null) != -1) {
                  throw `The Pager for "${requestName}" could not be updated. The value for "${Object.keys(pagerStats)[Object.values(pagerStats).indexOf(null)]}" was not found.`;
                }

                const pagerProps = {
                  now: pagerStats.page,
                  max: pagerStats.limit > 0
                       ? Math.ceil((pagerStats.total * pagerStats.limit) / pagerStats.limit)
                       : 1
                };
                
                console.log(controls.pager, pagerStats, pagerProps);

                const wasPagerUpdated = updatePagerProps(controls.pager, pagerProps);
                
                if (!controlsWereUpdated) {
                  controlsWereUpdated = wasPagerUpdated;
                }
              }

              if (!controlsWereUpdated) {
                throw `No controls have been assigned to Request "${requestName}".`;
              }

              return true;
            }
            catch (error) {
              console.error(`requests.savedRequests.${arguments.callee.name} Error: ${error}`);
              return false;
            }
          },
          /**
           * Sync the `Saved Request Data` to and from the *query parameters*
           *
           * @param {string} requestName The name of the *Saved Request* to sync with.
           * @param {"query"|"params"} syncTo Indicates which direction to sync to:
           * - **"query"**: Syncs the `Saved Request Data` to the *query parameters*.
           * - **"params"** Syncs the *query parameters* to the `Saved Request Data`.
           * @returns {object|false} Returns the *synced properties* on success, or **false** if an error occurred.
           */
          syncQueryParams (requestName, syncTo = 'query') {
            const shiftObject = this;
            const savedRequest = this.getRequest(requestName, true);
            const requestParameters = this.getAllRequestParameters(requestName, 'FullData');
            const setRequestParameter = this.setRequestParameter;
            let queryParameters = getQueryParameters();

            if (syncTo == 'query') {
              // The `Saved Request Data`
              for (let paramName in requestParameters) {
                let paramData = requestParameters[paramName];
                let canUpdateProp = (function () {
                  if (!paramData.isReadOnly) {
                    const hasSameValueAsDefault = (function () {
                      if (!Array.isArray(paramData.value)) {
                        return paramData.value === paramData.defaultValue;
                      }
                      else {
                        const hasSameArrays = (function () {
                          for (let item of paramData.value) {
                            if (paramData.defaultValue.indexOf(item) == -1) {
                              return false;
                            }
                          }
                          for (let item of paramData.defaultValue) {
                            if (paramData.value.indexOf(item) == -1) {
                              return false;
                            }
                          }

                          return true;
                        })();

                        return hasSameArrays;
                      }
                    })();

                    if (!hasSameValueAsDefault) {
                      return true
                    }
                  }

                  return false;
                })();

                if (canUpdateProp) {
                  queryParameters[paramName] = paramData.value;
                }
                else if (queryParameters[paramName] !== undefined) {
                  queryParameters[paramName] = null;
                }
              }

              updateQueryParameters(queryParameters, savedRequest.configuration.syncParameters);

              return queryParameters;
            }
            else if (syncTo == 'params') {
              for (let paramName in requestParameters) {
                let paramData = requestParameters[paramName];
                let queryValue = (function () {
                  const queryValue = queryParameters[paramName];
                  const intProps = [ 'limit', 'page' ];

                  if (queryValue && intProps.indexOf(paramName) != -1) {
                    return tryParseInt(queryValue);
                  }
                  else {
                    return queryValue;
                  }
                })();

                if (!paramData.isReadOnly) {
                  if (queryValue !== undefined && queryValue != paramData.value) {
                    // shiftObject.setResultProp(paramName, queryValue);
                    setRequestParameter('FetchShiftCodes', paramName, queryValue);
                  }
                  else if (queryValue === undefined) {
                    // shiftObject.setResultProp(paramName, shiftObject.props[paramName].defaultValue);
                    setRequestParameter('FetchShiftCodes', paramName, paramData.defaultValue);
                  }
                }
              }

              return shiftObject.props;
            }

            return false;
          }
        },
        /**
         * @property Properties and methods related to Custom Request Events
         * - See `eventList` for the list of form events and their provided properties.
         * - Use `dispatchRequestEvent()` to dispatch a form event.
         * - When fired, all request event names are prefixed with `tkRequests` _(Ex: tkRequestsRequestCompleted)_
         * - All events are fired on the `window`.
         * - All provided properties are found in the `requestEventData` object of the `Event`.
         **/
        requestEvents: {
          /** @property The prefix of all custom request events */
          prefix: 'tkRequests',
          /**
           * @property Custom Request Events and a list of their provided properties.
           * - Use `dispatchRequestEvent()` to dispatch a request event.
           * - When fired, all request event names are prefixed with `tkRequests` _(Ex: tkRequestsRequestCompleted)_
           * - All properties are found in the `requestEventData` object of the `Event`.
           **/
          eventList: {
            /**
             * `tkRequestsRequestDispatched`
             * Fired when a *request* is dispatched.
             * - Fires when the `XMLHttpRequest Event` **loadstart** is fired.
             * - Provides the following properties:
             * - - `request`
             * - - `originalEvent`
             * - - `timestamp`
             * - - `timestampStart`
             * - - `timeEnlapsed`
             */
            RequestDispatched: [
              'request',
              'originalEvent',
              'timestamp',
              'timestampStart',
              'timeEnlapsed'
            ],
            /**
             * `tkRequestsRequestLoading`
             * Fired when the *request* fires an event related to loading.
             * - Fires when the following `XMLHttpRequest Events` are fired:
             * - - `load`
             * - - `progress`
             * - Provides the following properties:
             * - - `request`
             * - - `originalEvent`
             * - - `timestamp`
             * - - `timestampStart`
             * - - `timeEnlapsed`,
             * - - `totalSize`
             * - - `downloadedSize`
             * - - `downloadedPercentage`
             */
            RequestLoading: [
              'request',
              'originalEvent',
              'timestamp',
              'timestampStart',
              'timeEnlapsed',
              'totalSize',
              'downloadedSize',
              'downloadedPercentage'
            ],
            /**
             * `tkRequestsRequestCompleted`
             * Fired when a *request* has successfully completed.
             * - Fires when the `XMLHttpRequest Event` **loadend** is fired.
             * - Provides the following properties:
             * - - `request`
             * - - `originalEvent`
             * - - `timestamp`
             * - - `timestampStart`
             * - - `timeEnlapsed`,
             * - - `totalSize`
             * - - `downloadedSize`
             * - - `downloadedPercentage`
             * - - `resultState`
             * - - `resultResponse`
             * - - `resultResponseObject`
             * - - `resultStatusCode`
             * - - `resultStatusText`
             */
            RequestCompleted: [
              'request',
              'originalEvent',
              'timestamp',
              'timestampStart',
              'timestampEnd',
              'timeEnlapsed',
              'totalSize',
              'downloadedSize',
              'downloadedPercentage',
              'resultState',
              'resultResponse',
              'resultResponseObject',
              'resultStatusCode',
              'resultStatusText'
            ],
            /**
             * `tkRequestsRequestCancelled`
             * Fired when a *request* was cancelled before it finished loading.
             * - Fires when the following `XMLHttpRequest Events` are fired:
             * - - `abort`
             * - - `error`,
             * - - `timeout`
             * - Provides the following properties:
            * - - `request`
             * - - `originalEvent`
             * - - `timestamp`
             * - - `timestampStart`
             * - - `timestampEnd`
             * - - `timeEnlapsed`,
             * - - `totalSize`
             * - - `downloadedSize`
             * - - `downloadedPercentage`
             * - - `resultState`
             */
            RequestCancelled: [
              'request',
              'originalEvent',
              'timestamp',
              'timestampStart',
              'timestampEnd',
              'timeEnlapsed',
              'totalSize',
              'downloadedSize',
              'downloadedPercentage',
              'resultState'
            ],
          },
          /**
           * Dispatch a custom request event
           * - See `eventList` for the full list of request events and their associated properties
           * - Note that all events are fired on the `window`.
           *
           * @param {string} eventName The name of the event.
           * @param {object} args The provided event propertes.
           * @param {false|string} source The source of the event, if available.
           * @returns {boolean|null} Returns **true** or **false** if the event was successfully dispatched, or NULL if an error occurred.
           * - See `dispatchCustomEvent()` for more information on the event dispatch result.
           */
          dispatchRequestEvent (eventName, args, source = false) {
            try {
              const requestsObj = ShiftCodesTK.requests;
              const requestsEventObject = requestsObj.requestEvents;

              if (Object.keys(requestsEventObject.eventList).indexOf(eventName) == -1) {
                throw `"${eventName}" is not a valid Request Event name`;
              }

              const eventProperties = (function () {
                const ignoredMissingProperties = [
                  'totalSize',
                  'downloadedSize',
                  'downloadedPercentage'
                ];
                let propertyList = requestsEventObject.eventList[eventName];
                let properties = {};

                for (let property of propertyList) {
                  if (args[property] !== undefined) {
                    properties[property] = args[property];
                  }
                  else {
                    properties[property] = null;

                    if (ignoredMissingProperties.indexOf(property) == -1) {
                      console.warn(`requestEvents.dispatchRequestEvent Warning: The argument property "${property}" was not provided.`);
                    }
                  }
                }

                return properties;
              })();

              return dispatchCustomEvent({
                event: {
                  target: window,
                  name: `${requestsEventObject.prefix}${eventName}`,
                  source: source
                },
                options: {
                  bubbles: true,
                  cancelable: false
                },
                customProperties: {
                  requestEventData: eventProperties
                }
              });
            }
            catch (error) {
              console.error(`requestEvents.${arguments.callee.name} Error: ${error}`);
              return false;
            }
          },
        },
        /**
         * Perform an AJAX Request
         *
         * @param {object|string} requestConfiguration Indicates how the request is to be configured. Can be in one of two forms:
         * - An `Object` can be provided with the *Request Properties* used to configure the request:
         * - - *path* `object` - The path to the resource being requested.
         * - - *type* `"GET"|"POST"` - The type of request being submitted.
         * - - *parameters* `Object` - The *Request Parameters* to be sent with the request.
         * - - *callback* `Function` - The callback `Function` to be invoked when the request has completed.
         * - - - The first provided argument is the *Request Response*. The value of the response is determined by `parseResultJSON`.
         * - - - The second provided argument is the *Request `Object`*.
         * - - *parseResultJSON* `boolean` - Indicates if the request result value should automatically be parsed into the corresponding JSON `object`. Determines the value of the *Request Response* argument of the `callback` function.
         * - - - If `parseResultJSON` is set to **true**:
         * - - - - If the response is a valid JSON string, the corresponding `Object` will be returned.
         * - - - - If the response is not a valid JSON string, returns **false**.
         * - - - If `parseResultJSON` is set to **false**, the *response text* will be returned.
         * - - *headers* `Object` - Additional request headers to be sent with the request.
         * - - - Requests automatically attach a **x-request-token** header containing the current *Request Token*.
         * - - - Requests automatically attach a **Accept** header with the value _"* / *"_ if it is not explicitly provided.
         * - - - Requests using a `type` value of **POST** automatically attach a **Content-Type** header of `application/x-www-form-urlencoded` if it is not explicitly provided.
         * - - - For security reasons, some headers can only be controlled by the user agent. These headers include the [forbidden header names](https://developer.mozilla.org/en-US/docs/Glossary/Forbidden_header_name) and [forbidden response header names](https://developer.mozilla.org/en-US/docs/Glossary/Forbidden_response_header_name).
         * - A `string` can be provided, indicating the name of the *Saved Request* to be used. The properties & parameters of the Saved Request will be used to configure the request.
         * @param {boolean} _tokenRefreshed - Indicates if the request has already been sent and is being resent with a refreshed Request Token.
         * - This is an internal property that should not be manually provided.
         * - Only used when a request returns a 401 Error regarding an expired Request Token.
         * @returns {string|false} Returns the *Request ID* of the request if it has been successfully dispatched, or **false** if an error occurred.
         * - You can use `getActiveRequest()` using the returned *Request ID* to access the active request object.
         */
        request (requestConfiguration, _tokenRefreshed = false) {
          const requestsObj = ShiftCodesTK.requests;

          try {
            /** The XHR Request Object */
            const request = (function () {
              if (window.XMLHttpRequest) {
                return new XMLHttpRequest();
              }
              else if (window.ActiveXObject) {
                return new ActiveXObject('Microsoft.XMLHttp');
              }
              else {
                ShiftCodesTK.toasts.newToast({
                  settings: {
                    id: 'cannot_perform_ajax_request_error',
                    duration: 'infinite'
                  },
                  content: {
                    title: 'Cannot Perform Request',
                    body: 'Your browser does not support asynchronous requests. You will be unable to use some features of ShiftCodesTK until you use a more updated browser.'
                  }
                });

                throw 'An Asynchronous Request Object could not be found.';
              }
            })();
            /** The unique Request ID used to retrieve the active request. */
            const requestID = randomTimestampID();
            /** The *Saved Request `Object`*, if applicable */
            const savedRequest = typeof requestConfiguration == 'string'
                                   ? requestsObj.savedRequests.getRequest(requestConfiguration, true)
                                   : false;
            /** The compiled request properties */
            const requestProperties = (function () {
              let properties = (function () {
                let properties = {};

                // Saved Request Properties
                if (savedRequest) {
                  for (let property in savedRequest.configuration.request) {
                    let propertyValue = savedRequest.configuration.request[property];

                    if (propertyValue !== null) {
                      properties[property] = propertyValue;
                    }
                  }

                  (function () {
                    const savedRequest = requestsObj.savedRequests.getRequest(requestConfiguration);
                    const metaParams = savedRequest.configuration.request.params;
                    const savedParams = savedRequest.parameters;

                    if (savedParams) {
                      let requestParams = {};

                      for (let paramName in savedParams) {
                        let param = savedParams[paramName];

                        if (param.value !== null) {
                          requestParams[paramName] = !param.isReadOnly
                                                     ? param.value
                                                     : param.defaultValue;
                        }
                      }

                      properties.parameters = requestParams;
                    }
                    if (metaParams) {
                      properties.parameters = mergeObj(properties.parameters, metaParams);
                    }
                  })();
                }
                // Provided Properties
                else if (typeof requestConfiguration == 'object') {
                  properties = requestConfiguration;
                }
                // Unrecognized Request Type
                else {
                  throw `Provided request is not a valid Saved Request Name String or Request Properties Object: ${requestConfiguration}`;
                }

                return mergeObj(requestsObj.DEFAULT_REQUEST_PROPERTIES, properties);
              })();

              // Update Type to Uppercase
              properties.type = properties.type.toUpperCase();
              // Add Content-Type Header to POST Requests
              if (properties.type == 'POST' && !properties.headers['Content-Type']) {
                properties.headers['Content-Type'] = 'application/x-www-form-urlencoded';
              }
              // Add Request Token Header
              properties.headers[requestToken.headerName] = requestToken.get();

              // Invalid Resource Path
              if (properties.path === null) {
                throw 'A resource path was not provided.';
              }
              // Invalid Request Type
              if ([ 'GET', 'POST' ].indexOf(properties.type) == -1) {
                throw `"${properties.type}" is not a valid request type.`;
              }

              return properties;
            })();

            /**
             * Opens and sends the AJAX Request
             *
             * @param {any} requestBody The *Request Body* of the request. Only used for **POST** Requests.
             */
            function sendRequest (requestBody = null) {
              // Open the Request
              request.open(requestProperties.type, requestProperties.path, true);

              // Update Headers
              (function () {
                const headers = requestProperties.headers;

                for (let headerName in headers) {
                  let headerValue = headers[headerName];

                  try {
                    request.setRequestHeader(headerName, headerValue);
                  }
                  catch (error) {
                    console.warn(`requests.${arguments.callee.name} Warning: "${headerName}" is not a valid header or "${headerValue}" is not a valid header value.\r\n${error}`);
                    continue;
                  }
                }
              })();

              // Send Request
              request.send(requestBody);
            }

            // Add Event Listeners
            (function () {
              /**
               * Handle the result of a request
               *
               * @param {*} requestEvent
               */
              function handleRequestEvent (requestEvent) {
                const currentTimestamp = moment().valueOf();
                const targetRequest = requestEvent.target;
                const eventType =  requestEvent.type;

                const customEventName = (function () {
                  if (eventType == 'loadstart')                                 { return 'RequestDispatched'; }
                  if ([ 'load', 'progress' ].indexOf(eventType) != -1)          { return 'RequestLoading'; }
                  if ([ 'abort', 'error', 'timeout' ].indexOf(eventType) != -1) { return 'RequestCancelled'; }
                  if (eventType == 'loadend')                                   { return 'RequestCompleted'; }
                })();
                const eventProperties = (function () {
                  let properties = {
                    request: requestEvent.target,
                    requestProperties: requestProperties,
                    originalEvent: requestEvent,
                    timestamp: currentTimestamp
                  };

                  // Start Timestamp
                  (function () {
                    if (targetRequest.requestEventData !== undefined) {
                      properties.timestampStart = targetRequest.requestEventData.timestampStart;
                    }
                    else {
                      properties.timestampStart = currentTimestamp;
                    }

                    properties.timeEnlapsed = currentTimestamp - properties.timestampStart;
                  })();
                  // End Timestamp & Result State
                  if ([ 'RequestCancelled', 'RequestCompleted' ].indexOf(customEventName) != -1) {
                    properties = mergeObj(properties, {
                      timestampEnd: moment().valueOf(),
                      resultState: customEventName == 'RequestCompleted'
                    });
                  }
                  // Request Stats
                  if (requestEvent.lengthComputable) {
                    properties = mergeObj(properties, {
                      totalSize: requestEvent.total,
                      downloadedSize: requestEvent.loaded,
                      downloadedPercentage: Math.round((requestEvent.loaded / requestEvent.total) * 100)
                    });
                  }
                  // Result Properties
                  if (customEventName == 'RequestCompleted') {
                    properties = mergeObj(properties, {
                      resultResponse: targetRequest.responseText,
                      resultResponseObject: tryJSONParse(targetRequest.responseText, 'ignore'),
                      resultStatusCode: targetRequest.status,
                      resultStatusText: targetRequest.statusText
                    });
                  }

                  return properties;
                })();

                // Update & Dispatch Events
                requestEvent.requestEventData = eventProperties;
                targetRequest.requestEventData = eventProperties;
                requestsObj.requestEvents.dispatchRequestEvent(customEventName, eventProperties);

                // Request Dispatched
                if (customEventName == 'RequestDispatched') {
                  requestsObj.activeRequests.addRequest(requestID, targetRequest);

                  if (savedRequest) {
                    savedRequest.activeRequest = requestID;
                  }
                }
                // Request Completed (Erroneously or Successfully)
                if ([ 'RequestCancelled', 'RequestCompleted' ].indexOf(customEventName) != -1) {
                  requestsObj.activeRequests.removeRequest(requestID);

                  if (savedRequest) {
                    let lastResult = {};

                    for (let propertyName in eventProperties) {
                      if (propertyName.indexOf('result') == 0) {
                        lastResult[propertyName] = eventProperties[propertyName];
                      }
                    }

                    try {
                      const resultSetData = eventProperties.resultResponseObject.payload.result_set;

                      if (resultSetData) {
                        for (let propertyName in resultSetData) {
                          let propertyValue = resultSetData[propertyName];
                          let parsedPropertyName = propertyName.replaceAll(/_(\w)/g, (match, m1) => { return m1.toUpperCase(); });

                          if (savedRequest.results[parsedPropertyName] !== undefined) {
                            savedRequest.results[parsedPropertyName] = propertyValue;
                          }
                        }
                      }
                    }
                    catch (error) {}

                    savedRequest.results.lastResult = lastResult;
                    savedRequest.activeRequest = false;

                    if (savedRequest.configuration.type == 'pagination') {
                      if (savedRequest.configuration.controls.syncControls) {
                        requestsObj.savedRequests.syncControls(requestConfiguration);
                      }
                    }
                  }

                  // Successful Request
                  if (customEventName == 'RequestCompleted') {
                    if (eventProperties.resultStatusCode == 401) {
                      const response = tryJSONParse(eventProperties.resultResponse, 'ignore');

                      if (response) {
                        if (response.statusMessage == 'Missing or Invalid Request Token' && !_tokenRefreshed) {
                          requestToken.check((newToken, oldToken) => {
                            requestProperties.headers[requestToken.headerName] = newToken;
                            
                            // Check Params
                            (function () {
                              const params = requestProperties.parameters;
  
                              for (let param in params) {
                                if (param.indexOf('token') != -1) {
                                  const value = params[param];
  
                                  if (value == oldToken) {
                                    params[param] = newToken;
                                  }
                                }
                              }
                            })();

                            return requestsObj.request(requestProperties, true);
                          });
                        }
                      }
                    }

                    if (typeof requestProperties.callback == 'function') {
                      requestProperties.callback(
                        requestProperties.parseResultJSON
                        ? eventProperties.resultResponseObject
                        : eventProperties.resultResponse,
                      eventProperties);
                    }
                  }
                  // Erroneous Request
                  else if (customEventName == 'RequestCancelled') {
                    requestProperties.callback(null, eventProperties);
                  }
                }
              }

              const eventList = [
                'abort',
                'error',
                'load',
                'loadend',
                'loadstart',
                'progress',
                'timeout'
              ];

              for (let event of eventList) {
                request.addEventListener(event, handleRequestEvent);
              }
            })();
            // Send Request
            (function () {
              let paramString = encodeQueryParameters(requestProperties.parameters, false);

              if (savedRequest) {
                const activeRequestID = savedRequest.activeRequest;

                if (activeRequestID) {
                  const activeRequest = requestsObj.activeRequests[activeRequest];

                  if (activeRequest) {
                    activeRequest.abort();
                  }
                }
              }

              if (requestProperties.type == 'GET') {
                if (!_tokenRefreshed) {
                  requestProperties.path += `?${paramString}`;
                }

                sendRequest();
              }
              else if (requestProperties.type == 'POST') {
                sendRequest(paramString);
              }
            })();

            return requestID;
          }
          catch (error) {
            console.error(`requests.${arguments.callee.name} Error: ${error}`);
            return false;
          }
        }
      };

      // Startup
      setTimeout(() => {
        const requestsObj = ShiftCodesTK.requests;
        const savedRequestsObj = requestsObj.savedRequests;

        // Event Listeners
        (function () {
          // Sort/Filter Form
          (function () {
            window.addEventListener('tkFormsFormBeforeSubmit', (event) => {
              const formEventData = event.formEventData;
              const form = formEventData.form;
              const formData = formEventData.formData;
              const savedRequests = savedRequestsObj.getAllRequests();
              let propertyWasUpdated = false;

              for (let requestName in savedRequests) {
                let savedRequest = savedRequestsObj.getRequest(requestName);

                if (savedRequest) {
                  let savedRequestParams = savedRequestsObj.getAllRequestParameters(requestName);
                  let savedRequestControls = savedRequest.configuration.controls;
  
                  if (savedRequest.configuration.controls && savedRequestControls.sortAndFilter == form) {
                    for (let originalFieldName in formData) {
                      let fieldName = originalFieldName.replace(/\[\]$/, '');
  
                      if (savedRequestParams[fieldName] !== undefined) {
                        let fieldValue = formData[originalFieldName];
      
                        if (fieldValue !== null) {
                          propertyWasUpdated = true;
                          savedRequestsObj.setRequestParameter(requestName, fieldName, fieldValue);
                        }
                        else {
                          savedRequestsObj.setRequestParameter(requestName, fieldName, savedRequestsObj.getRequestParameter(requestName, fieldName, 'FullData').defaultValue);
                        }
                      }
                    }
  
                    if (propertyWasUpdated) {
                      savedRequestsObj.setRequestParameter(requestName, 'page', 1);

                      if (savedRequest.configuration.syncParameters) {
                        savedRequestsObj.syncQueryParams(requestName, 'query');
                      }
                      if (savedRequestControls.syncControls) {
                        savedRequestsObj.syncControls(requestName);
                      }

                      requestsObj.request(requestName);
                    }
                  }
                }
              }
            });
          })();
          // Popstate Event
          window.addEventListener('popstate', (event) => {
            const requestList = savedRequestsObj.getAllRequests();

            for (let requestName in requestList) {
              let request = requestList[requestName];

              if (request.configuration.syncParameters) {
                savedRequestsObj.syncQueryParams(requestName, 'params');
                requestsObj.request(requestName);
              }
            }
          });
        })();
      }, 50);
    }
  }, 50);
})();