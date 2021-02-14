function _createForOfIteratorHelper(e,t){var a;if("undefined"==typeof Symbol||null==e[Symbol.iterator]){if(Array.isArray(e)||(a=_unsupportedIterableToArray(e))||t&&e&&"number"==typeof e.length){a&&(e=a);function r(){}var n=0;return{s:r,n:function(){return n>=e.length?{done:!0}:{done:!1,value:e[n++]}},e:function(e){throw e},f:r}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var o,i=!0,d=!1;return{s:function(){a=e[Symbol.iterator]()},n:function(){var e=a.next();return i=e.done,e},e:function(e){d=!0,o=e},f:function(){try{i||null==a.return||a.return()}finally{if(d)throw o}}}}function _unsupportedIterableToArray(e,t){if(e){if("string"==typeof e)return _arrayLikeToArray(e,t);var a=Object.prototype.toString.call(e).slice(8,-1);return"Object"===a&&e.constructor&&(a=e.constructor.name),"Map"===a||"Set"===a?Array.from(e):"Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)?_arrayLikeToArray(e,t):void 0}}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var a=0,r=new Array(t);a<t;a++)r[a]=e[a];return r}function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}!function(){var t=setInterval(function(){var a,r,i;function e(e){var t=e.formEventData,a=t.fieldProps.info.name;"change_username"==dom.get(t.field.form,"attr","data-form-name")&&"username"==a&&t.fieldValue!=t.fieldProps.info.defaultValue&&(r.savedRequests.setRequestParameter("check_username","username",t.fieldValue),r.request("check_username"))}"undefined"!=typeof globalFunctionsReady&&"undefined"!=typeof moment&&"undefined"!=typeof ShiftCodesTK&&ShiftCodesTK.requests.isLoaded&&(clearInterval(t),ShiftCodesTK.profile_card={CARD_HIDE_BORDER:1,CARD_HIDE_PROFILE_PICTURE:2,CARD_HIDE_USERNAME:4,CARD_HIDE_USER_ID:8,CARD_SHOW_ROLES:16,CARD_SHOW_STATS:32,CARD_SHOW_ACTIONS:64,CARD_ALLOW_EDITING:128,template:dom.find.id("profile_card_template"),card_data_template:{user_data:{id:null,username:null,profile_picture:null,profile_stats_preference:null,roles:[]},permissions:{can_report:!1,can_enforce:!1,can_edit:!1,can_change_username:!1},profile_stats:{last_public_activity:null,creation_date:null,shift_codes_submitted:null}},stored_data:{},store_data:function(e,t){var a=1<arguments.length&&void 0!==t&&t;if("object"!=_typeof(e)||!e.user_data||!e.user_data.id)return console.error("An invalid Profile Card Data Object was provided."),!1;var r=e.user_data.id;return!(this.stored_data[r]&&!a)&&(this.stored_data[r]={data:e,max_age:moment().add(10,"minutes").valueOf()},!0)},get_stored_data:function(e){var t=this.stored_data[e];if(t){if(moment().valueOf()<this.stored_data[e].max_age)return t.data;delete this.stored_data[e]}return!1},create_card:function(e,v,t){var a,y=2<arguments.length&&void 0!==t?t:0,g=ShiftCodesTK.profile_card,S=ShiftCodesTK.layers,E=(a=edit.copy(g.template),a=dom.find.id("temp").appendChild(a)),T=E.id,b=randomID("profile_card_",100,1e3);function r(i){var e,t,a,r,n,o,d,s,l,c,f,u;if(E.id=b,edit.attr(E,"update","data-user-id",i.user_data.id),E.innerHTML=E.innerHTML.replaceAll(T,b),E.innerHTML=E.innerHTML.replaceAll("${user_id}",i.user_data.id),E.innerHTML=E.innerHTML.replaceAll("${username}",i.user_data.username),S.setupChildLayers(E),multiView_setup_children(E),multiView_setup(E),y&g.CARD_HIDE_BORDER&&edit.class(E,"add","hide-border"),n=dom.find.child(E,"class","profile-picture"),o=i.user_data.profile_picture,y&g.CARD_HIDE_PROFILE_PICTURE?deleteElement(E):(e=dom.find.child(n,"tag","img"),t=dom.find.child(n,"class","placeholder"),o?(deleteElement(t),edit.attr(e,"add","src","/assets/img/users/profiles/".concat(i.user_data.id,"/").concat(o,"?_request_token=").concat(requestToken.get(),"&size=128"))):(a=r=(r=i.user_data.username.match(/[A-Z]/g))?(r=r.slice(0,2)).join(""):(r=username.slice(0,2)).toUpperCase(),deleteElement(e),t.innerHTML=a)),y&g.CARD_HIDE_USERNAME&&deleteElement(dom.find.child(E,"class","user-name")),y&g.CARD_HIDE_USER_ID&&deleteElement(dom.find.child(E,"class","user-id")),i.user_data.roles){var m,_=_createForOfIteratorHelper(dom.find.child(E,"class","section roles").childNodes);try{for(_.s();!(m=_.n()).done;){var p=m.value,h=dom.get(p,"attr","data-role");h&&(-1!=i.user_data.roles.indexOf(h)?edit.class(E,"add","role-".concat(h)):(S.detachLayers(p),deleteElement(p)))}}catch(e){_.e(e)}finally{_.f()}}return 0==(y&g.CARD_SHOW_ROLES)&&deleteElement(dom.find.child(E,"class","section roles")),function(){var e=dom.find.child(E,"class","section stats");if(y&g.CARD_SHOW_STATS){var t=["last_public_activity","creation_date"],a=!1;for(var r in i.profile_stats){var n,o=i.profile_stats[r];null!==o&&(n=dom.find.child(dom.find.child(e,"class","definition ".concat(r)),"tag","dd"),a=!0,-1!=t.indexOf(r)?(edit.attr(n,"update","data-relative-date",o),ShiftCodesTK.relative_dates.refresh_element(n),ShiftCodesTK.layers.updateTooltip(n,moment(o).format("MMMM DD, YYYY hh:mm A [UTC]"))):n.innerHTML=o)}g.CARD_SHOW_ACTIONS,g.CARD_ALLOW_EDITING,a||deleteElement(e)}else deleteElement(e)}(),u=dom.find.child(E,"class","section actions"),y&g.CARD_SHOW_ACTIONS?(i.permissions.can_report||(d=dom.find.child(u,"class","report"),S.detachLayers(d),deleteElement(d)),i.permissions.can_enforce||(s=dom.find.child(u,"class","enforcement"),S.detachLayers(s),deleteElement(s)),c=dom.find.child(E,"class","section stats"),f=dom.find.child(c,"class","stat-privacy"),y&g.CARD_ALLOW_EDITING&&i.permissions.can_edit?setTimeout(function(){var e,t,a,r;e=f.nextElementSibling,(t=dom.find.child(e,"class","multi-view"))&&multiView_update(dom.find.child(t,"class",i.user_data.profile_stats_preference),!0),a=dom.find.child(E,"attr","data-value","change-username"),r=dom.find.parent(a,"class","multi-view"),i.permissions.can_change_username||multiView_update(dom.find.child(r,"class","disabled"),!0)},10):(l=dom.find.child(u,"class","edit-profile"),S.detachLayers(l),deleteElement(l))):deleteElement(u),ShiftCodesTK.forms.setupChildForms(E),E=deleteElement(E),v(E),1}if("string"==typeof e){var n=g.get_stored_data(e);return n?r(n):ShiftCodesTK.requests.request({type:"GET",path:"/assets/requests/get/account/profile-card-data",parameters:{user_id:e},callback:function(e){e&&200==e.status_code&&(g.store_data(e.payload),r(e.payload))}}),!0}if("object"!=_typeof(e)||void 0===e.user_data)return!1;var o=mergeObj(g.card_data_template,e);return g.store_data(o),r(o),!0},get_card_modal:function(e,t,a){var r=1<arguments.length&&void 0!==t?t:null,n=2<arguments.length&&void 0!==a?a:this.CARD_SHOW_ROLES|this.CARD_SHOW_STATS;return this.create_card(e,function(e){var t,a=dom.find.id("profile_card_modal");(t=dom.find.child(a,"class","body"),dom.find.child(t,"class","content-container")).innerHTML=e.outerHTML,r?r(a):ShiftCodesTK.modals.toggleModal(a,!0)},n)}},cardObj=ShiftCodesTK.profile_card,i=["change_username","profile_stats_privacy"],window.addEventListener("tkFormsFormAfterSubmit",function(e){var t,a,r=e.formEventData,n=r.formProps.info.name,o=r.formResponseData.payload;-1!=i.indexOf(n)&&o&&o.form&&o.form.result&&(t=dom.find.parent(e.target,"class","profile-card"),a=dom.find.child(t,"class","view primary"),multiView_update(a))}),a=ShiftCodesTK.forms,(r=ShiftCodesTK.requests).savedRequests.saveRequest("profile_card_check_username",{parameters:{username:""},request:{path:"/assets/requests/get/account/check_username_availability",callback:function(e){var t;e&&void 0!==e.payload&&!1===e.payload&&(t=a.getField(dom.find.id("change_username"),"username"),a.reportFieldIssue(t,"This username is already in use."))}}}),window.addEventListener("tkFormsFieldTimeout",e),window.addEventListener("tkFormsFieldCommit",e),window.addEventListener("tkFormsFormBeforeSubmit",function(e){var t=e.formEventData,a=ShiftCodesTK.requests;"change_username"==t.formProps.info.name&&a.savedRequests.getRequest("check_username").activeRequest}),window.addEventListener("tkFormsFormAfterSubmit",function(e){var t=e.formEventData;if("change_username"==t.formProps.info.name){var a=t.formResponseData;if(a&&void 0!==a.payload&&a.payload.form.result){var r,n=dom.find.children(document.body,"class","profile-card"),o=a.payload.can_change_username_again,i=dom.get(dom.find.parent(t.originalEvent.target,"class","profile-card"),"attr","data-user-id"),d=_createForOfIteratorHelper(n);try{for(d.s();!(r=d.n()).done;){var s,l,c=r.value;dom.has(c,"attr","data-user-id",i)&&(s=dom.find.child(c,"class","definition user-name"),dom.find.child(s,"tag","dd").innerHTML=a.payload.new_username,o||(l=dom.find.child(c,"class","view-toggle change-username"))&&(isDisabled(l,!0),ShiftCodesTK.layers.updateTooltip(l,"You can only change your username&nbsp;<em>twice</em>&nbsp;every&nbsp;<em>24 hours</em>.")))}}catch(e){d.e(e)}finally{d.f()}}}}),window.addEventListener("tkFormsFormAfterSubmit",function(e){var r,n,t,o,a=e.formEventData;if("profile_stats_privacy"==a.formProps.info.name){var i=a.formResponseData;if(i&&void 0!==i.payload&&i.payload.form.result){var d,s=_createForOfIteratorHelper(dom.find.children(document.body,"class","profile-card"));try{for(s.s();!(d=s.n()).done;)o=t=n=r=void 0,t=d.value,(o=dom.find.child(t,"class","view-toggle stat-privacy"))&&(r=o.nextElementSibling,n=a.formData.privacy_preference,function(){var e,t=_createForOfIteratorHelper(dom.find.children(o,"class","icon"));try{for(t.s();!(e=t.n()).done;){var a=e.value;isHidden(a,!dom.has(a,"class",n))}}catch(e){t.e(e)}finally{t.f()}}(),function(){var e,t=_createForOfIteratorHelper(dom.find.children(r,"class","status"));try{for(t.s();!(e=t.n()).done;){var a=e.value;isHidden(a,!dom.has(a,"class",n))}}catch(e){t.e(e)}finally{t.f()}}())}catch(e){s.e(e)}finally{s.f()}}}}),function(){var a,t=_createForOfIteratorHelper(dom.find.children(document.body,"class","profile-card"));try{for(t.s();!(a=t.n()).done;)(function(){var i=a.value,e=function(){var e=dom.get(i,"attr","data-card-user");if(e){var t=tryJSONParse(e,"ignore");return t&&"object"==_typeof(t)?t:e}return console.warn('Profile Card element "'.concat(i.id,'" did not provide any user data.')),!1}();if(!e)return;var t=function(){var e=0,t=dom.get(i,"attr","data-card-flags");if(t){var a,r=_createForOfIteratorHelper(t.split("|"));try{for(r.s();!(a=r.n()).done;){var n=a.value,o=cardObj[n.toUpperCase()];void 0!==o&&(e|=o)}}catch(e){r.e(e)}finally{r.f()}}return e}();try{cardObj.create_card(e,function(e){i.parentNode.replaceChild(e,i)},t)}catch(e){return console.error(e)}})()}catch(e){t.e(e)}finally{t.f()}}())},250)}();