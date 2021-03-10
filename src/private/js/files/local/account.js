// (function () {
//   let interval = setInterval(function () {
//     if (typeof globalFunctionsReady != 'undefined' && typeof moment != 'undefined' && typeof ShiftCodesTK != 'undefined' && ShiftCodesTK.requests.isLoaded) {
//       clearInterval(interval);
      
//       // Update stat timestamps
//       // (function () {
//       //   let main = dom.find.child(document.body, 'tag', 'main');
//       //   let profile = dom.find.child(main, 'class', 'profile-card');
//       //   let stats = dom.find.children(dom.find.child(profile, 'class', 'stats'), 'class', 'definition');
  
//       //   for (let i = 0; i < stats.length; i++) {
//       //     let stat = stats[i];
  
//       //     if (stat.className.indexOf('date') != -1) {
//       //       let def = dom.find.child(stat, 'tag', 'dd');
//       //       let date = moment.utc(dom.get(def, 'attr', 'data-ts'));
    
//       //       updateLabel(def, date.format('MMMM DD, YYYY'), [ 'aria' ]);
//       //       ShiftCodesTK.layers.updateTooltip(def, date.format('MMMM DD, YYYY'));
//       //       def.innerHTML = ucWords(date.fromNow(true)) + ' ago';
//       //       edit.attr(def, 'remove', 'data-ts');
//       //     }
//       //   }
//       // })();
//       // View Toggles
//       (function () {
//         const toggleForms = [
//           'change_username',
//           'profile_stats_privacy'
//         ];

//         function handleViewToggleForForms (event) {
//           const formEventData = event.formEventData;
//           const formName = formEventData.formProps.info.name;
//           const payload = formEventData.formResponseData.payload;
          
//           if (toggleForms.indexOf(formName) != -1) {
//             const profileCard = dom.find.parent(event.target, 'class', 'profile-card');
//             const view = dom.find.child(profileCard, 'class', 'view primary');
  
//             multiView_update(view);
//           }
//         }

//         // // window.addEventListener('tkFormsFormBeforeReset', handleViewToggleForForms);
//         window.addEventListener('tkFormsFormAfterSubmit', handleViewToggleForForms);
//       })();
//       // Username
//       (function () {
//         const formsObject = ShiftCodesTK.forms;
//         const requestsObject = ShiftCodesTK.requests;

//         requestsObject.savedRequests.saveRequest('check_username', {
//           parameters: {
//             username: ''
//           },
//           request: {
//             path: '/assets/requests/get/account/check_username_availability',
//             callback: (response) => {
//               if (response && response.payload !== undefined) {
//                 if (response.payload === false) {
//                   const field = formsObject.getField(dom.find.id('change_username'), 'username');

//                   // formsObject.addAlert(field, 'This username is already in use.');
//                   formsObject.reportFieldIssue(field, 'This username is already in use.');
//                 }
//               }
//             }
//           }
//         });
        

//         function checkUsernameForAvailability (event) {
//           const formEventData = event.formEventData;
//           const fieldName = formEventData.fieldProps.info.name;
//           const formName = dom.get(formEventData.field.form, 'attr', 'data-form-name');

//           if (formName == 'change_username' && fieldName == 'username') {
//             if (formEventData.fieldValue != formEventData.fieldProps.info.defaultValue) {
//               requestsObject.savedRequests.setRequestParameter('check_username', 'username', formEventData.fieldValue);
//               requestsObject.request('check_username');
//             }
//           }
//         }

//         window.addEventListener('tkFormsFieldTimeout', checkUsernameForAvailability);
//         window.addEventListener('tkFormsFieldCommit', checkUsernameForAvailability);
//         window.addEventListener('tkFormsFormBeforeSubmit', (event) => {
//           const formEventData = event.formEventData;
//           const requestsObject = ShiftCodesTK.requests;

//           if (formEventData.formProps.info.name == 'change_username') {
//             const request = requestsObject.savedRequests.getRequest('check_username');
//             // console.log(requestsObject.activeRequests.getRequest(request.activeRequest));

//             if (request.activeRequest) {
//               // requestsObject.activeRequests.removeRequest(request.activeRequest);
//             }
//           }
//         });
//         window.addEventListener('tkFormsFormAfterSubmit', (event) => {
//           const formEventData = event.formEventData;

//           if (formEventData.formProps.info.name == 'change_username') {
//             const formResponseData = formEventData.formResponseData;

//             if (formResponseData && formResponseData.payload !== undefined) {
//               if (formResponseData.payload.form.result) {
//                 const profileCards = dom.find.children(document.body, 'class', 'profile-card');
//                 const canChangeUsernameAgain = formResponseData.payload.can_change_username_again;
//                 const userID = dom.get(dom.find.parent(formEventData.originalEvent.target, 'class', 'profile-card'), 'attr', 'data-user-id');

//                 for (let profileCard of profileCards) {
//                   if (dom.has(profileCard, 'attr', 'data-user-id', userID)) {
//                     let username = dom.find.child(profileCard, 'class', 'definition user-name');
  
//                     dom.find.child(username, 'tag', 'dd').innerHTML = formResponseData.payload.new_username;
  
//                     if (!canChangeUsernameAgain) {
//                       let button = dom.find.child(profileCard, 'class', 'view-toggle change-username');
  
//                       if (button) {
//                         isDisabled(button, true);
//                         ShiftCodesTK.layers.updateTooltip(button, 'You can only change your username&nbsp;<em>twice</em>&nbsp;every&nbsp;<em>24 hours</em>.');
//                       }
//                     }
//                   }

//                 }
//               }
//             }
//           }
//         });
//       })();
//       // Profile Stats
//       (function () {
//         window.addEventListener('tkFormsFormAfterSubmit', (event) => {
//           const formEventData = event.formEventData;

//           if (formEventData.formProps.info.name == 'profile_stats_privacy') {
//             const formResponseData = formEventData.formResponseData;

//             if (formResponseData && formResponseData.payload !== undefined) {
//               if (formResponseData.payload.form.result) {
//                 const profileCards = dom.find.children(document.body, 'class', 'profile-card');

//                 for (let profileCard of profileCards) {
//                   const indicator = dom.find.child(profileCard, 'class', 'view-toggle stat-privacy');
                  
//                   if (indicator) {
//                     const tooltip = indicator.nextElementSibling;
//                     const preference = formEventData.formData.privacy_preference;
    
//                     // Update Icons
//                     (function () {
//                       const icons = dom.find.children(indicator, 'class', 'icon');
    
//                       for (let icon of icons) {
//                         isHidden(icon, !dom.has(icon, 'class', preference));
//                       }
//                     })();
//                     // Update Tooltip
//                     (function () {
//                       const statuses = dom.find.children(tooltip, 'class', 'status');
    
//                       for (let status of statuses) {
//                         isHidden(status, !dom.has(status, 'class', preference));
//                       }
//                     })();
//                   }
//                 }
//               }
//             }
//           }
//         });
//       })();
//     }
//   }, 250);
// })();