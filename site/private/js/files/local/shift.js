/**
 * Statistics regarding the retrieved list of SHiFT Codes
 */
var shiftStats = {
  'total': 0,
  'new': 0,
  'expiring': 0
};
/**
 * Properties regarding which SHiFT Codes are to be retrieved
 */
var shiftProps = {
  'game'  : 'all',
  'owner' : false,
  'code'  : false,
  'filter': [ 'active' ],
  'order' : 'default',
  'limit' : 10,
  'offset': 0
};
/**
 * SHiFT Code update poller
 */
var shiftUpdates = {
  /**
   * Interval properties and methods
   */
  interval: {
    id: 0,
    /**
     * Start the SHiFT Code update poller
     */
    set: function () {
      shiftUpdates.interval.id = setInterval(shiftUpdates.check, 60000 * 2);
    },
    /**
     * Stop the SHiFT Code update poller
     */
    clear: function () {
      clearInterval(shiftUpdates.interval.id);
    },
    /**
     * Stop and Restart the SHiFT Code update poller
     */
    restart: function () {
      shiftUpdates.clear();
      shiftUpdates.set();
    }
  },
  /**
   * First and last update check
   */
  stats: {},
  /**
   * Toggle the SHiFT Code update indicator
   * 
   * @param {boolean} newState True to display the indicator, or false to hide it
   * @param {int} count The new update count 
   */
  toggleIndicator: function (newState, count = 0) {
    let indicator = dom.find.id('shift_update_indicator');
  
    dom.find.child(indicator, 'class', 'counter').innerHTML = count;
    updateLabel(indicator, indicator.title.replace(new RegExp('\\d+'), count));

    if (newState) {
      indicator.addEventListener('click', shiftUpdates.fetch, { once: true });
      isHidden(indicator, false);
      edit.class(indicator, 'remove', 'hidden');
    }
    else {
      edit.class(indicator, 'add', 'hidden');
      
      setTimeout(function () {
        isHidden(indicator, true);
      }, 250);
    }
  },
  /**
   * Poll for SHiFT Code updates
   */
  check: function () {
    newAjaxRequest({
      file: '/assets/requests/get/shift/updates',
      params: {
        'last_check': moment.utc(shiftUpdates.stats.last_check).format(),
        'game_id': shiftProps.game
      },
      callback: function (responseText) {
        let response = tryJSONParse(responseText);

        if (response && response.statusCode == 200) {
          let count = response.payload.count;

          if (count > 0) {
            shiftUpdates.toggleIndicator(true, count);
          }
          else {
            shiftUpdates.stats.last_check = moment.utc().valueOf();
          }
        }
      }
    })
  },
  /**
   * Retrieve the recently updated SHiFT Codes
   */
  fetch: function () {
    getCodes();
    shiftUpdates.toggleIndicator(false);
    shiftUpdates.stats.last_check = moment.utc().valueOf();
  }
};

/**
 * Sync the SHiFT Control and Display components with the most recent information
 */
function syncShiftComponents () {
  let header = dom.find.id('shift_header');
  
  // Update Badges
  (function () {
    let badges = dom.find.child(header, 'class', 'section badges');

    for (let badge of dom.find.children(badges, 'class', 'badge')) {
      let type = dom.get(badge, 'attr', 'data-value');
      let displayType = ucWords(type);
      let count = shiftStats[type];
      let title = '';
  
      if (count > 0) {
        title += `${count} `;
  
        if (type == 'total') {
          title += 'SHiFT Codes Available'; 
        }
        else {
          let active = shiftProps.filter.indexOf(type) != -1;
  
          title += `${displayType} SHiFT Codes ${active ? '(Click to clear Filter)' : '(Click to Filter)'}`;
          edit.attr(badge, 'update', 'aria-pressed', active);
        }
      }
      else {
        if (type == 'total') { title = 'No SHiFT Codes Available'; }
        else                 { title = `No ${displayType} SHiFT Codes`; }
      }
  
      if (type != 'total') {
        isDisabled(badge, !(count > 0));
      }
  
      edit.class(badge, count > 0 ? 'remove' : 'add', 'inactive');
      dom.find.child(badge, 'class', 'count').innerHTML = count;
      updateLabel(badge, title, [ 'tooltip' ]);
    }
  })();
  // Update Sort/Filter
  (function () {
    let form = dom.find.id('shift_header_sort_filter_form');
    let bindings = {
      sort: shiftProps.order,
      status_filter: shiftProps.filter,
      game_filter: shiftProps.game
    };

    if (!dom.has(form, 'class', 'updated')) {
      for (let binding in bindings) {
        formUpdateField(form, binding, bindings[binding]);
      }

      edit.class(form, 'add', 'updated');
    }
  })();
  // Update Pager
  (function () {
    /**
     * SHiFT Code statistics and properties
     */
    let stats = {
      limit: shiftProps.limit,
      offset: shiftProps.offset,
      total: shiftStats.total
    };
    /**
     * Pager properties
     */
    let pagerProps = {
      now: (stats.offset / stats.limit) + 1,
      max: stats.total > 0 ? Math.ceil(stats.total / stats.limit) : 1
    };

    // Verify and Update the Pager
    (function () {
      function checkAndUpdatePager () {
        /**
         * The SHiFT Code pager
         */
        let pager = dom.find.id('shift_code_pager');

        if (pager && dom.has(pager, 'class', 'configured')) {
          return updatePagerProps(pager, pagerProps);
        }

        return false;
      }

      if (!checkAndUpdatePager()) {
        tryToRun({
          function: checkAndUpdatePager
        });
      }
    })();

  })();
}
/**
 * Redeem or Un-Redeem a given SHiFT Code
 * 
 * @param {Element|string} shiftCode The SHiFT Code that is to be redeemed. 
 * - Providing the *Code Hash* of the SHiFT Code will redeem the SHiFT Code server-side, and update the appearance of all present, matching SHiFT Code Dropdown Panels.
 * - Providing a *SHiFT Code Dropdown Panel* will not redeem the SHiFT Code server-side, and only update the apperance of the provided dropdown.
 * @param {boolean} redemptionState Indicates the new *rdemption state* of the SHiFT Code. 
 * - Passing **true** indicates that the SHiFT Code is to be *redeemed*.
 * - Passing **false** indicates that the SHiFT Code is to be *un-redeemed*.
 * @returns {boolean} Returns **true** on success and **false** on failure.
 */
