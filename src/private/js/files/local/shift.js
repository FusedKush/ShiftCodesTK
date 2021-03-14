// Initialization
(function () {
  const interval = setInterval(() => {
    const isReady = typeof ShiftCodesTK !== 'undefined' 
                    && typeof node_modules !== 'undefined'
                    && ShiftCodesTK.forms.isLoaded
                    && ShiftCodesTK.requests.isLoaded
                    && pagers.isLoaded;
    
    if (isReady) {
      clearInterval(interval);

      const currentTimestamp = node_modules.dayjs.utc().valueOf();
      const formsObj = ShiftCodesTK.forms;
      const layersObj = ShiftCodesTK.layers;
      const modalsObj = ShiftCodesTK.modals;
      const requestsObj = ShiftCodesTK.requests;
      const request = requestsObj.request;
      const toastsObj = ShiftCodesTK.toasts;

      /** Properties & Methods related to using SHiFT Codes */
      ShiftCodesTK.local.shift = {
        /** @property Counts of *Total*, *New*, and *Expiring* SHiFT Codes from the current result set */
        stats: {
          total: 0,
          new: 0,
          expiring: 0
        },
        /** 
         * @property Properties that control the returned result set 
         * - Properties are stored in the following format: 
         * - - `any value` - The current value of the property.
         * - - `any defaultValue` - The default value of the property before applying the query string parameters.
         * - - `boolean isLocked` - Indicates if the property is locked and cannot be changed from `defaultValue`.
         **/
        props: (function () {
          const props = {
            /** @property `false|string` The *GameID* of a specific game to filter by, or **false** to not filter by game. */
            game: false,
            /** 
             * @property `array` The list of *Code Status Filters*
             * - Possible values include *"active"*, *"expired"*, & *"hidden"*
             **/
            status: [ 'active' ],
            /** @property `false|string` The *Platform ID* of the platform to filter by, or **false** to not filter by platform. */
            platform: false,
            /** @property `false|string` The *User ID* of the user to filter by, or **false** to not filter by platform. */
            owner: false,
            /** @property `false|string` The *Code ID* of the SHiFT Code to search for, or **false** to not search for a particular SHiFT Code. */
            code: false,
            /** @property `"default"|"newest"|"oldest"` Indicates how the returned SHiFT Codes are to be ordered. */
            order: 'default',
            /** @property `int` Indicates how many SHiFT Codes are to be returned per result set. */
            limit: 10,
            /** @property `int` Indicates the current page number of results. */
            page: 1
          };
          for (let prop in props) {
            let propValue = props[prop];

            props[prop] = {
              value: propValue,
              defaultValue: propValue,
              isLocked: false
            };
          }

          return props;
        })(),
        /** @property The `XMLHTTPRequestObject` of the current or previous AJAX Request. */
        request: null,
        /** @property Properties & Methods regarding the SHiFT Code Update Poller */
        updateCheck: {
          /** @property `int` The Interval ID of the update interval */
          intervalID: 0,
          /** @property `int` Indicates in *minutes* how long between update polls */
          intervalDuration: 2,
          /** @property `object` Stores the first and last update check timestamps */
          updateStats: {
            /** @property `int` The timestamp of the first update check */
            first: currentTimestamp,
            /** @property `int` The timestamp of the last update check */
            last: currentTimestamp
          },
          /**
           * Toggle the SHiFT Code update indicator
           * 
           * @param {boolean} state Indicates the new visibility state of the update indicator
           * - **True** displays the indicator
           * - **False** hides the indicator
           * @param {int} count The total number of updates
           * @returns {boolean} Returns **true** on success, or **false** on failure
           */
          toggleIndicator (state, count = 0) {
            try {
              const indicator = dom.find.id('shift_update_indicator');

              if (indicator) {
                const counter = dom.find.child(indicator, 'class', 'counter');
                const title = dom.get(indicator, 'attr', 'aria-label');
                
                counter.innerHTML = count;
                updateLabel(indicator, title.replace(new RegExp('\\d+'), count), [ 'aria', 'tooltip' ]);
  
                // Show Indicator
                if (state) {
                  isHidden(indicator, false);
                  edit.class(indicator, 'remove', 'hidden');
                }
                // Hide Indicator
                else {
                  edit.class(indicator, 'add', 'hidden');
  
                  setTimeout(() => {
                    isHidden(indicator, true);
                  }, 250);
                }
  
                return true;
              }
              else {
                throw 'Indicator is missing';
              }
            }
            catch (error) {
              console.error(`local.shift.updateCheck.toggleIndicator Error: ${error}`);
              return false;
            }
          },
          /**
           * Update the *last update* timestamp
           * 
           * @returns {int|false} Returns the *new timestamp* on success, or **false** if an error occurred.
           */
          updateLastTimestamp () {
            try {
              const newTimestamp = node_modules.dayjs.utc().valueOf();
  
              this.updateStats.last = newTimestamp;
  
              return newTimestamp;
            }
            catch (error) {
              console.error(`local.shift.updateCheck.updateLastTimestamp Error: ${error}.`);
              return false;
            }
          },
          /**
           * Start the SHiFT Code Update Poller
           * 
           * @returns {boolean} Returns **true** on success, or **false** on failure
           */
          start () {
            try {
              this.intervalID = setInterval(this.poll, this.intervalDuration * 60000);
              return true;
            }
            catch (error) {
              console.error(`local.shift.updateCheck.start Error: ${error}`);
              return false;
            }
          },
          /**
           * Shut down the SHiFT Code Update Poller
           * 
           * @return {boolean} Returns **true** on success, or **false** on failure
           */
          stop () {
            try {
              clearInterval(this.intervalID);
              return true;
            }
            catch (error) {
              console.error(`local.shift.updateCheck.stop Error: ${error}`);
              return false;
            }
          },
          /**
           * Restart the SHiFT Code Update Poller
           * 
           * @returns {boolean} Returns **true** on success, or **false** on failure
           */
          restart () {
            try {
              this.stop();
              this.start();
              return true;
            }
            catch (error) {
              console.error(`local.shift.updateCheck.restart Error: ${error}`);
              return false;
            }
          },
          /**
           * Poll for SHiFT Code updates
           * 
           * @returns {boolean} Returns the **true** if polling was successful, or **false** if an error occurred.
           */
          poll () {
            const shiftObj = ShiftCodesTK.local.shift;
            const updateCheck = shiftObj.updateCheck;

            function handleUpdateData (responseText) {
              const updateData = tryJSONParse(responseText);

              if (updateData) {
                const count = updateData.payload.count;

                if (count > 0) {
                  updateCheck.toggleIndicator(true, count);
                }

                updateCheck.updateLastTimestamp();
              }
            }

            return newAjaxRequest({
              file: '/assets/requests/get/shift/updates',
              params: {
                'last_check': node_modules.dayjs.utc(updateCheck.updateStats.last).format(),
                'game_id': shiftObj.getResultProp('game')
              },
              callback: handleUpdateData
            });
          },
          /**
           * Refresh the SHiFT Code List with the most recently updated data
           */
          update () {
            ShiftCodesTK.local.shift.getCodes();
            this.toggleIndicator(false);
            // this.updateLastTimestamp();
          }
        },
        /** @property Locations related to the SHiFT Codes */
        locations: {
          shiftHeader: dom.find.id('shift_header'),
          /** @property The SHiFT Code Display List */
          shiftCodeList: dom.find.id('shift_code_list'),
        },
        /** @property SHiFT Code Templates */
        templates: {
          shiftCode: dom.find.id('shift_code_template'),

        },

        /**
         * Retrieve a SHiFT Code result set property
         * 
         * @param {string} property The property to be retrieved
         * @returns {any} Returns the *property value* on success. Will return **NULL** if an error occurred.
         */
        getResultProp (property) {
          try {
            const prop = this.props[property];

            if (prop) {
              return prop.value;
            }
            else {
              throw `"${property}" is not a valid property name`;
            }

            return false;
          }
          catch (error) {
            console.error(`local.shift.getResultProp Error: ${error}`);
            return NULL;
          }
        },
        /**
         * Update a SHiFT Code result set property
         * 
         * @param {string} property The property to be updated.
         * @param {any} value The new value of the property.
         * @returns {boolean} Returns the **true** on success, or **false** on failure.
         */
        setResultProp (property, value) {
          try {
            const prop = this.props[property];

            if (prop && !prop.isLocked) {
              prop.value = value;
              return true;
            }
            else if (!prop) {
              throw `"${property}" is not a valid property name`;
            }

            return false;
          }
          catch (error) {
            console.error(`local.shift.setResultProp Error: ${error}`);
            return NULL;
          }
        },

        /**
         * Synchronize the SHiFT Control and Display Components with those in `stats`
         * 
         * @returns {boolean} Returns **true** on success, or **false** on failure
         */
        syncShiftComponents () {
          try {
            const shiftObj = ShiftCodesTK.local.shift;
            const header = dom.find.id('shift_header');
  
            if (header) {
              const props = shiftObj.props;

              // Badges
              (function () {
                const badgeContainer = dom.find.child(header, 'class', 'section badges');

                if (badgeContainer) {
                  const badges = dom.find.children(badgeContainer, 'class', 'badge');

                  for (let badge of badges) {
                    let type = dom.get(badge, 'attr', 'data-value');
                    let displayType = ucWords(type);
                    let count = shiftObj.stats[type];
                    let countDisplay = dom.find.child(badge, 'class', 'count');
                    let title = '';

                    if (count > 0) {
                      if (type == 'total') { title = `${count} SHiFT Code${checkPlural(count)} Available`; }
                      else                 { title = `${count} ${displayType} SHiFT Code${checkPlural(count)}`; }
                    }
                    else {
                      if (type == 'total') { title = 'No SHiFT Codes Available'; }
                      else                 { title = `No ${displayType} SHiFT Codes`; }
                    }

                    edit.class(badge, count == 0 ? 'add' : 'remove', 'inactive');
                    countDisplay.innerHTML = count;
                    updateLabel(badge, title, [ 'aria', 'tooltip' ]);
                  }
                }
                else {
                  console.warn('local.shift.syncShiftComponents Warning: Badges are missing');
                }
              })();
              // Update Sort & Filter
              (function () {
                const form = dom.find.id('shift_header_sort_filter_form');
           
                if (form) {
                  const bindings = {
                    sort: shiftObj.getResultProp('order'),
                    status_filter: shiftObj.getResultProp('status'),
                    platform_filter: shiftObj.getResultProp('platform'),
                    game_filter: shiftObj.getResultProp('game')
                  };

                  // Update Platform Filter
                  // if (!shiftObj.props.game.isLocked) {
                  //   const platforms = formsObj.getField(form, 'platform_filter');
                  //   const game = bindings.game_filter;

                  //   for (let platform of platforms) {
                  //     if (game === false || ShiftCodesTK.shift.games[game].support.unsupported.platforms.indexOf(platform.value) != -1) {
                  //       isHidden(platform, game !== false);
                  //     }
                  //   }
                  // }

                  if (!dom.has(form, 'class', 'updated')) {
                    for (let binding in bindings) {
                      let bindingField = formsObj.getField(form, binding);
                      let bindingValue = bindings[binding];

                      if (bindingField && bindingValue) {
                        formsObj.updateField(
                          Array.isArray(bindingField)
                            ? bindingField[0]
                            : bindingField,
                          bindingValue,
                          // {
                          //   updateDefault: true
                          // }
                        );
                      }
                    }

                    edit.class(form, 'add', 'updated');
                  }
                }
                else {
                  console.warn('local.shift.syncShiftComponents Warning: Sorting & Filtering Form is missing.');
                }
              })();
              // Pager
              (function () {
                /** The SHiFT Code List Pager */
                const pager = dom.find.id('shift_code_pager');
                /** SHiFT Code Stats */
                const pagerStats = {
                  limit: shiftObj.getResultProp('limit'),
                  offset: shiftObj.getResultProp('page'),
                  total: shiftObj.stats.total
                };
                /** Pager Properties */
                const pagerProps = {
                  now: pagerStats.offset,
                  max: pagerStats.total > 0 
                       ? Math.ceil(pagerStats.total / pagerStats.limit) 
                       : 1
                };

                return updatePagerProps(pager, pagerProps);
              })();

              return true;
            }
            else {
              throw 'SHiFT Header is missing.';
            }
          }
          catch (error) {
            console.error(`local.shift.syncShiftComponents Error: ${error}.`);
            return false;
          }
        },
        /**
         * Sync `props` to and from the *query parameters*
         * 
         * @param {"query"|"var"} syncTo Indicates which direction to sync
         * - **"query"**: Syncs `props` to the *query parameters*
         * - **"var"** Syncs the *query parameters* to `props`
         * @returns {object|false} Returns the *synced properties* on success, or **false** if an error occurred.
         */
        syncQueryParams (syncTo = 'query') {
          const shiftObj = this;

          if (syncTo == 'query') {
            let queryParameters = {};

            // Existing Query Parameters
            queryParameters = mergeObj(queryParameters, getQueryParameters());
            // `Props`
            for (let propName in shiftObj.props) {
              let propData = shiftObj.props[propName];
              let canUpdateProp = (function () {
                if (!propData.isLocked) {
                  const hasSameValueAsDefault = (function () {
                    if (!Array.isArray(propData.value)) {
                      return propData.value != propData.defaultValue;
                    }
                    else {
                      const hasSameArrays = (function () {
                        for (let item of propData.value) {
                          if (propData.defaultValue.indexOf(item) == -1) {
                            return false;
                          }
                        }
                        for (let item of propData.defaultValue) {
                          if (propData.value.indexOf(item) == -1) {
                            return false;
                          }
                        }

                        return true;
                      })();
  
                      return !hasSameArrays;
                    }
                  })();

                  if (hasSameValueAsDefault) {
                    return true
                  }
                }

                return false;
              })();

              if (canUpdateProp) {
                queryParameters[propName] = propData.value;
              }
              else if (queryParameters[propName] !== undefined) {
                delete queryParameters[propName];
              }
            }

            // window.history.pushState({}, '', encodeQueryParameters(queryParameters));
            updateQueryParameters(queryParameters, 'new');

            return queryParameters;
          }
          else if (syncTo == 'var') {
            const queryParameters = getQueryParameters();

            for (let propName in shiftObj.props) {
              let propValue = shiftObj.props[propName];
              let queryValue = (function () {
                const queryValue = queryParameters[propName];
                const intProps = [ 'limit', 'page' ];

                if (queryValue && intProps.indexOf(propName) != -1) {
                  return tryParseInt(queryValue);
                }
                else {
                  return queryValue;
                }
              })();

              if (queryValue !== undefined && queryParameters[propName] != propValue) {
                shiftObj.setResultProp(propName, queryValue);
              }
              else if (queryValue === undefined) {
                shiftObj.setResultProp(propName, shiftObj.props[propName].defaultValue);
              }
            }

            return shiftObj.props;
          }

          return false;
        },

        /**
         * Update the *redemption status* of a SHiFT Code
         * 
         * @param {Element|string} shiftCode The SHiFT Code being updated.
         * - Using the *SHiFT Code Dropdown Panel* will update the appearance to match the new `redemptionState`.
         * - Using the *Code Hash* of the SHiFT Code will update the appearance of all currently matching *SHiFT Code Dropdown Panels*
         * @param {boolean} redemptionState Indicates if the SHiFT Code is being *redeemed* or *unredeemed*:
         * - **True**: Marks the SHiFT Code as *Redeemed*
         * - **False**: Marks the SHiFT Code as *Un-redeemed*
         * @returns {boolean} Returns **true** on success, and **false** on failure.
         */
        redeemShiftCode (shiftCode, redemptionState = true) {
          try {
            /** `"codeHash"|"panel"|false` The type of argument provided for `shiftCode` */
            const shiftCodeType = (function () {
              if (typeof shiftCode == 'string' && shiftCode.length == 12) {
                return 'codeHash';
              }
              else if (typeof shiftCode == 'object' && shiftCode.constructor.name.indexOf('Element') != -1 && dom.has(shiftCode, 'class', 'shift-code')) {
                return 'panel';
              }
  
              return false;
            })();
  
            /**
             * Update the redemption controls and content for a given *SHiFT Code Dropdown Panel*
             * 
             * @param {Element} shiftCodePanel The SHiFT Code to be updated
             */
            function updateRedemptionComponents (shiftCodePanel) {
              const redemptionButtonContainer = dom.find.child(shiftCodePanel, 'class', 'action redeem');
              const redemptionButton = dom.find.child(redemptionButtonContainer, 'class', 'redeem');
  
              edit.class(shiftCodePanel, redemptionState ? 'add' : 'remove', 'redeemed');
  
              if (redemptionButton) {
                /** String replacement bindings */
                const bindings = {
                  icon: [
                    redemptionState 
                     ? 'fa-bookmark' 
                     : 'fa-check', 
                    redemptionState 
                     ? 'fa-check' 
                     : 'fa-bookmark'
                  ],
                  title: [
                    redemptionState 
                      ? 'redeemed' 
                      : 'unredeemed', 
                    redemptionState 
                      ? 'unredeemed' 
                      : 'redeemed'
                  ]
                };
                /** Base title and tooltip strings */
                const baseTitles = {
                  aria: 'Mark this SHiFT Code as redeemed', 
                  tooltip: `Mark this SHiFT Code as&nbsp;<em>redeemed</em>
                            <br>
                            <br><button class="link no-color modal-toggle" data-modal="shift_code_redeeming_codes_info_modal"><strong>Learn More</strong></button>`
                };
  
                // Button Content
                redemptionButton.innerHTML = redemptionButton.innerHTML.replace(...bindings.icon);
                // redemptionButton.innerHTML = redemptionButton.innerHTML.replace(...bindings.title);

                if (!redemptionButton.disabled) {
                  // Aria
                  edit.attr(redemptionButton, 'update', 'aria-pressed', redemptionState);
                  updateLabel(redemptionButton, baseTitles.aria.replace(...bindings.title), [ 'aria' ]);
                  // Tooltip
                  updateLabel(redemptionButton, baseTitles.tooltip.replace(...bindings.title), [ 'tooltip' ]);
                }
              }
            }
            /**
             * Toggle the active state of Redemption Buttons for all applicable SHiFT Code Dropdown Panels.
             * 
             * @param {array} shiftCodePanels An array of SHiFT Codes to be updated.
             * @param {boolean} state The new *Redemption State* of the SHiFT Code.
             * - **True**: Enables the Redemption Buttons
             * - **False**: Disables the Redemption Buttons
             */
            function updateRedemptionButtons (shiftCodePanels, state) {
              for (let shiftCodePanel of shiftCodePanels) {
                const redemptionButton = dom.find.child(shiftCodePanel, 'class', 'action redeem');
  
                if (redemptionButton) {
                  isDisabled(redemptionButton, !state);
                  edit.class(redemptionButton, !state ? 'add' : 'remove', 'in-progress');
                }
              }
            }

            if (!shiftCodeType) {
              throw 'Provided SHiFT Code is not a valid SHiFT Code Dropdown Panel or Code Hash ID';
            }

            if (shiftCodeType == 'panel') {
              updateRedemptionComponents(shiftCode);
            }
            else if (shiftCodeType == 'codeHash') {
              const matchingShiftCodes = dom.find.children(this.locations.shiftCodeList, 'attr', 'data-code-hash', shiftCode);

              if (!matchingShiftCodes) {
                throw `No present SHiFT Codes match the provided code hash: "${shiftCode}".`;
              }

              updateRedemptionButtons(matchingShiftCodes, true);

              for (let matchingShiftCode of matchingShiftCodes) {
                updateRedemptionComponents(matchingShiftCode);
              }

              if (redemptionState && responseData.payload.form_response) {
                let toastContent = (function () {
                  let type = response.payload.toastType;

                  if (type == 1) {
                    return "This SHiFT Code has been redeemed successfully! Redeemed SHiFT Codes will be marked anytime you visit ShiftCodesTK from your current browser. If your browser cookies are deleted, your redeemed SHiFT Codes may be lost."
                  }
                  if (type == 2) {
                    return "This SHiFT Code has been redeemed successfully! Redeemed SHiFT Codes will be marked anytime you visit ShiftCodesTK as long as you are logged in."
                  }
                })();

                ShiftCodesTK.toasts.newToast({
                  settings: {
                    id: 'redeemed_code_toast',
                    duration: 'infinite'
                  },
                  content: {
                    icon: 'fas fa-key',
                    title: 'SHiFT Code Redeemed!',
                    body: toastContent
                  },
                  actions: [
                    {
                      content: 'Learn More',
                      link: '#/help/redeeming-shift-codes'
                    }
                  ]
                });
              }
            }
          }
          catch (error) {
            console.error(`local.shift.redeemShiftCode Error: ${error}`);
            return false;
          }
        },
        /**
         * Update the `Last Updated` timestamp on a given *SHiFT Code Dropdown Panel*.
         * 
         * @param {Element} shiftCode The SHiFT Code to update
         * @param {string} timestampString A datetime string used to construct the timestamp. If ommitted, the current timestamp will be used.
         * @returns {string} Returns the *new timestamp* on success, or **false** on failure.
         */
        updateShiftCodeTimestamp (shiftCode, timestampString = undefined) {
          const field = (function () {
            const codeInfo = dom.find.child(shiftCode, 'class', 'code-info');

            if (codeInfo) {
              const lastUpdate = dom.find.child(codeInfo, 'class', 'last-update');

              if (lastUpdate) {
                const content = dom.find.child(lastUpdate, 'tag', 'dd');

                if (content) {
                  return content;
                }
              }
            }

            return false;
          })();

          if (field) {
            const timestamp = node_modules.dayjs.utc(timestampString);

            if (timestamp) {
              const formattedTimestamp = timestamp.format('MMMM DD, YYYY hh:mm A [UTC]');
    
              field.innerHTML = timestamp.fromNow();
              updateLabel(field, formattedTimestamp, [ 'aria', 'tooltip' ]);

              return formattedTimestamp;
            }
          }

          return false;
        },

        /**
         * Toggle the active state of the SHiFT Code interface controls
         * 
         * @param {boolean} isActive Indicates the new active state of the SHiFT Code
         * - **True** indicates that the controls are to be *enabled*.
         * - **False** indicates that the controls are to be *disabled*.
         * @returns {boolean} Returns **true** on success, or **false** on failure
         */
        toggleControls (state) {
          try {
            const shiftObj = this;
            const controls = (function () {
              let badges = dom.find.child(shiftObj.locations.shiftHeader, 'class', 'section badges');
              let controls = [
                dom.find.child(badges, 'class', 'new'),
                dom.find.child(badges, 'class', 'expiring'),
                dom.find.id('shift_header_add'),
                dom.find.id('shift_header_sort_filter'),
                dom.find.id('shift_code_pager')
              ];
              
              return controls;
            })();
            let controlsUpdated = 0;
  
            for (let control of controls) {
              if (control && !dom.has(control, 'class', 'inactive')) {
                isDisabled(control, !state);
                controlsUpdated++;
              }
            }

            if (controlsUpdated == 0) {
              throw 'No controls were found on this page';
            }

            return true;
          }
          catch (error) {
            console.error(`local.shift.toggleControls Error: ${error}`);
            return false;
          }
        },
        /**
         * Update various components of the SHiFT Code List Overlay
         * 
         * @param {object} settings The components that are to be updated and a **boolean** indication of if they are to be *visible* or not.
         * - `overlay` — The SHiFT Code List Overlay container
         * - `spinner` — The Loading Spinner
         * - `error` — The error message to be displayed when no SHiFT Codes were found
         * @returns {boolean} Returns **true** on success, and **false** on failure.
         */
        updateOverlay (settings) {
          const pieces = {};
                pieces.overlay = dom.find.id('shift_overlay');
                pieces.spinner = dom.find.child(pieces.overlay, 'class', 'spinner');
                pieces.error = dom.find.child(pieces.overlay, 'class', 'error');

          for (let piece in pieces) {
            const isVisible = settings[piece];

            isHidden(pieces[piece], !isVisible);
          }

          return true;
        },
        /**
         * Clear all present SHiFT Codes from the SHiFT Code list
         * 
         * @returns {int|false} Returns the *number of cleared SHiFT Codes* on success, or **false** if an error occurred.
         */
        clearShiftCodes () {
          const shiftCodes = dom.find.children(this.locations.shiftCodeList, 'class', 'shift-code');
          const removedCodes = shiftCodes.length;

          for (let i = shiftCodes.length - 1; i >= 0; i--) {
            deleteElement(shiftCodes[i]);
          }

          return removedCodes;
        },

        /**
         * Create a new SHiFT Code Dropdown Panel for a given SHiFT Code
         * 
         * @param {object} shiftCodeData The *SHiFT Code Data `object`*
         * @returns {Element|false} Returns the new *SHiFT Code Dropdown Panel* on success, or **false** if an error occurred.
         */
        createShiftCodePanel (shiftCodeData) {
          try {
            /** The SHiFT Codes Object */
            const shiftObj = this;
            /** The new SHiFT Code Dropdown Panel */
            const shiftCodePanel = edit.copy(shiftObj.templates.shiftCode);
            /** Sections of the `shiftCodePanel` */
            const shiftCodeSections = {
              header: dom.find.child(shiftCodePanel, 'class', 'header'),
              body: dom.find.child(shiftCodePanel, 'class', 'body')
            };
            /** Different formats of the SHiFT Code ID */  
            const shiftCodeIDs = {};
                  shiftCodeIDs.baseElementID = 'shift_code';
                  shiftCodeIDs.codeID = shiftCodeData.properties.code.id;
                  shiftCodeIDs.elementID = `${shiftCodeIDs.baseElementID}_${shiftCodeIDs.codeID}`;

            // Properties
            (function () {
              /**
               * Converts IDs to their updated equivalents
               * 
               * @param {string} originalIDs A comma-separated list of IDs to update
               * @returns {string} Returns the updated string
               */
              function getUpdatedID (originalIDs) {
                const idList = originalIDs.split(', ');

                for (let id in idList) {
                  idList[id] = `${shiftCodeIDs.elementID}_${idList[id]}`;
                }

                return idList.join(', ');
              }

              shiftCodePanel.id = shiftCodeIDs.elementID;
              edit.attr(shiftCodePanel, 'add', 'data-code-id', shiftCodeIDs.codeID);

              if (codeHash = shiftCodeData.properties.code.hash) {
                edit.attr(shiftCodePanel, 'add', 'data-code-hash', codeHash);
              }

              // Update Properties
              (function () {
                const updatedProperties = [
                  'id',
                  'for',
                  'data-view',
                  'data-target',
                  'data-layer-target',
                  'data-layer-targets',
                  'aria-labelledby',
                  'aria-describedby',
                ];
                const childrenToUpdate = shiftCodePanel.querySelectorAll(`[${updatedProperties.join('], [')}]`);
                
                for (let child of childrenToUpdate) {
                  for (let property of updatedProperties) {
                    const childValue = dom.get(child, 'attr', property);

                    if (childValue !== false && childValue != '') {
                      edit.attr(child, 'update', property, getUpdatedID(childValue));
                    }
                  }
                }
              })();

            })(); 
            // Configuration
            (function () {
              // Dropdown Panel Setup
              dropdownPanelSetup(shiftCodePanel);
              // Multi-View Configuration
              multiView_setup(shiftCodeSections.body);
              // Forms
              formsObj.setupChildForms(shiftCodePanel);
            })();
            // Header
            (function () {
              // Reward
              dom.find.child(shiftCodeSections.header, 'class', 'reward').innerHTML = shiftCodeData.info.reward;
              
              // Labels
              (function () {
                const codeState = shiftCodeData.properties.code.state;
                const states = shiftCodeData.states;
                
                // Basic, Rare, New, Expiring 
                (function () {
                  const isBasicShiftCode = shiftCodeData.info.reward.trim().search(new RegExp('\\d{1} Golden Key(s){0,1}$')) == 0;
  
                  // Basic SHiFT Code
                  if (isBasicShiftCode) {
                    edit.class(shiftCodePanel, 'add', 'basic');
                  }
                  // Rare SHiFT Code
                  else {
                    edit.class(shiftCodePanel, 'add', 'rare');
                  }
                  if (states.code.isActive && codeState == 'active') {
  
                    // New SHiFT Code
                    if (states.code.isNew) {
                      edit.class(shiftCodePanel, 'add', 'new');
                    }
                    // Expiring SHiFT Code
                    if (states.code.isExpiring) {
                      edit.class(shiftCodePanel, 'add', 'expiring');
                    }
                  }
                  // Hidden
                  else if (codeState == 'hidden') {
                    edit.class(shiftCodePanel, 'add', 'hidden');
                  }
                  // Expired
                  if (!states.code.isActive) {
                    edit.class(shiftCodePanel, 'add', 'expired');
                  }
                })();

                // Game Label
                if (!requestsObj.savedRequests.getRequestParameter('FetchShiftCodes', 'game')) {
                  /** The Game Label */
                  let label = dom.find.child(shiftCodeSections.header, 'class', 'label game-label');
                  /** The Game ID of the SHiFT Code */
                  let gameID = shiftCodeData.properties.game_id;
                  /** The Game Name of the SHiFT Code */
                  let gameName = shiftNames[gameID];
                  
                  // Display the label
                  edit.class(shiftCodePanel, 'add', 'game-label');
                  // Update the label
                  edit.class(label, 'add', gameID);
                  label.innerHTML = label.innerHTML.replace('Borderlands', gameName);
                  updateLabel(label, `This SHiFT Code is redeemable for ${gameName}`, [ 'aria', 'tooltip' ]);
                }

                // Recently Submitted
                if (states.code.wasRecentlySubmitted) {
                  edit.class(shiftCodePanel, 'add', 'recently-submitted');
                }
                // Recently Updated
                else if (states.code.wasRecentlyUpdated) {
                  edit.class(shiftCodePanel, 'add', 'recently-updated');
                }
                // Owner
                if (states.user.isOwner) {
                  edit.class(shiftCodePanel, 'add', 'owned');
                }
              })();

              // Progress Bar
              (function () {
                /** The header Progress Bar */
                const progressBar = dom.find.child(shiftCodeSections.header, 'class', 'progress-bar');
                /** The inside bar of the Progress Bar */
                const innerProgressBar = dom.find.child(progressBar, 'class', 'progress');
                /** The progress value of the Progress Bar */
                let progressValue = 0;
                /** The label of the Progress Bar */
                let progressTitle = '';

                // SHiFT Code has an Expiration Date
                if (shiftCodeData.info.release_date.type == 'date' && [ 'through', 'until' ].indexOf(shiftCodeData.info.expiration_date.type) != -1) {
                  /** The Expiration Date Moment of the SHiFT Code */
                  let expiration = node_modules.dayjs.utc(shiftCodeData.info.expiration_date.value);
                  /** The total duration of the SHiFT Code */
                  let duration = expiration.diff(shiftCodeData.info.release_date.value, 'hours');
                  /** The total number of hours remaining before the SHiFT Code is set to expire */
                  let timeLeft = expiration.diff(node_modules.dayjs.utc(), 'hours');
                  /** The time-from-now string for the Expiration Date */
                  let timeAgo = expiration.fromNow(true);

                  // Active SHiFT Code
                  if (shiftCodeData.states.code.isActive) {
                    progressValue = 100 - Math.round((timeLeft / duration) * 100);
                    progressTitle = ucWords(`${timeAgo} Remaining`);
                  }
                  // Expired SHiFT Code
                  else {
                    progressValue = 100;
                    progressTitle = ucWords(`Expired ${timeAgo} Ago`);
                  }
                }
                // SHiFT Code does have a Release or Expiration Date, or does not expire
                else {
                  edit.class(progressBar, 'add', 'inactive');
                  progressValue = 0;

                  if (shiftCodeData.info.expiration_date.type == 'infinite') {
                    progressTitle = 'No Expiration Date';
                  }
                  else {
                    progressTitle = 'No Provided Release/Expiration Date';
                  }
                }

                // edit.attr(progressBar, 'add', 'aria-valuenow', progressValue);
                updateProgressBar(progressBar, progressValue, { useWidth: true });
                updateLabel(progressBar, progressTitle, [ 'tooltip' ]);
                edit.class(innerProgressBar, 'add', shiftCodeData.properties.game_id);
                // innerProgressBar.style.width = `${progressValue}%`;
              })();
            })();
            // Body
            (function () {
              /**
               * Retrieve the content block of a given field
               * 
               * @param {string} name The name of the field.
               * @returns {Element} Returns the content block of the field
               */
              function getShiftCodeField (name) {
                const section = dom.find.child(shiftCodeSections.body, 'class', `section ${name}`);
                const content = dom.find.child(section, 'class', 'content');

                return content;
              }

              // Code Information
              (function () {
                // Release & Expiration Date
                (function () {
                  /** Date Formats */
                  let formats = {};
                      /** Display Date Formats */
                      formats.dates = {};
                      formats.dates.date = 'MMM DD, YYYY';
                      formats.dates.expandedDate = 'MMMM DD, YYYY';
                      formats.dates.time = 'h:mm A zz';
                      formats.dates.full = `${formats.dates.date} ${formats.dates.time}`;
                      formats.dates.expanded = `${formats.dates.expandedDate} ${formats.dates.time}`;
                      /** Calendar Date Formats */
                      formats.calendars = {
                        sameDay: '[Today]',
                        nextDay: '[Tomorrow]',
                        nextWeek: 'dddd',
                        lastDay: '[Yesterday]',
                        lastWeek: '[Last] dddd',
                      };
                  /** Specific Release & Expiration Formats & Dates */
                  let dates = {};
                      /** Release Date Formats & Dates */
                      dates.release = {};
                      dates.release.formats = (function () {
                        let f = {
                          simple: {},
                          full: {},
                          expanded: {}
                        };
                        let dates = formats.dates
                        let calendars = formats.calendars;

                        for (let calendar in calendars) {
                          f.simple[calendar] = `${calendars[calendar]}`;
                          f.full[calendar] = `${calendars[calendar]}, ${dates.date}`;
                          f.expanded[calendar] = `${calendars[calendar]}, ${dates.expandedDate}`;
                        }

                        f.simple.sameElse = f.full.sameElse = dates.date;
                        f.expanded.sameElse = `dddd, ${dates.expandedDate}`;

                        return f;
                      })();
                      dates.release.type = shiftCodeData.info.release_date.type;
                      dates.release.dates = (function () {
                        let release = shiftCodeData.info.release_date.value;

                        if (dates.release.type == 'date') {
                          /** The SHiFT Code Release Date `dayjs` object */
                          let releaseObj = node_modules.dayjs(release);

                          return {
                            simple: releaseObj.calendar(null, dates.release.formats.simple),
                            full: releaseObj.calendar(null, dates.release.formats.full),
                            expanded: releaseObj.calendar(null, dates.release.formats.expanded),
                          };
                        }
                        else {
                          return false;
                        }
                      })();
                      /** Expiration Date Formats & Dates */
                      dates.expiration = {};
                      dates.expiration.formats = (function () {
                        let f = {
                          simple: {},
                          full: {},
                          expanded: {}
                        };
                        let dates = formats.dates
                        let calendars = formats.calendars;

                        for (let calendar in calendars) {
                          f.simple[calendar] = `${calendars[calendar]}, ${dates.time}`;
                          f.full[calendar] = `${calendars[calendar]}, ${dates.full}`;
                          f.expanded[calendar] = `${calendars[calendar]}, ${dates.expanded}`;
                        }

                        f.simple.sameElse = f.full.sameElse = dates.full;
                        f.expanded.sameElse = `dddd, ${dates.expanded}`;

                        return f;
                      })();
                      dates.expiration.type = shiftCodeData.info.expiration_date.type;
                      dates.expiration.dates = (function () {
                        let expiration = shiftCodeData.info.expiration_date.value;

                        if ([ 'through', 'until' ].indexOf(dates.expiration.type) != -1) {
                          /** The SHiFT Code Release Date `dayjs` object */
                          let expirationObj = node_modules.dayjs.tz(expiration, shiftCodeData.info.timezone);

                          return {
                            simple: expirationObj.calendar(null, dates.expiration.formats.simple),
                            full: expirationObj.calendar(null, dates.expiration.formats.full),
                            expanded: expirationObj.calendar(null, dates.expiration.formats.expanded),
                          };
                        }
                        else {
                          return false;
                        }
                      })();

                  for (let date in dates) {
                    let dateType = dates[date].type;
                    let dateDates = dates[date].dates;
                    /** The Date Field Components */
                    let comps = {};
                        comps.main = getShiftCodeField(date);
                        comps.simple = dom.find.child(comps.main, 'class', 'simple');
                        comps.full = dom.find.child(comps.main, 'tag', 'dd');

                    // Valid Date
                    if (dateDates) {
                      // Display Relative Date
                      if (dateDates.simple != dateDates.full) {
                        comps.simple.firstChild.innerHTML = dateDates.simple;
                      }
                      // Only display Actual Date
                      else {
                        deleteElement(comps.simple);
                      }

                      comps.full.innerHTML = dateDates.full;
                      updateLabel(comps.full, dateDates.expanded, [ 'tooltip' ]);
                    }
                    // Date was not provided or never expires
                    else {
                      edit.class(comps.main, 'add', 'inactive');
                      deleteElement(comps.simple);

                      // No date provided
                      if (dateType == 'none') {
                        comps.full.innerHTML = `N/A`;
                        updateLabel(comps.full, `No ${ucWords(date)} Date was provided`, [ 'tooltip' ]);
                      }
                      else if (dateType == 'infinite') {
                        comps.full.innerHTML = `Never Expires`;
                        updateLabel(comps.full, `This SHiFT Code is set to never expire`, [ 'tooltip' ]);
                      }
                    }
                  }
                })();
                // Source
                (function () {
                  /** The SHiFT Code Source Field */
                  let field = getShiftCodeField('source');
                  /** The Source Link Component */
                  let onlineComp = dom.find.child(field, 'class', 'online-source');
                  /** The Source Static Text Component */
                  let physicalComp = dom.find.child(field, 'class', 'physical-source');
                  /** The SHiFT Code Source Type */
                  let sourceType = shiftCodeData.info.source.type;
                  /** The SHiFT Code Source */
                  let source = shiftCodeData.info.source.value;

                  if (sourceType != 'none') {
                    if (sourceType == 'online') {
                      onlineComp.href = source;
                      onlineComp.innerHTML += source;
                      deleteElement(physicalComp.nextElementSibling);
                      deleteElement(physicalComp);
                    }
                    else if (sourceType == 'physical') {
                      physicalComp.innerHTML = source;
                      deleteElement(onlineComp.nextElementSibling);
                      deleteElement(onlineComp);
                    }
                  }
                  else {
                    edit.class(field, 'add', 'inactive');
                    physicalComp.innerHTML = 'N/A';
                    updateLabel(physicalComp, 'A source was not provided for this SHiFT Code', [ 'title', 'tooltip' ]);
                    deleteElement(onlineComp.nextElementSibling);
                    deleteElement(onlineComp);
                  }
                })();
                // Notes
                (function () {
                  /** The SHiFT Code Notes Field */
                  let field = getShiftCodeField('notes');
                  /** The SHiFT Code Notes */
                  let notes = shiftCodeData.info.notes;

                  if (notes !== null) {
                    /** The Notes Field list */
                    let list = dom.find.child(field, 'tag', 'ul');
                    /** The converted notes markup */
                    let markup = (function () {
                      // One or more specified line-items
                      if (notes.indexOf('-') != -1) {
                        // Convert "-" to line items
                        return notes.replace(new RegExp('-.*', 'g'), function (match) {
                          return `${match.replace(new RegExp('-\\s{1}', 'g'), '<li>')}</li>`;
                        });
                      }
                      // No specified line items
                      else {
                        return `<li>${notes}</li>`;
                      }
                    })();

                    list.innerHTML = markup;
                  }
                  else {
                    deleteElement(field.parentNode);
                  }
                })();
              })();
              // SHiFT Codes
              (function () {
                /** SHiFT Code Platform Data */
                const platformData = ShiftCodesTK.shift.platforms;
                /** The SHiFT Codes*/
                const shiftCodes = shiftCodeData.codes.shift_codes;
                /** The supported platforms*/
                const platforms = shiftCodeData.codes.platforms;
                /** The SHiFT Code Section Template */
                const codeSectionTemplate = dom.find.child(shiftCodeSections.body, 'class', 'shift-code');

                for (let familyID in shiftCodes) {
                  let familyCode = shiftCodes[familyID];
                  let familyPlatforms = platforms[familyID];

                  if (familyCode && familyPlatforms) {
                    let section = edit.copy(codeSectionTemplate);

                    // SHiFT Code Family
                    (function () {
                      const content = (function () {
                        // Universal SHiFT Codes
                        if (familyID == 'universal') {
                          return {
                            title: 'Universal SHiFT Code',
                            tooltip: 'This SHiFT Code can be redeemed for all supported platforms'
                          };
                        }
                        // Individual SHiFT Codes
                        else {
                          let familyData = platformData[familyID];

                          if (familyData) {
                            let displayName = familyData.display_name;

                            if (displayName) {
                              return {
                                title: `${displayName} SHiFT Code`,
                                tooltip: `This SHiFT Code can be redeemed for <strong>${displayName}</strong> platforms`
                              };
                            }
                          }
                        }
                      })();
                      const field = dom.find.child(section, 'class', 'platform-family');
                      const tooltip = field.nextElementSibling;

                      field.innerHTML = content.title;
                      updateLabel(field, content.tooltip.replace(new RegExp('\\<strong(?:\\/){0,1}\\>', 'g'), ''), [ 'aria' ]);
                      tooltip.innerHTML = content.tooltip;
                      // layersObj.setupLayer(tooltip);
                    })();
                    // SHiFT Code Platform List
                    (function () {
                      const platforms = (function () {
                        let platformList = [];

                        // Universal SHiFT Codes
                        if (familyID == 'universal') {
                          // Loop through all supported platform families
                          let platformFamilyIDList = Object.keys(platformData);

                          for (let platformListFamilyID of platformFamilyIDList) {
                            let platformListFamilyData = platformData[platformListFamilyID];

                            if (platformListFamilyData) {
                              // Loop through supported platforms
                              let platformListFamilyPlatforms = platformListFamilyData.platforms

                              if (platformListFamilyPlatforms) {
                                for (let platformListPlatformID in platformListFamilyPlatforms) {
                                  // Platform is supported
                                  if (familyPlatforms.indexOf(platformListPlatformID) != -1) {
                                    let platformListPlatformData = platformListFamilyPlatforms[platformListPlatformID];
        
                                    if (platformListPlatformData) {
                                      let platformListPlatformDisplayName = platformListPlatformData.display_name;
        
                                      if (platformListPlatformDisplayName) {
                                        platformList.push(platformListPlatformDisplayName);
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                        // Individual SHiFT Codes
                        else {
                          // Loop through provided platforms
                          for (let platform of familyPlatforms) {
                            let familyData = platformData[familyID];

                            if (familyData) {
                              let familyPlatforms = familyData.platforms;

                              if (familyPlatforms) {
                                let familyPlatformData = familyPlatforms[platform];

                                if (familyPlatformData) {
                                  let familyPlatformDisplayName = familyPlatformData.display_name;

                                  if (familyPlatformDisplayName) {
                                    platformList.push(familyPlatformDisplayName);
                                  }
                                }
                              }
                            }
                          }
                        }

                        return platformList;
                      })();
                      const platformList = dom.find.child(section, 'class', 'platform-list');
                      const platformLabelTemplate = dom.find.child(platformList, 'tag', 'li');

                      for (let platform of platforms) {
                        const platformLabel = edit.copy(platformLabelTemplate);
                        const platformLabelContainer = dom.find.child(platformLabel, 'class', 'platform');
                        const platformLabelContent = dom.find.child(platformLabelContainer, 'tag', 'span');
                        const platformLabelTooltip = dom.find.child(platformLabel, 'class', 'tooltip');
                        const tooltipText = `This SHiFT Code can be redeemed for <strong>${platform}</strong>.`;
                        
                        updateLabel(platformLabel, tooltipText.replace(new RegExp('\\<(?:\\/){0,1}strong\\>', 'g'), ''), [ 'aria' ]);
                        edit.class(platformLabelContainer, 'add', shiftCodeData.properties.game_id);
                        platformLabelContent.innerHTML = platform;
                        platformLabelTooltip.innerHTML = tooltipText;

                        platformLabel = platformList.appendChild(platformLabel);
                        // layersObj.setupLayer(dom.find.child(platformLabel, 'class', 'layer'));
                      }

                      deleteElement(platformLabelTemplate);
                    })();
                    // SHiFT Code
                    (function () {
                      const field = dom.find.child(section, 'class', 'code');

                      field.innerHTML = familyCode;
                      edit.class(dom.find.parent(field, 'class', 'content'), 'add', shiftCodeData.properties.game_id);
                    })();
                    // Copy to Clipboard Button
                    (function () {
                      const button = dom.find.child(section, 'class', 'copy-to-clipboard');

                      edit.class(button, 'add', shiftCodeData.properties.game_id);
                    })();

                    codeSectionTemplate.insertAdjacentElement('beforebegin', section);
                  }
                }

                // SHiFT Code Usage Link
                (function () {
                  const codeUsage = dom.find.child(shiftCodeSections.body, 'class', 'shift-code-usage');
                  const inGame = dom.find.child(codeUsage, 'class', 'in-game');

                  inGame.href += `/${shiftCodeData.properties.game_id}`;
                })();

                deleteElement(codeSectionTemplate);
              })();
            })();
            // Footer
            (function () {
              const footer = dom.find.child(shiftCodeSections.body, 'class', 'footer');

              // Actions 
              (function () {
                let actions = {};
                    actions.container = dom.find.child(footer, 'class', 'actions');
                    actions.share = dom.find.child(actions.container, 'class', 'share');
                    actions.redeem = dom.find.child(actions.container, 'class', 'redeem');
                    actions.optionsMenu = dom.find.child(actions.container, 'class', 'options-menu');
      
                // Active SHiFT Code
                if (shiftCodeData.states.code.isActive) {
                  // Share Form
                  (function () {
                    const shareForm = dom.find.child(actions.share, 'tag', 'form');
                    const codeIDField = formsObj.getField(shareForm, 'share_link');

                    formsObj.updateField(codeIDField, `https://${window.location.host}/${shiftCodeData.properties.game_id}?code=${shiftCodeData.properties.code.id}`, { updateDefault: true });
                  })(); 
                  // Redeem Form
                  (function () {
                    const redeemForm = dom.find.child(actions.redeem, 'tag', 'form');
                    const bindings = {
                      code: shiftCodeData.properties.code.hash,
                      action: shiftCodeData.states.user.hasRedeemed ? 'remove' : 'redeem'
                    };
  
                    for (let binding in bindings) {
                      let bindingValue = bindings[binding];
                      let field = formsObj.getField(redeemForm, binding);
  
                      if (field) {
                        if (Array.isArray(field)){
                          formsObj.updateField(field[0], bindingValue);
                        }
                        else {
                          formsObj.updateField(field, bindingValue);
                        }
                      }
                    }
                  })();
                }
                // Inactive SHiFT Code
                else {
                  isDisabled(dom.find.child(actions.share, 'tag', 'button'), true);
                  isDisabled(dom.find.child(actions.redeem, 'tag', 'button'), true);
                  // updateLabel(actions.redeem, 'This SHiFT Code has Expired.', [ 'aria', 'tooltip' ]);
                }
              })();
              // Info
              (function () {
                let info = {};
                    info.container = dom.find.child(footer, 'class', 'code-info');
                    info.id = dom.find.child(info.container, 'class', 'id');
                    info.lastUpdate = dom.find.child(info.container, 'class', 'last-update');
                    info.owner = dom.find.child(info.container, 'class', 'owner');
                
                /** 
                 * Update the value of a given Info Field
                 * 
                 * @param {string} infoName The name of the field that is to be updated.
                 * @param {string} infoValue The new value of the field.
                 * @param {string} infoLabel The alternative text label of the field.
                 */
                function updateInfoValue (infoName, infoValue, infoLabel = false) {
                  let value = dom.find.child(info[infoName], 'tag', 'dd');

                  value.innerHTML = infoValue;

                  if (infoLabel) {
                    updateLabel(value, infoLabel, [ 'tooltip' ]);
                  }
                }

                // SHiFT Code ID
                (function () {
                  const ID = shiftCodeData.properties.code.id;

                  updateInfoValue('id', ID, `SHiFT Code<br><i>#${ID}</i>`);
                })();
                // SHiFT Code Last Update
                (function () {
                  shiftObj.updateShiftCodeTimestamp(shiftCodePanel, shiftCodeData.info.last_update);
                })();
                // The SHiFT Code Owner
                (function () {
                  // At this time, don't display username unless the user has Edit Permission
                  if (!shiftCodeData.states.user.canEdit) {
                    deleteElement(info.owner);
                  }
                  else {
                    let username = shiftCodeData.properties.owner.username;
                    let userID = shiftCodeData.properties.owner.id;

                    updateInfoValue('owner', username, `${username}<br><i>#${userID}</i>`);
                  }
                })();
              })();
            })();
            // Options Menu
            (function () {
              const menus = dom.find.children(shiftCodePanel, 'class', 'shift-code-options-menu');

              for (let menu of menus) {
                let pieces = {};
                  (function () {
                    pieces.codeID = dom.find.child(menu, 'class', 'code-id');
                    pieces.share = dom.find.child(menu, 'attr', 'data-value', 'share');
                    pieces.report = dom.find.child(menu, 'attr', 'data-value', 'report');
                    pieces.editActions = dom.find.child(menu, 'class', 'edit-actions');
                    pieces.visibilityForm = dom.find.child(pieces.editActions, 'attr', 'data-form-name', 'toggle_shift_code_visibility_form');
                    pieces.makePublic = dom.find.child(pieces.visibilityForm, 'attr', 'data-value', 'make_public');
                    pieces.makePrivate = dom.find.child(pieces.visibilityForm, 'attr', 'data-value', 'make_private');
                    pieces.deleteForm = dom.find.child(pieces.editActions, 'attr', 'data-form-name', 'delete_shift_code_form');
                  })();

                edit.attr(menu, 'add', 'data-code-id', shiftCodeIDs.codeID);
                pieces.codeID.innerHTML = shiftCodeIDs.codeID;

                if (shiftCodeData.states.user.isOwner) {
                  isDisabled(pieces.report);
                  updateLabel(pieces.report, 'You cannot report your own SHiFT Code.', [ 'aria', 'tooltip' ]);
                }
                // User has Edit Permission
                if (shiftCodeData.states.user.canEdit) {
                  // Update Visibility Buttons
                  (function () {
                    const inactiveButton = shiftCodeData.properties.code.state == 'active'
                                      ? pieces.makePublic
                                      : pieces.makePrivate;
                    const field = formsObj.getField(pieces.visibilityForm, 'code_id');
  
                    formsObj.updateField(field, shiftCodeIDs.codeID);
                    // formsObj.toggleField(inactiveButton, { hidden: true, disabled: true });
                                      
                    isHidden(inactiveButton, true);
                    isDisabled(inactiveButton, true);
                  })();
                  // Update Delete Button
                  (function () {
                    const field = formsObj.getField(pieces.deleteForm, 'code_id');

                    formsObj.updateField(field, shiftCodeIDs.codeID, { updateDefault: true });
                  })(); 
                }
                // User does not have Edit Permission
                else {
                  deleteElement(pieces.editActions);
                }
              }
            })();
            // Final Configuration
            (function () {
              // Layers
              ShiftCodesTK.layers.setupChildLayers(shiftCodePanel);
              // Redeemed SHiFT Code
              if (shiftCodeData.states.user.hasRedeemed) {
                shiftObj.redeemShiftCode(shiftCodePanel, true);
              }
            })();

            return shiftCodePanel;
          }
          catch (error) {
            console.error(`local.shift.createShiftCodePanel Error: ${error}`);
            return false;
          }
        },
        /**
         * Retrieves the SHiFT Codes Result Set
         * 
         * @returns {boolean} Returns **true** if the request has been sent, or **false** if an error occurred.
         */
        fetchShiftCodes () {
          const shiftObj = this;
          
          shiftObj.toggleControls(false);
          shiftObj.updateOverlay({
            overlay: true,
            spinner: true,
            error: false
          });
          lpbUpdate(50, true, { start: 15 });

          shiftObj.updateCheck.stop();
          shiftObj.clearShiftCodes();

          // Dispatch the request
          // setTimeout(() => {
          //   // if (shiftObj.request && shiftObj.request.readyState != 4) {
          //   //   console.warn('Aborted SHiFT Code Request.');
          //   //   shiftObj.request.abort();
          //   // }
  
          //   // const requestObject = newAjaxRequest({
          //   //   file: '/assets/requests/get/shift/codes',
          //   //   params: shiftCodeParams,
          //   //   callback: handleFetchShiftCodesResponse
          //   // });
          //   requestID = request('FetchShiftCodes');
          //   shiftObj.request = requestID;
          // }, 50);

          return true;
        }
      };

      // Startup
      (function () {
        const shiftObj = ShiftCodesTK.local.shift;
        const requestsObj = ShiftCodesTK.requests;

        // Sync SHiFT Properties
        (function () {
          // Page Properties
          (function () {
            const shiftCodeList = shiftObj.locations.shiftCodeList;
            const pageProps = tryJSONParse(dom.get(shiftCodeList, 'attr', 'data-shift'));
  
            if (pageProps) {
              for (let prop in shiftObj.props) {
                let pageProp = pageProps[prop];
                let storedProp = shiftObj.props[prop];

                if (pageProp !== undefined) {
                  if (pageProp != storedProp) {
                    shiftObj.setResultProp(prop, pageProp);
                    storedProp.defaultValue = pageProp;
                  }
                  if (pageProps.lockedProperties && pageProps.lockedProperties.indexOf(prop) != -1) {
                    shiftObj.props[prop].isLocked = true;
                  }
                }
              }

              // edit.attr(shiftCodeList, 'remove', 'data-shift');
            }
          })();

          // Query Properties
          // shiftObj.syncQueryParams('var');
          // requestsObj.savedRequests.syncQueryParams('FetchShiftCodes', 'params');
        })(); 
        // Setup Requests
        (function () {
          // FetchShiftCodes
          (function () {
            function handleFetchShiftCodesResponse (responseData, requestData) {
              if (responseData && responseData.status_code == 200) {
                lpbUpdate(85, true, { start: 50 });
  
                setTimeout(() => {
                  const shiftCodes = responseData.payload.shift_codes;
  
                  if (shiftCodes) {
                    const flagCounts = shiftCodes.flag_counts;
    
                    // Update SHiFT Components
                    if (flagCounts && shiftObj.stats != flagCounts) {
                      shiftObj.stats = flagCounts;
                      shiftObj.syncShiftComponents();
                    }
  
                    // Update Result List
                    if (shiftCodes && shiftCodes.length > 0) {
                      // Create and Push SHiFT Code Dropdown Panels
                      for (let shiftCodeIndex = 0; shiftCodeIndex < shiftCodes.length; shiftCodeIndex++) {
                        const shiftCode = shiftCodes[shiftCodeIndex];
                        const shiftCodePanel = shiftObj.createShiftCodePanel(shiftCode);
  
                        if (shiftCodePanel) {
                          shiftCodePanel.style.animationDelay = `${shiftCodeIndex * 0.2}s`;
                          shiftObj.locations.shiftCodeList.appendChild(shiftCodePanel);
                        }
                      }
    
                      shiftObj.updateOverlay({
                        overlay: false
                      });
                    }
                    // No SHiFT Codes Found
                    else {
                      shiftObj.updateOverlay({
                        overlay: true,
                        spinner: false,
                        error: true
                      });
                    }
    
                    shiftObj.updateCheck.start();
                    
                    setTimeout(function () {
                      shiftObj.toggleControls(true);
                      lpbUpdate(100);
                    }, 500);
                  }
                }, 50);
              }
              else {
                lpbUpdate(100);
                shiftObj.updateOverlay({
                  overlay: true,
                  spinner: false,
                  error: true
                });
                ShiftCodesTK.toasts.newToast({
                  settings: {
                    template: 'fatalException'
                  },
                  content: {
                    title: 'SHiFT Code Downloading Error',
                    body: 'We could not retrieve any SHiFT Codes due to an error. Please refresh the page and try again.'
                  }
                });
              }
              // if (requestID == shiftObj.request) {
              // }
              // else {
              //   console.warn('Expired Request ID. Ignoring.');
              // }
            }
  
            const requestName = 'FetchShiftCodes';
            const requestConfiguration = {
              type: 'pagination',
              request: {
                path: '/assets/requests/get/shift/codes',
                callback: handleFetchShiftCodesResponse,
                params: {
                  get_result_set_data: true,
                  get_flag_counts: true,
                },
              },
              controls: {
                sortAndFilter: dom.find.id('shift_header_sort_filter_form'),
                pager: dom.find.id('shift_code_pager'),
                controlsState: [
                  dom.find.id('shift_header_add'),
                  dom.find.id('shift_header_sort_filter'),
                  dom.find.id('shift_code_pager')
                ]
              },
              syncParameters: 'replace'
            };
            const shiftCodeList = shiftObj.locations.shiftCodeList;
            const pageProps = tryJSONParse(dom.get(shiftCodeList, 'attr', 'data-shift'));
            const requestParameters = (function () {
              const properties = {
                /** @property `null|string` The *GameID* of a specific game to filter by, or **null** to not filter by game. */
                game: null,
                /** 
                 * @property `array` The list of *Code Status Filters*
                 * - Possible values include *"active"*, *"expired"*, & *"hidden"*
                 **/
                status: [ 'active' ],
                /** @property `null|string` The *Platform ID* of the platform to filter by, or **null** to not filter by platform. */
                platform: null,
                /** @property `null|string` The *User ID* of the user to filter by, or **null** to not filter by platform. */
                owner: null,
                /** @property `null|string` The *Code ID* of the SHiFT Code to search for, or **null** to not search for a particular SHiFT Code. */
                code: null,
                /** @property `"default"|"newest"|"oldest"` Indicates how the returned SHiFT Codes are to be ordered. */
                order: 'default',
                /** @property `int` Indicates how many SHiFT Codes are to be returned per result set. */
                limit: 10,
                /** @property `int` Indicates the current page number of results. */
                page: 1
              };
  
              // Page Properties
              (function () {
                const pageProps = tryJSONParse(dom.get(shiftCodeList, 'attr', 'data-shift'));
      
                if (pageProps) {
                  for (let prop in properties) {
                    let pageProp = pageProps[prop];
                    let storedProp = shiftObj.props[prop];
  
                    if (pageProp !== undefined) {
                      if (pageProp != storedProp) {
                        properties[prop] = pageProp;
                      }
                    }
                  }
  
                  edit.attr(shiftCodeList, 'remove', 'data-shift');
                }
              })();
  
              return properties;
            })();
    
            requestsObj.savedRequests.saveRequest(requestName, requestConfiguration, requestParameters, pageProps.readOnlyProperties ? pageProps.readOnlyProperties : []);
            // requestsObject.request(requestName);
          })();
          // GetShiftCode
          (function () {
            const requestName = 'GetShiftCode';
            const requestConfiguration = {
              request: {
                path: '/assets/requests/get/shift/codes',
                params: {
                  game: null,
                  status: [ 'active', 'expired', 'hidden' ],
                  limit: 1,
                  page: 1
                }
              }
            };
            const requestParameters = {
              /** 
               * @property `array` The list of *Code Status Filters*
               * - Possible values include *"active"*, *"expired"*, & *"hidden"*
               **/
              status: [ 'active', 'expired', 'hidden' ],
              /** @property `null|string` The *User ID* of the user to filter by, or **null** to not filter by platform. */
              owner: null,
              /** @property `null|string` The *Code ID* of the SHiFT Code to search for. */
              code: null
            }
            
            requestsObj.savedRequests.saveRequest(requestName, requestConfiguration, requestParameters);
            // requestsObject.request(requestName);
          })();
        })();
        // Initial SHiFT Code Listing
        (function () {
          const requestName = 'FetchShiftCodes';
          const hasMatchingHash = addHashListener('shift_code_\d{12}$', function (hash) {
            requestsObj.savedRequests.setRequestParameter(requestName, 'page', 1);
            requestsObj.savedRequests.setRequestParameter(requestName, 'code', hash.replace('#shift_code_', ''));
            requestsObj.request(requestName);
            requestsObj.savedRequests.setRequestParameter(requestName, 'code', null);
          });

          if (!hasMatchingHash) {
            // shiftObj.fetchShiftCodes();
            requestsObj.request(requestName);
          }
        })();
        // Initial SHiFT Code Update Timestamps
        (function () {
          let now = node_modules.dayjs.utc().valueOf();

          shiftObj.updateCheck.updateStats = {
            first: now,
            last: now
          };
        })();
        // Event Listeners
        (function () {
          // Request Dispatch
          window.addEventListener('tkRequestsRequestDispatched', (event) => {
            const requiredRequestPath = ShiftCodesTK.requests.savedRequests.getRequest('FetchShiftCodes').configuration.request.path;
            const eventRequestPath = event.requestEventData.request.requestEventData.requestProperties.path;

            if (eventRequestPath.indexOf(requiredRequestPath) != -1) {
              shiftObj.toggleControls(false);
              shiftObj.updateOverlay({
                overlay: true,
                spinner: true,
                error: false
              });
              lpbUpdate(50, true, { start: 15 });

              shiftObj.updateCheck.stop();
              shiftObj.clearShiftCodes();
            }
          });
          // Sort/Filter Slideout
          (function () {
            const slideout = dom.find.child(shiftObj.locations.shiftHeader, 'class', 'slideout');

            // Toggle
            (function () {
            const button = dom.find.id('shift_header_sort_filter');

            if (button) {
              button.addEventListener('click', (event) => {
                isHidden(slideout);
              });

              // Form
              (function () {
                const form = dom.find.child(slideout, 'tag', 'form');
    
                if (form) {
                  formsObj.getField(form, 'game').addEventListener('tkFormsFieldCommit', (event) => {
                    const formEventData = event.formEventData;
                    const newValue = formEventData.fieldValue;
                    const platforms = formsObj.getField(form, 'platform_filter');

                    for (let platform of platforms) {
                      if (newValue === "" || ShiftCodesTK.shift.games[newValue].support.unsupported.platforms.indexOf(platform.value) != -1) {
                        isDisabled(platform, newValue !== "");
                      }
                    }

                  });
                  form.addEventListener('tkFormsFormAfterSubmit', (event) => {
                    const formEventData = event.formEventData;
                    console.log(formEventData);
                  });
                }
                else {
                  console.warn('local.shift Setup Warning: Sort/Filter Form is missing');
                }
              })();
            }
            else {
              console.warn('local.shift Setup Warning: Sort/Filter Header Button is missing');
            }
            })();
          })();
          // Click Listeners (Update Indicator)
          window.addEventListener('click', (event) => {
            // Update Indicator
            if (dom.has(event.target, 'class', 'update-indicator', null, true)) {
              shiftObj.updateCheck.update();
            }
          });
          // Redeem SHiFT Code Hook
          window.addEventListener('tkFormsFormAfterSubmit', (event) => {
            const formEventData = event.formEventData;
            
            if (formEventData.formProps.info.name == 'redeem_shift_code_form') {
              const formData = formEventData.formData;
  
              /** Trigger a response error toast */
              function redemptionErrorToast () {
                event.preventDefault();
                return ShiftCodesTK.toasts.newToast({
                  setting: {
                    duration: 'long'
                  },
                  content: {
                    title: `Failed to ${formData.action == 'redeem' ? 'Redeem' : 'Unredeem'} SHiFT Code`,
                    body: `SHiFT Code (${formData.code}) could not be ${formData.action == 'redeem' ? 'redeemed' : 'unredeemed'} due to an error. Please try again.`
                  }
                })
              }
    
              if (formEventData.formResponseData) {
                if (formEventData.formResponseData.statusCode == 200) {
                  setTimeout(() => {
                    const matchingShiftCodes = dom.find.children(shiftObj.locations.shiftCodeList, 'class', 'shift-code');
  
                    for (let code of matchingShiftCodes) {
                      if (dom.has(code, 'attr', 'data-hash', formData.code)) {
                        shiftObj.redeemShiftCode(code);
                      }
                    }
    
                  }, 500);
  
                  return true;
                }
              }
    
              redemptionErrorToast();
              return false;
            }
          });
          // SHiFT Code Options Menu Hooks
          layersObj.addLayerListener('shift_code_options_menu', (action, dropdown) => {
            const actionValue = dom.get(action, 'attr', 'data-value');
            const codeID = dom.get(dropdown, 'attr', 'data-code-id');

            /**
             * Updates the state of the action button
             * 
             * @param {boolean} state The new state of the action button
             * - **true**: Displays the *spinner*, marks the button as *pressed*, and *disables* the button. 
             * - **false**: Hides the *spinner*, marks the button as *unpressed*, and *enables* the button. 
             */
            function updateAction (state) {
              edit.class(action, state ? 'add' : 'remove', 'spinning');
              edit.attr(action, 'update', 'aria-pressed', state);
              isDisabled(action, state);
            }

            if (codeID) {
              updateAction(true);

              setTimeout(() => {
                // Edit SHiFT Code
                if (actionValue == 'edit') {
                  function editCodeError (errorMessage = 'This SHiFT Code could not be edited due to an error. Please try again later.') {
                    lpbUpdate(100);
                    edit.class(action, state ? 'add' : 'remove', 'spinning');
                    toastsObj.newToast({
                      settings: {
                        duration: 'long',
                        template: 'fatalException'
                      },
                      content: {
                        title: 'Failed to edit SHiFT Code',
                        body: errorMessage
                      }
                    });
                  }

                  const shiftCode = (function () {
                    const lastResult = requestsObj.savedRequests.getResultData('FetchShiftCodes').lastResult;
  
                    if (lastResult && lastResult.resultStatusCode == 200 && lastResult.resultResponseObject) {
                      const shiftCodes = lastResult.resultResponseObject.payload.shift_codes;
  
                      for (let shiftCode of shiftCodes) {
                        let shiftCodeID = shiftCode.properties.code.id;
  
                        if (shiftCodeID == codeID) {
                          return shiftCode;
                        }
                      }
                    }
  
                    return false;
                  })();

                  lpbUpdate(85, true, { start: 50 });
                  
                  console.info(shiftCode);

                  if (shiftCode) {
                    if (shiftCode.states.user.canEdit) {
                      const shiftCodePanel = dom.find.id(`shift_code_${codeID}`);
                      const editView = dom.find.child(shiftCodePanel, 'class', 'view edit');
                      const editForm = dom.find.child(editView, 'tag', 'form');
                      const formBindings = (function () {
                        let bindings = {};
  
                        bindings.code_id = codeID;
                        bindings.reward = shiftCode.info.reward;
                        bindings.game_id = shiftCode.properties.game_id;
  
                        // Source
                        (function () {
                          const source = shiftCode.info.source;
  
                          bindings.source_type = source.type;
  
                          if (source.type == 'online') {
                            bindings.source_url = source.value;
                            bindings.source_string = '';
                          }
                          else if (source.type == 'physical') {
                            bindings.source_url = '';
                            bindings.source_string = source.value;
                          }
                          else if (source.type == 'none') {
                            bindings.source_url = '';
                            bindings.source_string = 'N/A';
                          }
                        })();
                        // Release Date
                        (function () {
                          const releaseDate = shiftCode.info.release_date;
  
                          if (releaseDate.type == 'date') {
                            const date = node_modules.dayjs(releaseDate.value);
  
                            if (date) {
                              bindings.release_date = date.format('Y-MM-DD');
                              return true;
                            }
                          }
  
                          bindings.release_date = '';
                          return false;
                        })(); 
                        // Expiration Date
                        (function () {
                          const expirationDate = shiftCode.info.expiration_date;
                          const timezone = shiftCode.info.timezone;
  
                          bindings.expiration_date_type = expirationDate.type;
  
                          if ([ 'through', 'until' ].indexOf(expirationDate.type) != -1) {
                            const date = node_modules.dayjs.tz(expirationDate.value, timezone);
  
                            if (date) {
                              bindings.expiration_date_value_date = date.format('Y-MM-DD');
                              bindings.expiration_date_value_time = date.format('HH:mm:SS');
                              bindings.expiration_date_value_tz = timezone;
                              return true;
                            }
                          }
  
                          bindings.expiration_date_value_date = '';
                          bindings.expiration_date_value_time = '';
                          bindings.expiration_date_value_tz = '';
                          return false;
                        })(); 
                        // Notes
                        bindings.notes = shiftCode.info.notes
                                         ? shiftCode.info.notes
                                         : '';
                        // SHiFT Codes & Platforms
                        (function () {
                          const platforms = shiftCode.codes.platforms;
                          const shiftCodes = shiftCode.codes.shift_codes;
                          const isUniversalCode = shiftCodes.universal !== undefined && shiftCodes.universal !== null;
  
                          bindings.codes_code_type = isUniversalCode
                                                     ? 'universal'
                                                     : 'individual';
                          bindings.codes_universal_platforms = isUniversalCode
                                                               ? platforms.universal
                                                               : '';
                          bindings.codes_universal_code = isUniversalCode
                                                          ? shiftCodes.universal
                                                          : '';
  
                          for (let familyID of Object.keys(ShiftCodesTK.shift.platforms)) {
                            bindings[`codes_individual_${familyID}_platforms`] = !isUniversalCode
                                                                                ? platforms[familyID]
                                                                                : '';
                            bindings[`codes_individual_${familyID}_code`] = !isUniversalCode
                                                                           ? shiftCodes[familyID]
                                                                           : '';
                          }
                        })();
  
                        return bindings;
                      })();
  
                      console.log(formBindings);
                      for (let fieldName in formBindings) {
                        const field = formsObj.getField(editForm, fieldName);
                        const bindingValue = formBindings[fieldName];
  
                        if (Array.isArray(field)) {
                          field = field[0];
                        }
  
                        formsObj.updateField(field, bindingValue, { updateDefault: true });
                      }
  
                      formsObj.resetForm(editForm);
                      multiView_update(editView);
                      updateAction(false);
                      layersObj.toggleLayer(dropdown, false);
                      lpbUpdate(100);
                      return true;
                    }
                    else {
                      editCodeError('You do not have permission to edit this SHiFT Code. If you had access before, it may have been revoked. Please refresh the page and try again.');
                      return false;
                    }
                  }

                  editCodeError();
                  return false;
                }
              }, 100);
            }
          }, [ 'click' ]);
          // Edit View Reset Button Switch View
          window.addEventListener('tkFormsFormBeforeReset', (event) => {
            const formEventData = event.formEventData;

            if (formEventData.form.id.indexOf('update_shift_code_form') != -1) {
              const shiftCodePanel = dom.find.id(`shift_code_${formEventData.formData.code_id}`);
              const codeView = dom.find.child(shiftCodePanel, 'class', 'view code');
  
              multiView_update(codeView);
            }
          });
          // Delete Button
          window.addEventListener('tkFormsFormBeforeSubmitConfirmation', (event) => {
            const formEventData = event.formEventData;

            if (formEventData.formProps.info.name == 'delete_shift_code_form') {
              const shiftCodeResponseData = formEventData.formResponseData;
  
              function deleteCodeError (errorMessage = 'This SHiFT Code could not be deleted due to an error. Please try again later.') {
                lpbUpdate(100);
                toastsObj.newToast({
                  settings: {
                    duration: 'long',
                    template: 'fatalException'
                  },
                  content: {
                    title: 'Could not delete SHiFT Code',
                    body: errorMessage
                  }
                });
              }
  
              event.preventDefault();
              
              lpbUpdate(75, true);
  
              const codeID = formEventData.formData.code_id;
              const shiftCode = (function () {
                const lastResult = requestsObj.savedRequests.getResultData('FetchShiftCodes').lastResult;
  
                if (lastResult && lastResult.resultStatusCode == 200 && lastResult.resultResponseObject) {
                  const shiftCodes = lastResult.resultResponseObject.payload.shift_codes;
  
                  for (let shiftCode of shiftCodes) {
                    let shiftCodeID = shiftCode.properties.code.id;
  
                    if (shiftCodeID == codeID) {
                      return shiftCode;
                    }
                  }
                }
  
                return false;
              })();
  
              if (shiftCode) {
                if (shiftCode.states.user.canEdit) {
                  let modal = formEventData.confirmationModal;
                  const modalID = 'form_submit_confirmation_modal';
                  const modalProperties = (function () {
                    const body = edit.copy(dom.find.id(`delete_shift_code_confirmation_modal_template`));
  
                    return {
                      mode: 'update',
                      showModal: false,
                      title: `Delete SHiFT Code: "${shiftCode.info.reward}"?`,
                      body: body,
                      id: modalID,
                      actions: {
                        approve: {
                          name: 'Delete SHiFT Code',
                          tooltip: 'Permanently delete this SHiFT Code. This action is irreversable',
                          color: 'danger'
                        }
                      }
                    };
                  })();
  
                  modal = modalsObj.addConfirmationModal(modalProperties);
  
                  // Update info
                  (function () {
                    const dateFormat = 'MMMM DD, YYYY hh:mm A [UTC]';
                    const bindings = {
                      id: {
                        primary: shiftCode.properties.code.id,
                        secondary: 'SHiFT Code #'
                      },
                      owner: {
                        primary: shiftCode.properties.owner.username,
                        secondary: `#${shiftCode.properties.owner.id}`
                      },
                      created: (function () {
                        let date = shiftCode.info.creation_date;
                        let dateObj = node_modules.dayjs(date);
  
                        return {
                          primary: ucWords(dateObj.fromNow()),
                          secondary: dateObj.format(dateFormat)
                        }
                      })(),
                      updated: (function () {
                        let date = shiftCode.info.last_update;
                        let dateObj = node_modules.dayjs(date);
  
                        return {
                          primary: ucWords(dateObj.fromNow()),
                          secondary: dateObj.format(dateFormat)
                        }
                      })()
                      // expiration: (function () {
                      //   let date = shiftCode.info.expiration_date.value;
                      //   const expType = shiftCode.info.expiration_date.type;
  
                      //   if (expType == 'date') {
                      //     const dateObj = node_modules.dayjs(date);
  
                      //     return {
                      //       primary: ucWords(dateObj.fromNow()),
                      //       secondary: dateObj.format(dateFormat)
                      //     }
                      //   }
                      //   else {
                      //     return {
                      //       primary: "N/A",
                      //       secondary: (function () {
                      //         if (expType == 'infinite') {
                      //           return 'Never Expires';
                      //         }
                      //         else if (expType == 'none') {
                      //           return 'None Provided';
                      //         }
                      //       })()
                      //     }
                      //   }
                      // })()
                    };
                    const info = dom.find.child(modal, 'class', 'info');
  
                    for (let binding in bindings) {
                      const values = bindings[binding];
                      const group = dom.find.child(info, 'class', `section ${binding}`);
                      const groupValue = dom.find.child(group, 'tag', 'dd');
  
                      for (let sectionName in values) {
                        const section = dom.find.child(groupValue, 'class', sectionName);
                        const sectionValue = values[sectionName];
  
                        section.innerHTML = sectionValue;
  
                        if ([ '', 'N/A' ].indexOf(sectionValue) != -1) {
                          edit.class(section, 'add', 'inactive');
                        }
                        if (sectionName == 'secondary' && [ 'created', 'expiration' ].indexOf(binding) != -1) {
                          edit.attr(section, 'update', 'aria-label', `(${sectionValue})`);
                        }
                      }
                    }
                  })();
  
                  dropdownPanelSetup(dom.find.child(modal, 'class', 'dropdown-panel'));
                  edit.attr(modal, 'add', 'data-code-id', codeID);
                  modalsObj.toggleModal(modal, true);
                  return true;
                }
                else {
                  editCodeError('You do not have permission to delete this SHiFT Code. If you had access before, it may have been revoked. Please refresh the page and try again.');
                  return false;
                }
              }
  
              deleteCodeError();
              return false;
            }
          });
        })();
      })();
    }
  }, 250); 
})();