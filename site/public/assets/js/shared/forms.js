function _createForOfIteratorHelper(e,t){var r;if("undefined"==typeof Symbol||null==e[Symbol.iterator]){if(Array.isArray(e)||(r=_unsupportedIterableToArray(e))||t&&e&&"number"==typeof e.length){r&&(e=r);function a(){}var o=0;return{s:a,n:function(){return o>=e.length?{done:!0}:{done:!1,value:e[o++]}},e:function(e){throw e},f:a}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var n,i=!0,d=!1;return{s:function(){r=e[Symbol.iterator]()},n:function(){var e=r.next();return i=e.done,e},e:function(e){d=!0,n=e},f:function(){try{i||null==r.return||r.return()}finally{if(d)throw n}}}}function _unsupportedIterableToArray(e,t){if(e){if("string"==typeof e)return _arrayLikeToArray(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?_arrayLikeToArray(e,t):void 0}}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,a=new Array(t);r<t;r++)a[r]=e[r];return a}function formInputMouseEvent(e){var t,r,d,a=e.target,l=dom.find.parents(a,"tag","fieldset");l&&(t=function(t,r){function e(e){"add"==t?edit.class(e,"add",r):"delete"==t&&edit.class(e,"remove",r)}if(d){var a,o=_createForOfIteratorHelper(d);try{for(o.s();!(a=o.n()).done;){if(e(a.value),"focus"==r)break}}catch(e){o.e(e)}finally{o.f()}}var n,i=_createForOfIteratorHelper(l);try{for(i.s();!(n=i.n()).done;){e(n.value)}}catch(e){i.e(e)}finally{i.f()}},r=e.type,d=dom.find.parents(a,"class","field"),"mouseover"==r?t("add","hover"):"mouseout"==r&&t("delete","hover"),"focusin"==r?t("add","focus"):"focusout"==r&&t("delete","focus"))}function formUpdateCharCounter(e){var t={max:tryParseInt(e.maxLength),now:e.value.length};t.left=t.max-t.now;var r=document.getElementById(e.id.replace("_input","_char_counter"));r.innerHTML=t.left,updateLabel(r,"".concat(t.left,"/").concat(t.max," characters remaining"))}function formToggleDetailsEvent(e){var t=dom.find.parent(e,"tag","form"),r=dom.has(t,"class","hide-details"),a={false:"Hide",true:"Show"};return!!t&&(edit.class(t,"toggle","hide-details"),e.innerHTML=e.innerHTML.replace(a[r],a[!r]),updateLabel(e,e.title.replace(a[r],a[!r])),!0)}function formGetField(e,t){try{var r;return function(){if(void 0===e||!dom.has(e,"tag","form"))throw"A valid target form must be provided.";if(void 0===t||0==t.trim().length)throw"A valid field name must be provided."}(),((r=dom.find.child(e,"attr","name",t))||!!(r=dom.find.child(e,"attr","name","".concat(t,"[]"))))&&r}catch(e){return console.error("formGetField Error: ".concat(e)),!1}}function formAddAlert(){var e,t,r,a,o,n=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{};try{var i={info:"fa-info-circle",warning:"fa-exclamation-triangle",error:"fa-exclamation-circle"},d=mergeObj({form:null,target:!1,type:"error",message:null},n);return function(){if(null===d.form||!dom.has(d.form,"tag","form")||!dom.has(d.form,"class","configured"))throw"A valid target form must be provided.";if(!1===d.target||formGetField(d.form,d.target)||(console.warn('formAddAlert Warning: Field "'.concat(d.target,'" could not be found.')),d.target=!1),-1==["info","warning","error"].indexOf(d.type))throw"A valid type must be provided.";if(null===d.message||"string"!=typeof d.message||0==d.message.trim().length)throw"A valid message must be provided."}(),dom.has(d.form,"class","hide-alerts")?l():(e=dom.find.child(d.form,"class","alerts"),o=edit.copy(dom.find.id("form_alert_template")),edit.class(dom.find.child(dom.find.child(o,"class","icon"),"class","box-icon"),"add","fas ".concat(i[d.type])),dom.find.child(o,"class","message").innerHTML=d.message,t=o,!1!==d.target?(r=formGetField(d.form,d.target),(a=dom.find.child(dom.find.parent(r,"class","field"),"class","alerts"))?a.appendChild(t):e?e.appendChild(t):l()):e?e.appendChild(t):l()),!0}catch(e){return console.error("formAddAlert Error: ".concat(e)),!1}function l(){var e,t=newToast({settings:{id:"form_alert_toast_"+randomNum(100,9999),duration:"infinite"},content:{icon:"fas ".concat(i[d.type]),title:ucWords(d.type),body:d.message},action:{use:!1!==d.target&&formGetField(d.form,d.target),type:"button",action:function(e){var t,r,a=dom.get(e.target,"attr","data-form"),o=dom.get(e.target,"attr","data-field");a&&o&&(!(t=dom.find.id(a))||(r=formGetField(t,o))&&(e.preventDefault(),r.focus()))},name:"Show Field",label:"Show the associated field"}});t&&(e=dom.find.child(t,"class","action"),edit.attr(e,"add","data-form",d.form.id),edit.attr(e,"add","data-field",d.target))}}function formToggleState(e){var t=dom.find.children(e,"group","focusables"),r=dom.has(e,"attr","disabled")&&dom.has(e,"class","disabled-by-submit");edit.class(e,"toggle","disabled-by-submit"),isDisabled(e);var a,o=_createForOfIteratorHelper(t);try{for(o.s();!(a=o.n()).done;){var n=a.value;(!r&&!n.disabled||r&&dom.has(n,"class","disabled-by-submit"))&&(edit.class(n,"toggle","disabled-by-submit"),isDisabled(n))}}catch(e){o.e(e)}finally{o.f()}return!r}function formHandleSubmit(e){var s=e.target,c=e.submitter,f=dom.find.children(s,"group","focusables"),m={},t=dom.has(s,"class","ajax-submit"),r=dom.has(s,"class","use-ajax-progress-bar");if(!0,e.preventDefault(),e.stopImmediatePropagation(),t){formToggleState(s),dom.find.child(s,"class","alerts").innerHTML=[],r&&lpbUpdate(20);for(var a=0;a<f.length;a++)(function(e){var t,r,a=f[e],o="fieldset"==(t=dom.get(a,"tag"))||"select"==t||"a"==t||"button"==t?t:dom.get(a,"attr","type"),n=dom.get(a,"attr","name");if("a"==o||a.disabled&&!dom.has(a,"class","disabled-by-submit"))return;if("fieldset"==o&&0<a.innerHTML.trim().length)m[n]=a.innerHTML;else if("select"==o)for(var i=dom.find.children(a,"tag","option"),d=0;d<i.length;d++){var l=i[d];if(l.selected){m[n]=l.value;break}}else{"radio"==o||"checkbox"==o?("radio"==o?dom.find.children(s,"class","radio"):dom.find.children(s,"class","checkbox"),r=n.indexOf("[]")==n.length-2,a.checked&&(r&&!m[n]&&(m[n]=[]),r?m[n].push(a.value):m[n]=a.value)):0<a.value.trim().length&&("button"==o&&a!=c||(m[n]=a.value))}})(a);newAjaxRequest({type:dom.get(s,"attr","method"),file:dom.get(s,"attr","action"),requestHeader:"form",params:m,callback:function(e){response=tryJSONParse(e),response?function(){response.statusCode;for(var t,e=response.payload.form_result,o=[response.payload.info_messages,response.warnings,response.errors],n=0,i=o;n<i.length;n++)!function(){var e=i[n];if(e){var t,r=_createForOfIteratorHelper(e);try{for(r.s();!(t=r.n()).done;){var a=t.value;formAddAlert({form:s,target:!!a.parameter&&a.parameter,type:e==o[0]?"info":e==o[1]?"warning":e==o[2]?"error":void 0,message:a.message})}}catch(e){r.e(e)}finally{r.f()}}}();e?((t=response.payload.form_result_actions).toast&&newToast(t.toast),t.redirect&&setTimeout(function(){setTimeout(function(){var e=new RegExp("^reload$");window.location=decodeURIComponent(t.redirect.location.replace(e,""))},t.redirect.delay)},250)):setTimeout(function(){formToggleState(s)},500)}():newToast({settings:{duration:"infinite",template:"formResult"},content:{icon:"fas fa-exclamation-triangle",title:"Request Error",body:"We could not process and display the form response due to an error. Your information may or may not have been submitted properly."},action:{use:!0,type:"link",link:" ",name:"Refresh",label:"Refresh the current page"}}),r&&lpbUpdate(100)}}),r&&lpbUpdate(90,!0)}else requestToken.check(function(){s.submit()})}function formSetup(e){var t;t=""!=e.id?e.id:"form_".concat(randomNum(100,1e3)),e.id=t;edit.class(e,"add","configured")}function formUpdateField(f,m,u){var e,t,g=(e=dom.find.child(f,"attr","name",m))||(m+="[]",e=dom.find.child(f,"attr","name",m))?e:(m=m.slice(0,-2),!1),r=!!g&&dom.get(g,"tag"),h=!!g&&(t=dom.get(g,"attr","type"),"select"==r||"textarea"==r?r:t),p=!!g&&("select"==h||"checkbox"==h||"radio"==h?"multi":"single"),v=null!=u&&u.constructor.name.toLowerCase();return g?"array"==v&&"single"==p?(console.error('formUpdateField Error: The value for "'.concat(m,'" can only be an Array for Select, Radio, or Checkbox fields.')),!1):(function(){var e=u||"";if("single"==p)edit.attr(g,"update","value",e);else{var t="select"==h?dom.find.children(g,"tag","option"):dom.find.children(f,"attr","name",m),r="select"==h?"selected":"checked";if("select"==h){var a,o=_createForOfIteratorHelper(t);try{for(o.s();!(a=o.n()).done;){var n=a.value;edit.attr(n,"remove","selected")}}catch(e){o.e(e)}finally{o.f()}}var i,d=_createForOfIteratorHelper(t);try{for(d.s();!(i=d.n()).done;){var l=i.value,s=l.value,c=("string"==v?s==e:-1!=e.indexOf(s))?"add":"remove";edit.attr(l,c,r)}}catch(e){d.e(e)}finally{d.f()}}}(),!0):(console.error('formUpdateField Error: The field "'.concat(m,'" was not found in form "').concat(f.id,'".')),!1)}function formToggleField(e,t,r){try{var a=(c=dom.find.child(e,"attr","name",t))||(t+="[]",c=dom.find.child(e,"attr","name",t))?c:(t=t.slice(0,-2),!1);if(a){for(var o=dom.find.parent(a,"class","field"),n=0,i=["disabled","hidden","readonly","required"];n<i.length;n++)s=l=d=void 0,l=i[n],void 0!==(s=r[l])&&(d="toggle"==s?"toggle":!0===s?"add":!1===s?"remove":void 0,edit.attr(a,d,l),edit.class(o,d,l),"hidden"==l&&edit.attr(dom.find.parent(o,"tag","fieldset"),d,l));return!0}throw'formUpdateField: The field "'.concat(t,'" was not found in form "').concat(e.id,'".')}catch(e){return console.error(e),!1}var d,l,s,c}!function(){var e=setInterval(function(){globalFunctionsReady&&(clearInterval(e),function(){var e,t=_createForOfIteratorHelper(document.forms);try{for(t.s();!(e=t.n()).done;){var r=e.value;dom.has(r,"class","no-auto-config")||dom.has(r,"class","configured")||formSetup(r)}}catch(e){t.e(e)}finally{t.f()}}(),window.addEventListener("submit",formHandleSubmit),function(){for(var e=0,t=["mouseover","mouseout","focusin","focusout"];e<t.length;e++){var r=t[e];window.addEventListener(r,formInputMouseEvent)}}(),window.addEventListener("click",function(e){var t=e.target;dom.has(t,"tag","button")&&dom.has(t,"class","form-details-toggle")&&formToggleDetailsEvent(t)}))},250)}(),ShiftCodesTK.forms={getFormData:function(e,t){var d,l=1<arguments.length&&void 0!==t&&t,r=dom.find.children(e,"group","focusables"),s={},a=_createForOfIteratorHelper(r);try{for(a.s();!(d=a.n()).done;)(function(){var e,t=d.value,r=(e=dom.get(t,"tag"),-1!=["fieldset","select","a","button"].indexOf(e)?e:dom.get(t,"attr","type")),a=dom.get(t,"attr","name");if("a"==r||t.disabled&&!dom.has(t,"class","disabled-by-script"))return;if(-1!=["radio","checkbox"].indexOf(r))t.checked&&(a.indexOf("[]")==a.length-2?(void 0===s[a]&&(s[a]=[]),s[a].push(t.value)):s[a]=t.value);else if("select"==r){var o,n=_createForOfIteratorHelper(dom.find.children(t,"tag","option"));try{for(n.s();!(o=n.n()).done;){var i=o.value;if(i.selected){s[a]=i.value;break}}}catch(e){n.e(e)}finally{n.f()}}else"textarea"==r?0<t.innerHTML.trim().length&&(s[a]=t.innerHTML):"button"==r&&t!=l||0<t.value.trim().length&&(s[a]=t.value)})()}catch(e){a.e(e)}finally{a.f()}return s}};