function redeemShiftCode (shiftCode, redemptionState = true) {
  /** Indicates the type of SHiFT Code that was provided for the first argument. */
  let shiftCodeType = (function () {
    if (typeof shiftCode == 'string' && shiftCode.length == 12) {
      return 'code_hash';
    }
    else if (typeof shiftCode == 'object' && shiftCode.constructor.name.indexOf('Element') != -1 && dom.has(shiftCode, 'class', 'shift-code')) {
      return 'panel';
    }
    else {
      return false;
    }
  })();

  /**
   * Update the components of a SHiFT Code Dropdown Panel to reflect its new Redemption State 
   * 
   * @param {Element} panel The SHiFT Code Dropdown Panel that is being updated.
   */
  function updateComponents (panel) {
    let redeemButton = dom.find.child(panel, 'class', 'action redeem');

    edit.class(panel, redemptionState ? 'add' : 'remove', 'redeemed');

    if (redeemButton) {
      edit.attr(redeemButton, 'update', 'aria-pressed', redemptionState);
      redeemButton.innerHTML = redeemButton.innerHTML.replace(redemptionState ? 'fa-bookmark' : 'fa-check', redemptionState ? 'fa-check' : 'fa-bookmark');
      redeemButton.innerHTML = redeemButton.innerHTML.replace(redemptionState ? 'Redeem' : 'Redeemed', redemptionState ? 'Redeemed' : 'Redeem');
      updateLabel(redeemButton, redeemButton.title.replace(redemptionState ? 'Mark' : 'Un-mark', redemptionState ? 'Un-mark' : 'Mark'));
    }
  }
  /**
   * Toggle the active state of the Redemption Button for all applicable SHiFT Code Dropdown Panels
   * 
   * @param {array} panels An array of SHiFT Code Dropdown Panels to be updated.
   * @param {boolean} newState The new Redemption State of the dropdown panel.
   * - Passing **true** indicates that the redemption buttons are to be *enabled*.
   * - Passing **false** indicates that the redemption buttons are to be *disabled*
   */
  function updateRedemptionButtons (panels, newState) {
    for (let panel of panels) {
      let redeemButton = dom.find.child(panel, 'class', 'action redeem');

      if (redeemButton) {
        isDisabled(redeemButton, !newState);
        edit.class(redeemButton, newState ? 'remove' : 'add', 'in-progress');
      }
    }
  }

  // Parameter Issues
  if (!shiftCodeType) {
    console.error(`redeemShiftCode Error: Provided SHiFT Code is not a valid Code Hash or SHiFT Code Dropdown Panel.`);
    return false;
  }

  if (shiftCodeType == 'panel') {
    updateComponents(shiftCode);
  }
  else if (shiftCodeType == 'code_hash') {
    let matchingShiftCodes = dom.find.children(dom.find.id('shift_code_list'), 'attr', 'data-code-hash', shiftCode);

    if (!matchingShiftCodes) {
      console.error(`redeemShiftCode Error: No present SHiFT Code Dropdown Panels were found with a Code Hash of "${shiftCode}". This indicates an invalid Code Hash or illegal redemption operation.`);
      return false;
    }

    // Disable the Redemption Buttons
    updateRedemptionButtons(matchingShiftCodes, false);
    // Perform the POST Request
    newAjaxRequest({
      type: "POST",
      file: '/assets/requests/post/shift/redeem',
      params: {
        'code': shiftCode,
        'action': redemptionState ? 'add' : 'delete'
      },
      requestHeader: 'form',
      callback: function (responseText) {
        let response = tryJSONParse(responseText);

        /** Trigger a response error toast */
        function responseError () {
          return newToast({
            settings: {
              template: 'exception'
            },
            content: {
              title: 'Failed to redeem SHiFT Code',
              body: 'We could not redeem this SHiFT Code due to an error. Please refresh the page and try again.'
            }
          });
        }

        if (response) {
          if (response.statusCode == 200 || response.statusCode == 201) {
            setTimeout(function () {
              updateRedemptionButtons(matchingShiftCodes, true);

              for (let matchingShiftCode of matchingShiftCodes) {
                updateComponents(matchingShiftCode);
              }

              if (redemptionState && response.payload.displayToast) {
                let toastContent = (function () {
                  let type = response.payload.toastType;

                  if (type == 1) {
                    return "This SHiFT Code has been redeemed successfully! Redeemed SHiFT Codes will be marked anytime you visit ShiftCodesTK from your current browser. If your browser cookies are deleted, your redeemed SHiFT Codes may be lost."
                  }
                  if (type == 2) {
                    return "This SHiFT Code has been redeemed successfully! Redeemed SHiFT Codes will be marked anytime you visit ShiftCodesTK as long as you are logged in."
                  }
                })();

                newToast({
                  settings: {
                    id: 'redeemed_code_toast',
                    duration: 'infinite'
                  },
                  content: {
                    icon: 'fas fa-key',
                    title: 'SHiFT Code Redeemed!',
                    body: toastContent
                  }
                });
              }

              return true;
            }, 500);
          }
          else {
            responseError();
            return false;
          }
        }
        else {
          responseError();
          return false;
        }
      }
    });
  }

  return true;
}
function updateShiftCodeTimestamp (shiftCode, timestamp = undefined) {
  let lastUpdate = dom.find.child(
                    dom.find.child(
                      dom.find.child(
                        shiftCode, 
                        'class', 
                        'code-info'
                      ), 
                      'class', 
                      'last-update'
                    ), 
                    'tag', 
                    'dd'
                    );
  let tsMoment = moment.utc(timestamp);

  lastUpdate.innerHTML = tsMoment.fromNow();
  updateLabel(lastUpdate, tsMoment.format('MMMM DD, YYYY hh:mm A [UTC]'), [ 'tooltip' ]);
}
/**
 * Retrieve a list of SHiFT Codes
 */
