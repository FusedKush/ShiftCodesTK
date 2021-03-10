(function () {
  let interval = setInterval(function () {
    let isReady = typeof globalFunctionsReady != 'undefined' 
                  && typeof moment != 'undefined' 
                  && typeof ShiftCodesTK != 'undefined' 
                  && ShiftCodesTK.requests.isLoaded;

    if (isReady) {
      clearInterval(interval);

      /** Properties & Methods used for `Profile Cards` */
      ShiftCodesTK.profile_card = {
        /** 
         * @property The *Border* of the Profile Card will be omitted. 
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_HIDE_BORDER: 1,
        /** 
         * @property The *Profile Picture* of the Profile Card will be omitted. 
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_HIDE_PROFILE_PICTURE: 2,
        /** 
         * @property The *Username* of the Profile Card will be omitted. 
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_HIDE_USERNAME: 4,
        /** 
         * @property The *User ID* of the Profile Card will be omitted. 
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_HIDE_USER_ID: 8,
        /** 
         * @property The *Roles Section* of the Profile Card will be included. 
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_SHOW_ROLES: 16,
        /** 
         * @property The *Profile Stats Section* of the Profile Card will be included. 
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_SHOW_STATS: 32,
        /** 
         * @property The *Profile Actions Section* of the Profile Card will be included. 
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_SHOW_ACTIONS: 64,
        /** 
         * @property Editing features will be enabled for the Profile Card.
         * - Used for the `get_profile_card()` method.
         * @type {number}
         */
        CARD_ALLOW_EDITING: 128,

        /** 
         * @property The *Profile Card Template* used to create new Profile Cards.
         * @type {HTMLTemplateElement}
         */
        template: dom.find.id("profile_card_template"),
        /** 
         * @property An `object` representing a *Profile Card Data Object*.
         * @type {object}
         */
        card_data_template: {
          /** 
           * @property Properties related to the *User Profile* of the user.
           * @type {object}
           */
          user_data: {
            /** 
             * @property The User's *Unique ID*.
             * @type {?string}
             */
            id: null,
            /** 
             * @property The User's *Username*.
             * @type {?string}
             */
            username: null,
            /** 
             * @property If available, the path to the user's *Profile Picture*.
             * @type {?string}
             */
            profile_picture: null,
            /** 
             * @property If available, the *Privacy Preference* for the User's *Profile Stats*.
             * @type {(?"hidden"|"private"|"public")}
             */
            profile_stats_preference: null,
            /** 
             * @property An `array` made up of the *User Roles* that the user has been assigned.
             * @type {array}
             */
            roles: []
          },
          /** 
           * @property Properties related to the *User Permissions* of the user.
           * @type {object}
           */
          permissions: {
            /**
             * @property Indicates if the current user can *report* the user of the profile card. Defaults to **false**.
             * @type {boolean=} 
             */
            can_report: false,
            /**
             * @property Indicates if the current user can perform an *enforcement action* against the user of the profile card. Defaults to **false**.
             * @type {boolean=} 
             */
            can_enforce: false,
            /**
             * @property Indicates if the current user can *edit* the profile card. Defaults to **false**.
             * @type {boolean=} 
             */
            can_edit: false,
            /**
             * @property Indicates if the current user can *change their username* using the profile card. Defaults to **false**.
             * @type {boolean=} 
             */
            can_change_username: false
          },
          /** 
           * @property Properties representing the *Profile Stats* of the user, if available.
           * 
           * These properties may be omitted even when requested if the Current User does not have permission to view them.
           * 
           * @type {object}
           */
          profile_stats: {
            /**
             * @property A *Timestamp String* representing the last time the user performed a public activity.
             * @type {?string}
             */
            last_public_activity: null,
            /**
             * @property A *Timestamp String* representing when the user joined ShiftCodesTK.
             * @type {?string}
             */
            creation_date: null,
            /**
             * @property A `number` representing the number of SHiFT Codes the user has submitted.
             * @type {?string}
             */
            shift_codes_submitted: null
          }
        },
        /** 
         * @property An `object` representing *Saved Profile Card Data* that can be used for repeat queries.
         * 
         * You can use the `store_data()` and `get_stored_data()` to access the saved data.
         * 
         * @type {object}
         * 
         */
        stored_data: {},
        
        /**
         * Store a user's *Profile Card Data* for later use in the session. 
         * 
         * @param {object} userdata The *Profile Card Data `Object`* to be stored.
         * @param {boolean} replace_old_data Indicates if the old *Profile Card Data* for the associated user should be replaced if encountered. Defaults to **false**. 
         * @returns {boolean} Returns **true** on success and **false** on failure.
         */
        store_data (userdata, replace_old_data = false) {
          if (typeof userdata != 'object' || !userdata.user_data || !userdata.user_data.id) {
            console.error('An invalid Profile Card Data Object was provided.');
            return false;
          }

          const userID = userdata.user_data.id;

          if (!this.stored_data[userID] || replace_old_data) {
            this.stored_data[userID] = {
              /** 
               * @property The stored *Profile Card Data*
               * @type {object} 
               **/
              data: userdata,
              /** 
               * @property A *Timestamp* representing when the stored data will expire. 
               * @type {number}
               **/
              max_age: moment()
                       .add(10, 'minutes')
                       .valueOf()
            };

            return true;
          }

          return false;
        },
        /**
         * Retrieve the stored *Profile Card Data* for a user.
         * 
         * @param {string} user_id The User's `user_id`.
         * @return {object|false} Returns the *Stored Profile Card Data* on success. Returns **false** if no stored data was found or if the stored data has expired.
         */
        get_stored_data (user_id) {
          const storedData = this.stored_data[user_id];

          if (storedData) {
            // Check if stored data has expired
            if (moment().valueOf() < this.stored_data[user_id].max_age) {
              return storedData.data;
            }
            else {
              delete this.stored_data[user_id];
            }
          }

          return false;
        },
        /** 
         * Create a new *Profile Card*.
         * 
         * @param {(string|object)} user A value representing the target *User* of the Profile Card. This can be one of two values:
         * - A `string` representing the *User ID* of the user who's *Profile Card Data* is to be retrieved.
         * - An `object` representing the *Profile Card Data* of the user. See `card_data_template` for more information on the structure of the object.
         * @param {Function} callback The callback function to utilize the new Profile Card.
         * - Only one argument is provided: The *Profile Card `Element`*.
         * @param {number} flags A *bitmask* of `CARD_*` object constants used to customize the Profile Card:
         * 
         * | Flag | Description | 
         * | --- | --- |
         * | `CARD_HIDE_BORDER` | The Border of the Profile Card will be omitted. |
         * | `CARD_HIDE_PROFILE_PICTURE` | The Profile Picture of the Profile Card will be omitted. |
         * | `CARD_HIDE_USERNAME` | The Username of the Profile Card will be omitted. |
         * | `CARD_HIDE_USER_ID` | The User ID of the Profile Card will be omitted. |
         * | `CARD_SHOW_ROLES` | The Roles Section of the Profile Card will be included. |
         * | `CARD_SHOW_STATS` | The Profile Stats Section of the Profile Card will be included. |
         * | `CARD_SHOW_ACTIONS` | The Profile Actions Section of the Profile Card will be included. |
         * | `CARD_ALLOW_EDITING` | Editing features will be enabled for the Profile Card. |
         * @return {boolean} Returns **false** if `user` is of an invalid type. Otherwise, returns **true**.
         */
        create_card (user, callback, flags = 0) {
          const cardObj = ShiftCodesTK.profile_card;
          const layersObj = ShiftCodesTK.layers;
          let profileCard = (function () {
            let profileCard = edit.copy(cardObj.template);

            profileCard = dom.find.id('temp').appendChild(profileCard);

            return profileCard;
          })();
          const templateID = profileCard.id;
          const profileCardID = randomID('profile_card_', 100, 1000);

          /**
           * Generate the new Profile Card
           * 
           * @param {object} userData The *Profile Card Data Object* used to construct the profile card.
           * @return {object} Returns the new Profile Card.
           */
          function get_profile_card (userData) {
            profileCard.id = profileCardID;
            edit.attr(profileCard, 'update', 'data-user-id', userData.user_data.id);
            
            // Global Replacements
            profileCard.innerHTML = profileCard.innerHTML.replaceAll(templateID, profileCardID);
            profileCard.innerHTML = profileCard.innerHTML.replaceAll('${user_id}', userData.user_data.id);
            profileCard.innerHTML = profileCard.innerHTML.replaceAll('${username}', userData.user_data.username);

            layersObj.setupChildLayers(profileCard);
            multiView_setup_children(profileCard);
            multiView_setup(profileCard);

            // Hide Border
            if (flags & cardObj.CARD_HIDE_BORDER) {
              edit.class(profileCard, 'add', 'hide-border');
            }

            // Profile Picture
            (function () {
              const profilePicture = dom.find.child(profileCard, 'class', 'profile-picture');
              const profilePicturePath = userData.user_data.profile_picture;

              if (flags & cardObj.CARD_HIDE_PROFILE_PICTURE) {
                deleteElement(profileCard);
              }
              else {
                const img = dom.find.child(profilePicture, 'tag', 'img');
                const placeholder = dom.find.child(profilePicture, 'class', 'placeholder');
  
                if (profilePicturePath) {
                  deleteElement(placeholder);
                  edit.attr(img, 'add', 'src', `/assets/img/users/profiles/${userData.user_data.id}/${profilePicturePath}?_request_token=${requestToken.get()}&size=128`);
                }
                else {
                  const placeholderLetters = (function () {
                    let letters = userData.user_data.username.match(/[A-Z]/g);

                    if (letters) {
                      letters = letters.slice(0, 2);
                      letters = letters.join('');
                    }
                    else {
                      letters = username.slice(0, 2);
                      letters = letters.toUpperCase();
                    }

                    return letters;
                  })();

                  deleteElement(img);
                  placeholder.innerHTML = placeholderLetters;
                }
              }
            })();

            // Hide Username
            if (flags & cardObj.CARD_HIDE_USERNAME) {
              deleteElement(dom.find.child(profileCard, 'class', 'user-name'));
            }

            // Hide User ID
            if (flags & cardObj.CARD_HIDE_USER_ID) {
              deleteElement(dom.find.child(profileCard, 'class', 'user-id'));
            }

            // Roles
            if (userData.user_data.roles) {
              for (let element of dom.find.child(profileCard, 'class', 'section roles').childNodes) {
                let role = dom.get(element, 'attr', 'data-role');

                if (role) {
                  if (userData.user_data.roles.indexOf(role) != -1) {
                    edit.class(profileCard, 'add', `role-${role}`);
                  }
                  else {
                    layersObj.detachLayers(element);
                    deleteElement(element);
                  }
                }
              }
            }
            if ((flags & cardObj.CARD_SHOW_ROLES) === 0) {
              deleteElement(dom.find.child(profileCard, 'class', 'section roles'));
            }

            // Profile Stats
            (function () {
              const profileStats = dom.find.child(profileCard, 'class', 'section stats');

              if (flags & cardObj.CARD_SHOW_STATS) {
                const dateStats = [
                  'last_public_activity',
                  'creation_date'
                ];
                let hasProfileStats = false;
  
                for (let stat_name in userData.profile_stats) {
                  let stat_value = userData.profile_stats[stat_name];
  
                  if (stat_value !== null) {
                    let stat_node = dom.find.child(dom.find.child(profileStats, 'class', `definition ${stat_name}`), 'tag', 'dd');
  
                    hasProfileStats = true;
  
                    if (dateStats.indexOf(stat_name) != -1) {
                      edit.attr(stat_node, 'update', 'data-relative-date', stat_value);
                      ShiftCodesTK.relative_dates.refresh_element(stat_node);
                      ShiftCodesTK.layers.updateTooltip(stat_node, moment(stat_value).format('MMMM DD, YYYY hh:mm A [UTC]'));
                    }
                    else {
                      stat_node.innerHTML = stat_value;
                    }
                  }
                }

                if ((flags & cardObj.CARD_SHOW_ACTIONS) & (flags & cardObj.CARD_ALLOW_EDITING)) {

                }
  
                if (!hasProfileStats) {
                  deleteElement(profileStats);
                }
              }
              else {
                deleteElement(profileStats);
              }
            })();

            // Actions
            (function () {
              const actions = dom.find.child(profileCard, 'class', 'section actions');
              
              if (flags & cardObj.CARD_SHOW_ACTIONS) {
                if (!userData.permissions.can_report) {
                  const reportButton = dom.find.child(actions, 'class', 'report');
   
                  layersObj.detachLayers(reportButton);
                  deleteElement(reportButton);
                }
                if (!userData.permissions.can_enforce) {
                  const enforceButton = dom.find.child(actions, 'class', 'enforcement');
   
                  layersObj.detachLayers(enforceButton);
                  deleteElement(enforceButton);
                }
                // Editing Actions
                (function () {
                  const profileStats = dom.find.child(profileCard, 'class', 'section stats');
                  const profileStatsIndicator = dom.find.child(profileStats, 'class', 'stat-privacy');

                  if (flags & cardObj.CARD_ALLOW_EDITING && userData.permissions.can_edit) {
                    setTimeout(function () {
                      // Profile Stats Tooltip
                      (function () {
                        const tooltip = profileStatsIndicator.nextElementSibling;
                        const multiView = dom.find.child(tooltip, 'class', 'multi-view');
  
                        if (multiView) {
                          multiView_update(dom.find.child(multiView, 'class', userData.user_data.profile_stats_preference), true);
                        }
                      })();
                      // "Change Username" option 
                      (function () {
                        const button = dom.find.child(profileCard, 'attr', 'data-value', 'change-username');
                        const multiView = dom.find.parent(button, 'class', 'multi-view');
      
                        if (!userData.permissions.can_change_username) {
                          multiView_update(dom.find.child(multiView, 'class', 'disabled'), true);
                        }
                      })();
                    }, 10);
                  }
                  else {
                    const editProfileButton = dom.find.child(actions, 'class', 'edit-profile');
     
                    layersObj.detachLayers(editProfileButton);
                    deleteElement(editProfileButton);
                  }
                })();
              }
              else {
                deleteElement(actions);
              }
            })();

            ShiftCodesTK.forms.setupChildForms(profileCard);

            profileCard = deleteElement(profileCard);
            callback(profileCard);
            return true;
          }

          if (typeof user == 'string') {
            const storedData = cardObj.get_stored_data(user);

            if (storedData) {
              get_profile_card(storedData);
            }
            else {
              ShiftCodesTK.requests.request({
                type: 'GET',
                path: '/assets/requests/get/account/profile-card-data',
                parameters: {
                  user_id: user
                },
                callback: function (response) {
                  if (response && response.status_code == 200) {
                    cardObj.store_data(response.payload);
                    get_profile_card(response.payload);
                  }
                }
              })
            }

            return true;
          }
          else if (typeof user == 'object' && user.user_data !== undefined) {
            const userData = mergeObj(cardObj.card_data_template, user);

            cardObj.store_data(userData);
            get_profile_card(userData);
            return true;
          }

          // console.error(`The first provided argument must be a User ID String, or a Profile Card Data Object.
          //               \r\n
          //               \r\nProvided: ${$user}`);
          return false;
        },
        /**  
         * Retrieve a *Profile Card Modal* for a given user.
         * 
         * See `create_card()` for more details regarding the `user` and `flags` arguments.
         * 
         * @param {(string|object)} user A *User ID `String`* or a *Profile Card Data `Object`* representing the target *User* of the Profile Card.
         * @param {?Function} callback The callback function to utilize the new Profile Card Modal.
         * - Only one argument is provided: The *Profile Card Modal `Element`*.
         * - If omitted, the modal will just be displayed.
         * @param {number=} flags A *bitmask* of `CARD_*` object constants used to customize the Profile Card.
         * - The following flags are used by default: 
         * - - `CARD_SHOW_ROLES`
         * - - `CARD_SHOW_STATS`
         * @return {boolean} Returns **false** if `user` is of an invalid type. Otherwise, returns **true**.
         */
        get_card_modal (user, callback = null, flags = this.CARD_SHOW_ROLES|this.CARD_SHOW_STATS) {
          return this.create_card(user, (profile_card) => {
            const modal = dom.find.id('profile_card_modal');
            const modalBody = (function () {
              const body = dom.find.child(modal, 'class', 'body');
              const container = dom.find.child(body, 'class', 'content-container');
  
              return container;
            })();
  
            modalBody.innerHTML = profile_card.outerHTML;

            if (callback) {
              callback(modal);
            }
            else {
              ShiftCodesTK.modals.toggleModal(modal, true);
            }
          }, flags);
        }
      };

      cardObj = ShiftCodesTK.profile_card;
      
      // View Toggles
      (function () {
        const toggleForms = [
          'change_username',
          'profile_stats_privacy'
        ];

        function handleViewToggleForForms (event) {
          const formEventData = event.formEventData;
          const formName = formEventData.formProps.info.name;
          const payload = formEventData.formResponseData.payload;
          
          if (toggleForms.indexOf(formName) != -1 && payload && payload.form && payload.form.result) {
            const profileCard = dom.find.parent(event.target, 'class', 'profile-card');
            const view = dom.find.child(profileCard, 'class', 'view primary');
  
            multiView_update(view);
          }
        }

        window.addEventListener('tkFormsFormAfterSubmit', handleViewToggleForForms);
      })();
      // Username
      (function () {
        const formsObject = ShiftCodesTK.forms;
        const requestsObject = ShiftCodesTK.requests;

        requestsObject.savedRequests.saveRequest('profile_card_check_username', {
          parameters: {
            username: ''
          },
          request: {
            path: '/assets/requests/get/account/check_username_availability',
            callback: (response) => {
              if (response && response.payload !== undefined) {
                if (response.payload === false) {
                  const field = formsObject.getField(dom.find.id('change_username'), 'username');

                  // formsObject.addAlert(field, 'This username is already in use.');
                  formsObject.reportFieldIssue(field, 'This username is already in use.');
                }
              }
            }
          }
        });
        

        function checkUsernameForAvailability (event) {
          const formEventData = event.formEventData;
          const fieldName = formEventData.fieldProps.info.name;
          const formName = dom.get(formEventData.field.form, 'attr', 'data-form-name');

          if (formName == 'change_username' && fieldName == 'username') {
            if (formEventData.fieldValue != formEventData.fieldProps.info.defaultValue) {
              requestsObject.savedRequests.setRequestParameter('check_username', 'username', formEventData.fieldValue);
              requestsObject.request('check_username');
            }
          }
        }

        window.addEventListener('tkFormsFieldTimeout', checkUsernameForAvailability);
        window.addEventListener('tkFormsFieldCommit', checkUsernameForAvailability);
        window.addEventListener('tkFormsFormBeforeSubmit', (event) => {
          const formEventData = event.formEventData;
          const requestsObject = ShiftCodesTK.requests;

          if (formEventData.formProps.info.name == 'change_username') {
            const request = requestsObject.savedRequests.getRequest('check_username');

            if (request.activeRequest) {
              // requestsObject.activeRequests.removeRequest(request.activeRequest);
            }
          }
        });
        window.addEventListener('tkFormsFormAfterSubmit', (event) => {
          const formEventData = event.formEventData;

          if (formEventData.formProps.info.name == 'change_username') {
            const formResponseData = formEventData.formResponseData;

            if (formResponseData && formResponseData.payload !== undefined) {
              if (formResponseData.payload.form.result) {
                const profileCards = dom.find.children(document.body, 'class', 'profile-card');
                const canChangeUsernameAgain = formResponseData.payload.can_change_username_again;
                const userID = dom.get(dom.find.parent(formEventData.originalEvent.target, 'class', 'profile-card'), 'attr', 'data-user-id');

                for (let profileCard of profileCards) {
                  if (dom.has(profileCard, 'attr', 'data-user-id', userID)) {
                    let username = dom.find.child(profileCard, 'class', 'definition user-name');
  
                    dom.find.child(username, 'tag', 'dd').innerHTML = formResponseData.payload.new_username;
  
                    if (!canChangeUsernameAgain) {
                      let button = dom.find.child(profileCard, 'class', 'view-toggle change-username');
  
                      if (button) {
                        isDisabled(button, true);
                        ShiftCodesTK.layers.updateTooltip(button, 'You can only change your username&nbsp;<em>twice</em>&nbsp;every&nbsp;<em>24 hours</em>.');
                      }
                    }
                  }

                }
              }
            }
          }
        });
      })();
      // Profile Stats
      (function () {
        window.addEventListener('tkFormsFormAfterSubmit', (event) => {
          const formEventData = event.formEventData;

          if (formEventData.formProps.info.name == 'profile_stats_privacy') {
            const formResponseData = formEventData.formResponseData;

            if (formResponseData && formResponseData.payload !== undefined) {
              if (formResponseData.payload.form.result) {
                const profileCards = dom.find.children(document.body, 'class', 'profile-card');

                for (let profileCard of profileCards) {
                  const indicator = dom.find.child(profileCard, 'class', 'view-toggle stat-privacy');
                  
                  if (indicator) {
                    const tooltip = indicator.nextElementSibling;
                    const preference = formEventData.formData.privacy_preference;
    
                    // Update Icons
                    (function () {
                      const icons = dom.find.children(indicator, 'class', 'icon');
    
                      for (let icon of icons) {
                        isHidden(icon, !dom.has(icon, 'class', preference));
                      }
                    })();
                    // Update Tooltip
                    (function () {
                      const statuses = dom.find.children(tooltip, 'class', 'status');
    
                      for (let status of statuses) {
                        isHidden(status, !dom.has(status, 'class', preference));
                      }
                    })();
                  }
                }
              }
            }
          }
        });
      })();

      // Configure Present Profile Cards
      (function () {
        const profileCards = dom.find.children(document.body, 'class', 'profile-card');

        for (let profileCardElement of profileCards) {
          const user = (function () {
            const user = dom.get(profileCardElement, 'attr', 'data-card-user');

            if (user) {
              const jsonData = tryJSONParse(user, 'ignore');
  
              if (jsonData && typeof jsonData == 'object') {
                return jsonData;
              }
              else {
                return user;
              }
            }
            else {
              console.warn(`Profile Card element \"${profileCardElement.id}\" did not provide any user data.`);
              return false;
            }
          })();

          if (!user) {
            continue;
          }

          const flags = (function () {
            let flags = 0;
            const providedFlags = dom.get(profileCardElement, 'attr', 'data-card-flags');

            if (providedFlags) {
              const flagList = providedFlags.split('|');

              for (let flag of flagList) {
                let flagValue = cardObj[(flag.toUpperCase())];

                if (flagValue !== undefined) {
                  flags = flags|flagValue;
                }
              }
            }

            return flags;
          })();

          try {
            cardObj.create_card(user, (profile_card) => {
              profileCardElement.parentNode.replaceChild(profile_card, profileCardElement);
            }, flags);
          }
          catch (error) {
            console.error(error);
            continue;
          }
        }
      })();
    }
  }, 250);
})();