function retrieveCodes () {
  /** Various SHiFT Code Components */
  let shiftComps = {}
      /** The SHiFT Code Header */
      shiftComps.header = dom.find.id('shift_header');
      /** SHiFT Code Count Badges & Filter Controls */
      shiftComps.badges = {};
      shiftComps.badges.container = dom.find.child(shiftComps.header, 'class', 'section badges');
      shiftComps.badges.active = dom.find.child(shiftComps.badges.container, 'class', 'active');
      shiftComps.badges.new = dom.find.child(shiftComps.badges.container, 'class', 'new');
      shiftComps.badges.expiring = dom.find.child(shiftComps.badges.container, 'class', 'expiring');
      /** The *Add SHiFT Code* button */
      shiftComps.addCodeButton = dom.find.id('shift_header_add');
      /** The SHiFT Code Filter & Sort button */
      shiftComps.sortFilterButton = dom.find.id('shift_header_sort_filter');
      /** The SHiFT Code Result List */
      shiftComps.list = dom.find.id('shift_code_list');
      /** The SHiFT Code Result List Pager */
      shiftComps.pager = dom.find.id('shift_code_pager');

  /**
   * Toggle the state of the SHiFT Code Controls
   * 
   * @param {boolean} isActive Indicates if the SHiFT Code Controls are *enabled* or *disabled*.
   * - **True** indicates that the controls are to be *enabled*.
   * - **False** indicates that the controls are to be *disabled*.
   */
  function toggleControls (isActive) {
    /** The SHiFT Code Controls to be updated */
    let controls = [
      shiftComps.badges.new,
      shiftComps.badges.expiring,
      shiftComps.addCodeButton,
      shiftComps.sortFilterButton,
      shiftComps.pager
    ];

    for (let control of controls) {
      if (control && !dom.has(control, 'class', 'inactive')) {
        isDisabled(control, !isActive);
      }
    }
  }
  /**
   * Update various components of the SHiFT Code List Overlay
   * 
   * @param {object} settings The components that are to be updated and a **boolean** indication of if they are to be *visible* or not.
   * - `overlay` — The SHiFT Code List Overlay container
   * - `spinner` — The Loading Spinner
   * - `error` — The error message to be displayed when no SHiFT Codes were retrieved
   */
  function updateOverlay (settings) {
    /** The SHiFT Code List Overlay Components */
    let comps = {};
        comps.overlay = dom.find.id('shift_overlay');
        comps.spinner = dom.find.child(comps.overlay, 'class', 'spinner');
        comps.error = dom.find.child(comps.overlay, 'class', 'error');

    for (let comp in settings) {
      let isVisible = settings[comp];

      isHidden(comps[comp], !isVisible);
    }
  }
  /**
   * Clear the current SHiFT Code Result List
   */
  function clearList () {
    let shiftCodes = dom.find.children(shiftComps.list, 'class', 'shift-code');

    for (let i = shiftCodes.length - 1; i >= 0; i--) {
      deleteElement(shiftCodes[i]);
    }
  }
    /**
   * Generate a Dropdown Panel for a given SHiFT Code
   * 
   * @param {object} code The SHiFT Code properties object.
   * @returns {Element|false} Returns the SHiFT Code Dropdown Panel element on success, or **false** on failure.
   */
  function getShiftCodePanel (code) {
    try {
      /** The SHiFT Code Dropdown Panel */
      let panel = edit.copy(dom.find.id('shift_code_template'));
      /** Sections of the SHiFT Code Dropdown Panel */
      let panelSections = {};
          panelSections.header = dom.find.child(panel, 'class', 'header');
          panelSections.body = dom.find.child(panel, 'class', 'body active');
          panelSections.deletedBody = dom.find.child(panel, 'class', 'body deleted');
      /** The base of the SHiFT Code ID */
      let baseID = 'shift_code';
      /** The ID of the SHiFT Code */
      let codeID = `${baseID}_${code.properties.code_id}`;

      // SHiFT Code Properties
      (function () {
        let attributes = [
          'id',
          'for',
          'data-view',
          'data-target',
          'data-layer-target',
          'data-layer-targets',
          'aria-labelledby',
          'aria-describedby',
        ];
        let children = panel.querySelectorAll(`[${attributes.join('], [')}]`);
        
        /**
         * Retrieve the updated ID of a child element
         * 
         * @param {string} originalID The original ID to be updated.
         * @returns {string} Returns the updated ID.
         */
        function getUpdatedID (originalID) {
          let IDs = originalID.split(', ');

          for (let id in IDs) {
            IDs[id] = `${codeID}_${IDs[id]}`;
          }

          return IDs.join(', ');
        }

        // IDs and Hashes
        panel.id = codeID;
        edit.attr(panel, 'add', 'data-code-id', code.properties.code_id);

        if (code.properties.code_hash !== null) {
          edit.attr(panel, 'add', 'data-code-hash', code.properties.code_hash);
        }

        for (let child of children) {
          for (let attribute of attributes) {
            let value = dom.get(child, 'attr', attribute);
  
            if (value !== false && value != "") {
              edit.attr(child, 'update', attribute, getUpdatedID(value));
            }
          }
        }

        // Redeemed SHiFT Code
        if (code.states.userHasRedeemed) {
          redeemShiftCode(panel, true);
        }
      })();

      if (code.properties.code_state != 'deleted') {
        deleteElement(panelSections.deletedBody);

        // Header
        (function () {
          // Reward
          dom.find.child(panelSections.header, 'class', 'reward').innerHTML = code.info.reward;
          // Labels
          (function () {
            /** SHiFT Code States */
            let states = code.states;
  
            // Basic SHiFT Code, Rare SHiFT Code, New, Rare Labels
            if (states.codeIsActive && code.properties.code_state == 'active') {
              // Basic SHiFT Code Label
              if (code.info.reward.trim().search('\\d{1} Golden Key(s){0,1}$') == 0) {
                edit.class(panel, 'add', 'basic');
              }
              // Rare SHiFT Code Label
              else {
                edit.class(panel, 'add', 'rare');
              }
              // New! Label
              if (states.codeIsNew) {
                edit.class(panel, 'add', 'new');
              }
              // Expiring! Label
              if (states.codeIsExpiring) {
                edit.class(panel, 'add', 'expiring');
              }
            }
            else if (code.properties.code_state == 'hidden') {
              edit.class(panel, 'add', 'hidden');
            }
            // Expired SHiFT Code Label
            else if (!states.codeIsActive) {
              edit.class(panel, 'add', 'expired');
            }
            // Game Label
            if (shiftProps.game == 'all') {
              (function () {
                /** The Game Label */
                let label = dom.find.child(panelSections.header, 'class', 'label game-label');
                /** The Game ID of the SHiFT Code */
                let gameID = code.properties.game_id;
                /** The Game Name of the SHiFT Code */
                let gameName = shiftNames[gameID];
                
                // Display the label
                edit.class(panel, 'add', 'game-label');
                // Update the label
                edit.class(label, 'add', gameID);
                label.innerHTML = label.innerHTML.replace('Borderlands', gameName);
                updateLabel(label, `SHiFT Code for ${gameName}`, [ 'tooltip' ]);
              })();
            }
            // Recently Added Label
            if (states.codeWasRecentlyAdded) {
              edit.class(panel, 'add', 'recently-added');
            }
            // Recently Updated Label
            else if (states.codeWasRecentlyUpdated) {
              edit.class(panel, 'add', 'recently-updated');
            }
            // Owner Label
            if (states.userIsOwner) {
              edit.class(panel, 'add', 'owned');
            }
          })();
          // Progress Bar
          (function () {
            /** The header Progress Bar */
            let progressBar = dom.find.child(panelSections.header, 'class', 'progress-bar');
            /** The inside bar of the Progress Bar */
            let innerProgressBar = dom.find.child(progressBar, 'class', 'progress');
            /** The progress value of the Progress Bar */
            let progressValue = 0;
            /** The label of the Progress Bar */
            let progressTitle = '';
  
            // SHiFT Code has an Expiration Date
            if (code.info.expiration_date) {
              /** The Expiration Date Moment of the SHiFT Code */
              let expiration = moment.utc(code.info.expiration_date);
              /** The total duration of the SHiFT Code */
              let duration = expiration.diff(code.info.release_date, 'hours');
              /** The total number of hours remaining before the SHiFT Code is set to expire */
              let timeLeft = expiration.diff(moment.utc(), 'hours');
              /** The time-from-now string for the Expiration Date */
              let timeAgo = expiration.fromNow(true);
  
              // Active SHiFT Code
              if (code.states.codeIsActive) {
                progressValue = 100 - Math.round((timeLeft / duration) * 100);
                progressTitle = ucWords(`${timeAgo} Remaining`);
              }
              // Expired SHiFT Code
              else {
                progressValue = 100;
                progressTitle = ucWords(`Expired ${timeAgo} Ago`);
              }
            }
            // SHiFT Code does not expire
            else {
              edit.class(progressBar, 'add', 'inactive');
              progressValue = 0;
              progressTitle = 'No Expiration Date';
            }
  
            edit.attr(progressBar, 'add', 'aria-valuenow', progressValue);
            updateLabel(progressBar, progressTitle, [ 'tooltip' ]);
            edit.class(innerProgressBar, 'add', code.properties.game_id);
            innerProgressBar.style.width = `${progressValue}%`;
          })();
        })();
        // Body
        (function () {
          /**
           * Retrieve the content block of a given field
           * 
           * @param {string} name The name of the field.
           * @returns {Element} Returns the content block of the field.
           */
          function getField(name) {
            return dom.find.child(dom.find.child(panelSections.body, 'class', `section ${name}`), 'class', 'content');
          }
  
          // Code Info
          (function () {
            // Release & Expiration Date
            (function () {
              /** Date Formats */
              let formats = {};
                  /** Display Date Formats */
                  formats.dates = {};
                  formats.dates.date = 'MMM DD, YYYY';
                  formats.dates.expandedDate = 'dddd, MMMM DD, YYYY';
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
                    f.expanded.sameElse = dates.expandedDate;
          
                    return f;
                  })();
                  dates.release.dates = (function () {
                    let release = code.info.release_date;
  
                    if (release) {
                      /** The SHiFT Code Release Date Moment */
                      let releaseMoment = moment(release);
  
                      return {
                        simple: releaseMoment.calendar(null, dates.release.formats.simple),
                        full: releaseMoment.calendar(null, dates.release.formats.full),
                        expanded: releaseMoment.calendar(null, dates.release.formats.expanded),
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
                    f.expanded.sameElse = dates.expanded;
          
                    return f;
                  })();
                  dates.expiration.dates = (function () {
                    let expiration = code.info.expiration_date;
  
                    if (expiration) {
                      /** The SHiFT Code Release Date Moment */
                      let expirationMoment = moment.tz(expiration, code.info.timezone);
  
                      return {
                        simple: expirationMoment.calendar(null, dates.expiration.formats.simple),
                        full: expirationMoment.calendar(null, dates.expiration.formats.full),
                        expanded: expirationMoment.calendar(null, dates.expiration.formats.expanded),
                      };
                    }
                    else {
                      return false;
                    }
                  })();
  
              for (let date in dates) {
                let dateDates = dates[date].dates;
                /** The Date Field Components */
                let comps = {};
                    comps.main = getField(date);
                    comps.simple = dom.find.child(comps.main, 'class', 'simple');
                    comps.full = dom.find.child(comps.main, 'tag', 'dd');
  
                // Date is not NULL
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
                // Date is NULL
                else {
                  edit.class(comps.main, 'add', 'inactive');
                  updateLabel(comps.full, `No ${ucWords(date)} Date`, [ 'tooltip' ]);
                  deleteElement(comps.simple);
                  comps.full.innerHTML = 'N/A';
                }
              }
            })();
            // Source
            (function () {
              /** The SHiFT Code Source Field */
              let field = getField('src');
              /** The Source Link Component */
              let linkComp = dom.find.child(field, 'class', 'link');
              /** The Source Static Text Component */
              let staticComp = dom.find.child(field, 'class', 'no-link');
              /** The SHiFT Code Source */
              let source = code.info.source;
  
              if (source) {
                linkComp.href = source;
                linkComp.innerHTML += source;
                deleteElement(staticComp);
              }
              else {
                edit.class(field, 'add', 'inactive');
                deleteElement(linkComp);
              }
            })();
            // Notes
            (function () {
              /** The SHiFT Code Notes Field */
              let field = getField('notes');
              /** The SHiFT Code Notes */
              let notes = code.info.notes;
  
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
            /** The supported platform groups */
            let groups = [ 'pc', 'xbox', 'ps' ];
            /** The SHiFT Codes and their supported Platforms */
            let shiftCodes = code.codes;
  
            for (let group of groups) {
              /** The SHiFT Code Platform Group Field */
              let field = dom.find.child(panelSections.body, 'class', group);
              /** The platforms of the platform group that are supported by the SHiFT Code */
              let platforms = Object.values(shiftCodes[`platforms_${group}`]).join(' / ');
              /** The platform-group-specific SHiFT Code */
              let shiftCode = shiftCodes[`code_${group}`];
  
              // Platform Component
              dom.find.child(field, 'class', 'title').innerHTML = platforms;
              // SHiFT Code Display Component
              dom.find.child(field, 'class', 'display').innerHTML = shiftCode;
              updateLabel(dom.find.child(field, 'class', 'display'), `${platforms} SHiFT Code`, [ 'tooltip' ]);
              // SHiFT Code Clipboard-Copy Input Component
              dom.find.child(field, 'class', 'value').value = shiftCode;
            }
          })();
        })();
        // Footer
        (function () {
          let footer = dom.find.child(panelSections.body, 'class', 'footer');
  
          // Actions
          (function () {
            let actions = {};
                actions.container = dom.find.child(footer, 'class', 'actions');
                actions.redeem = dom.find.child(actions.container, 'class', 'redeem');
                actions.optionsMenu = dom.find.child(actions.container, 'class', 'options-menu');
  
            // Inactive SHiFT Codes cannot be redeemed
            if (!code.states.codeIsActive) {
              isDisabled(actions.redeem, true);
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
              let ID = code.properties.code_id;
  
              updateInfoValue('id', ID, `SHiFT Code #${ID}`);
            })();
            // SHiFT Code Last Update
            (function () {
              updateShiftCodeTimestamp(panel, code.info.last_update);
            })();
            // The SHiFT Code Owner
            (function () {
              // At this time, don't display username unless the user has Edit Permission
              if (!code.states.userCanEdit) {
                deleteElement(info.owner);
              }
              else {
                let username = code.properties.owner_username;
                let userID = code.properties.owner_id;
  
                updateInfoValue('owner', username, `${username} #${userID}`);
              }
            })();
          })();
        })();
        // SHiFT Code Options Menu
        (function () {
          let menus = dom.find.children(panel, 'class', 'shift-code-options-menu');

          for (let menu of menus) {
            let pieces = {};
              (function () {
                pieces.codeID = dom.find.child(menu, 'class', 'code-id');
                pieces.share = dom.find.child(menu, 'attr', 'data-value', 'share');
                pieces.report = dom.find.child(menu, 'attr', 'data-value', 'report');
                pieces.editActions = dom.find.child(menu, 'class', 'edit-actions');
                pieces.makePublic = dom.find.child(pieces.editActions, 'attr', 'data-value', 'make_public');
                pieces.makePrivate = dom.find.child(pieces.editActions, 'attr', 'data-value', 'make_private');
              })();

            edit.attr(menu, 'add', 'data-code-id', code.properties.code_id);
            pieces.codeID.innerHTML = code.properties.code_id;
  
            // User does not have Edit Permission
            if (!code.states.userCanEdit) {
              deleteElement(pieces.editActions);
            }
            // User has Edit Permission
            else {
              isDisabled(pieces.report);

              (function () {
                let inactiveButton = code.properties.code_state == 'active'
                                  ? pieces.makePublic
                                  : pieces.makePrivate;
                                  
                isHidden(inactiveButton, true);
                isDisabled(inactiveButton, true);
              })();
            }
          }
        })();
        // Configuration
        (function () {
          // Dropdown Panel Configuration
          dropdownPanelSetup(panel);
          // options Menu Configuration
          // configureDropdownMenu(dom.find.child(panelSections.body, 'class', 'shift-code-options-dropdown'));
          // setupDropdownMenu(dom.find.child(dom.find.child(dom.find.child(panelSections.body, 'class', 'footer'), 'class', 'actions'), 'class', 'dropdown-menu'));
          // MultiView Configuration
          multiView_setup(dom.find.child(panelSections.body, 'class', 'multi-view'));
          // Copy to Clipboard Listeners
          // TODO: Change to single Event Listener, Update Copy to Clipboard Functionality
          // (function () {
          //   let buttons = dom.find.children(panelSections.body, 'class', 'copy');
  
          //   for (let button of buttons) {
          //     button.addEventListener('click', copyToClipboard);
          //   }
          // })();
        })();
      }
      // Deleted SHiFT Code
      else {
        deleteElement(panelSections.body);

        let timestamp = dom.find.child(panelSections.deletedBody, 'class', 'timestamp');
        let lastUpdate = moment.utc(code.info.last_update);

        edit.class(panel, 'add', 'deleted');
        dom.find.child(panelSections.header, 'class', 'reward').innerHTML = `SHiFT Code ${code.properties.code_id}`;

        timestamp.innerHTML = lastUpdate.fromNow();
        updateLabel(timestamp, lastUpdate.format('MMMM DD, YYYY, hh:mm A [UTC]'), [ 'tooltip' ]);
      }

      return panel;
    }
    catch (e) {
      console.error(`getShiftCodePanel Error: ${e}`);
      return false;
    }
  }

  // Retrieve the list of SHiFT Codes
  (function () {
    // Clear the Update Poller interval
    shiftUpdates.interval.clear();
    // Disable the SHiFT Controls
    toggleControls(false);
    // Display the Loading Spinner
    updateOverlay({
      overlay: true,
      spinner: true,
      error: false
    });
    // Clear the current result list
    clearList();
    // Update the Loader Progress Bar
    lpbUpdate(50, true, { start: 15 });
    // Retrieve the list of SHiFT Codes
    newAjaxRequest({
      file: '/assets/requests/get/shift/codes',
      params: shiftProps,
      callback: function (responseText) {
        let response = tryJSONParse(responseText);

        if (response && response.statusCode == 200) {
          lpbUpdate(75);
  
          let totals = response.payload.counts;
          let shiftCodes = response.payload.shift_codes;

          // Update SHiFT Components if totals have been adjusted
          if (totals && shiftStats != totals) {
            shiftStats = totals;
            syncShiftComponents();
          }
          // Update Result List
          if (shiftCodes.length > 0) {
            for (let i = 0; i < shiftCodes.length; i++) {
              let panel = getShiftCodePanel(shiftCodes[i]);

              if (!panel) {
                continue;
              }

              panel.style.animationDelay = `${i * 0.2}s`;
              shiftComps.list.appendChild(panel);
              // setupDropdownMenu(dom.find.child(dom.find.child(dom.find.child(panel, 'class', 'footer'), 'class', 'actions'), 'class', 'dropdown-menu'));
            }

            hashUpdate();
            lpbUpdate(100);
            updateOverlay({
              overlay: false,
              spinner: false,
              error: false
            });
          }
          else {
            lpbUpdate(100);
            updateOverlay({
              overlay: true,
              spinner: false,
              error: true
            });
          }

          shiftUpdates.interval.set();
          
          setTimeout(function () {
            toggleControls(true);
          }, 500);
        }
        else {
          lpbUpdate(100);
          updateOverlay({
            overlay: true,
            spinner: false,
            error: true
          });
          ShiftCodesTK.toasts.newToast({
            settings: {
              template: 'fatalException'
            },
            content: {
              title: 'SHiFT Code Retrieval Error',
              body: 'We could not retrieve any SHiFT Codes due to an error. Please refresh the page and try again.'
            }
          });
        }   
      }
    });
  })();
}

function shiftCodeFormGameChangeEvent () {
  let form = dom.find.id('shift_code_form');
  let gameID = dom.find.child(form, 'attr', 'name', 'general_game_id').value;
  let platformFields = dom.find.children(form, 'attr', 'data-supported-games');

  for (let field of platformFields) {
    let supportedPlatforms = tryJSONParse(dom.get(field, 'attr', 'data-supported-games'));

    for (let platform in supportedPlatforms) {
      let supportedGames = supportedPlatforms[platform];
      let child = dom.find.child(field, 'attr', 'value', platform);
      let parent = dom.find.parent(child, 'class', 'field');
      let isActive = supportedGames.indexOf(gameID) != -1;

      isDisabled(child, !isActive);
      edit.class(parent, !isActive ? 'add' : 'remove', 'disabled');
      edit.attr(child, isActive ? 'add' : 'remove', 'checked');
    }
  }
}

// Startup Functions
(function () {
  let interval = setInterval(function () {
    let isReady = typeof globalFunctionsReady != 'undefined'
                  && typeof moment != 'undefined';

    if (isReady) {
      clearInterval(interval);

      // Get page-specific SHiFT Props
      (function () {
        // Get page properties
        props = tryJSONParse(dom.get(dom.find.id('shift_code_list'), 'attr', 'data-shift'));

        if (props) {
          for (let prop in props) {
            if (shiftProps[prop] !== undefined) {
              shiftProps[prop] = props[prop];
            }
          }
        }
      })();
      // Configure layers
      (function () { 
        let template = dom.find.id('shift_code_template').content.children[0];
        let layers = dom.find.children(template, 'class', 'layer');
        
        for (let i = 0; i < layers.length; i++) {
          let layer = layers[i];

          layer.id = `shift_code_layer_${i}`;
          ShiftCodesTK.layers.setupLayer(layer);
        }
      })();
      // Get initial SHiFT Code Listing
      (function () {
        hashState = addHashListener('shift_code_', function (hash) {
          shiftProps.offset = 0;
          shiftProps.code = hash.replace('#shift_code_', '');
          retrieveCodes();
          shiftProps.code = false;
        });
    
        if (!hashState) {
         retrieveCodes();
        }
      })();
      // Set SHiFT Code Update Timestamps
      (function () {
        let now = moment.utc().valueOf();

        shiftUpdates.stats = {
          first_check: now,
          last_check: now
        };
      })();
      // Event Listeners
      (function () {
        // Click Listeners (Sort/Filter, Redeem)
        window.addEventListener('click', function (event) {
          let target = event.target;

          // Filter
          if (target.id.indexOf('shift_header_count') != -1) {
            let pressed = dom.has(target, 'attr', 'aria-pressed', 'true');
            let filter = dom.get(target, 'attr', 'data-value');
            let props = shiftProps.filter;

            if (!pressed) { props.push(filter); }
            else          { props.splice(props.indexOf(filter), 1); }

            // updateLabel(target, target.title.replace(!pressed ? 'Filter' : 'clear Filter', !pressed ? 'clear Filter' : 'Filter'));
      
            shiftProps.offset = 0;
            retrieveCodes();
          }
          // Redeem
          else if (dom.has(target, 'class', 'redeem')) {
            let shiftCode = dom.find.parent(target, 'class', 'shift-code');
            let hash = shiftCode ? dom.get(shiftCode, 'attr', 'data-code-hash') : false;
            let isRedeemed = shiftCode ? dom.has(shiftCode, 'class', 'redeemed') : false;
    
            if (shiftCode && hash && !dom.has(shiftCode, 'class', 'expired')) {
              redeemShiftCode(hash, !isRedeemed);
            }
          }
        });
        
        // Add SHiFT Code
        if (dom.find.id('shift_header_add')) {
          dom.find.id('shift_header_add').addEventListener('click', function (e) {
            let modal = dom.find.id('shift_code_modal');
            let form = dom.find.child(modal, 'tag', 'form');
            let fields = dom.find.children(form, 'class', 'input');
  
            form.reset();
  
            for (let field of fields) {
              let name = dom.get(field, 'attr', 'name');
  
              if (name.indexOf('auth') == -1) {
                formUpdateField(form, name, '');
              }
            }
            
            formUpdateField(form, 'general_game_id', shiftProps.game != 'all' ? shiftProps.game : '');
            shiftCodeFormGameChangeEvent();
            toggleModal(modal, true);
          });
        }
        // Sort/Filter
        (function () {
          let slideout = dom.find.child(dom.find.id('shift_header'), 'class', 'slideout');
          let toggleButton = dom.find.id('shift_header_sort_filter');

          toggleButton.addEventListener('click', function (e) {
            edit.attr(toggleButton, 'toggle', 'aria-pressed');
            isHidden(slideout);
          });

          // Slideout Buttons
          (function () {
            let form = dom.find.child(slideout, 'tag', 'form');

            dom.find.child(form, 'class', 'submit').addEventListener('click', function (e) {
              let formData = ShiftCodesTK.forms.getFormData(form);
              let bindings = {
                game: formData.game_filter,
                filter: formData['status_filter[]'],
                order: formData.sort 
              }

              event.preventDefault();

              for (let binding in bindings) {
                let formValue = bindings[binding];

                if (formValue !== undefined) {
                  shiftProps[binding] = formValue;
                }
              }

              if (bindings.game) {
                edit.attr(document.body, 'update', 'data-theme', bindings.game != 'all' ? bindings.game : 'main');
              }
              edit.attr(toggleButton, 'update', 'aria-pressed', 'false');
              isHidden(slideout, true);

              setTimeout(retrieveCodes, 100);
            });
          })();
        })();

        // Dropdowns
        (function () {
          let layerObject = ShiftCodesTK.layers;

          // Options Actions
          layerObject.addLayerListener('shift_code_options_menu', function (option, layer) {
            let value = dom.get(option, 'attr', 'data-value');
            /** The code_id of the SHiFT Code */
            let codeID = dom.get(layer, 'attr', 'data-code-id');

            if (codeID) {
              if (value == 'edit') {
                /** The SHiFT Code */
                let shiftCode = dom.find.id(`shift_code_${codeID}`);
                /** The editing view */
                let view = dom.find.child(shiftCode, 'class', 'view edit');
                
                newAjaxRequest({
                  file: '/assets/requests/get/shift/codes',
                  type: 'GET',
                  params: {
                    code: codeID,
                    filter: [ 'inactive', 'active' ],
                    game: 'all',
                    limit: 1,
                    offset: 0,
                    order: 'default',
                    owner: true
                  },
                  callback: function (responseText) {
                    let response = tryJSONParse(responseText);

                    function onError (errorMessage = 'This SHiFT Code could not be edited due to an error while downloading the SHiFT Code. Please try again later.') {
                      newToast({
                        settings: {
                          template: 'exception'
                        },
                        content: {
                          title: 'Failed to edit SHiFT Code',
                          body: errorMessage
                        }
                      });
                      isDisabled(target, false);
                    }

                    if (response && response.statusCode == 200) {
                      let code = response.payload.shift_codes[0];

                      if (code) {
                        let canEdit = code.states.userCanEdit;

                        if (canEdit) {
                          let form = dom.find.child(view, 'tag', 'form');
                          let moments = {
                            release: code.info.release_date 
                                    ? moment(code.info.release_date)
                                    : false,
                            expiration: code.info.expiration_date
                                        ? moment.tz(code.info.expiration_date, code.info.timezone)
                                        : false
                          };

                          if (!dom.has(form, 'class', 'configured')) {
                            formSetup(form);
                          }
                          /** Form -> SHiFT Code bindings */
                          let bindings = {
                            general_code_id: code.properties.code_id,
                            general_reward: code.info.reward,
                            // general_game_id: shiftCode.properties.game_id,
                            // general_source: shiftCode.info.source,
                            // general_release_date: moments.release
                            //                       ? moments.release.format('Y-MM-DD')
                            //                       : '',
                            // general_expiration_date_date: moments.expiration
                            //                               ? moments.expiration.format('Y-MM-DD')
                            //                               : '',
                            // general_expiration_date_time: moments.expiration 
                            //                               ? moments.expiration.format('HH:mm:SS')
                            //                               : '',
                            // general_expiration_date_tz: shiftCode.info.timezone,
                            // general_notes: shiftCode.info.notes,
                            // codes_pc: shiftCode.codes.code_pc,
                            // platforms_pc: Object.keys(shiftCode.codes.platforms_pc),
                            // codes_xbox: shiftCode.codes.code_xbox,
                            // platforms_xbox: Object.keys(shiftCode.codes.platforms_xbox),
                            // codes_ps: shiftCode.codes.code_ps,
                            // platforms_ps: Object.keys(shiftCode.codes.platforms_ps),
                          };

                          form.reset();

                          for (let field in bindings) {
                            let binding = bindings[field];

                            formUpdateField(form, field, binding);
                          }

                          // shiftCodeFormGameChangeEvent ();
                          // toggleModal(modal, true);
                          multiView_update(view);
                          ShiftCodesTK.layers.toggleLayer(layer, false);
                          setTimeout(function () {
                            // isDisabled(target, false);
                          }, 500);
                        }
                        else {
                          newToast({
                            settings: {
                              duration: 'infinite'
                            },
                            content: {
                              title: 'Failed to edit SHiFT Code',
                              body: 'You do not have permission to edit this SHiFT Code.'
                            }
                          });
                          // isDisabled(option, false);
                        }
                      }
                      else {
                        onError();
                      }
                    }
                    else {
                      onError();
                      return false;
                    }
                  }
                });
              }
              else if (value == 'make_public' || value == 'make_private') {
                let shiftCode = dom.find.id(`shift_code_${codeID}`);
                
                ShiftCodesTK.layers.toggleLayer(layer, false);
                
                edit.class(
                  shiftCode,
                  value == 'make_public'
                    ? 'remove'
                    : 'add',
                  'hidden'
                );
                dom.find.child(dom.find.child(shiftCode, 'class', 'last-update'), 'tag', 'dd').innerHTML = moment().fromNow();

                // Update SHiFT Code
                (function () {

                })();
                // Update Dropdowns
                (function () {
                  let dropdowns = dom.find.children(shiftCode, 'class', 'shift-code-options-menu');

                  for (let dropdown of dropdowns) {
                    let options = dom.find.children(dropdown, 'class', 'visibility-toggle');

                    for (let option of options) {
                      let isSelectedOption = dom.get(option, 'attr', 'data-value') == value;

                      isHidden(option, isSelectedOption);
                      isDisabled(option, isSelectedOption);
                    }
                  }
                })();
              }
              else if (value == 'delete') {
                /** The confirmation modal */
                let modal = dom.find.id('shift_code_deletion_confirmation_modal');
                let form = dom.find.child(modal, 'tag', 'form');
                
                form.reset();
                formUpdateField(form, 'code_id', codeID);
                toggleDropdownMenu(layer, false);
                toggleModal(modal, true);
              }
            }
          });
        })();
        // Pager
        tryToRun({
          function: function () {
            let pager = dom.find.id('shift_code_pager');

            if (!pager || !dom.has(pager, 'class', 'configured')) {
              return false;
            }

            addPagerListener(dom.find.id('shift_code_pager'), function (offset) {
              shiftProps.offset = offset;
              retrieveCodes();
              return true;
            });

            return true;
          }
        });
        // SHiFT Code Form Game ID Change
        // tryToRun({
        //   // function: function () {
        //   //   let modal = dom.find.id('shift_code_modal');
        //   //   let form = dom.find.id('shift_code_form');
        //   //   let field = form ? dom.find.child(form, 'attr', 'name', 'general_game_id') : false;
            
        //   //   if (!modal || !dom.has(modal, 'class', 'configured')) {
        //   //     return false;
        //   //   }
  
        //   //   field.addEventListener('change', shiftCodeFormGameChangeEvent);
        //   // }
        // });
        
      })();
    }
  }, 250);
})();
