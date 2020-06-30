function formInputMouseEvent(e){var t,r,u,a=e.target,v=dom.find.parents(a,"tag","fieldset");v&&(t=function(t,r){function e(e){"add"==t?edit.class(e,"add",r):"delete"==t&&edit.class(e,"remove",r)}if(u){var a=!0,o=!1,n=void 0;try{for(var i,d=u[Symbol.iterator]();!(a=(i=d.next()).done);a=!0){if(e(i.value),"focus"==r)break}}catch(e){o=!0,n=e}finally{try{a||null==d.return||d.return()}finally{if(o)throw n}}}var l=!0,s=!1,f=void 0;try{for(var c,m=v[Symbol.iterator]();!(l=(c=m.next()).done);l=!0){e(c.value)}}catch(e){s=!0,f=e}finally{try{l||null==m.return||m.return()}finally{if(s)throw f}}},r=e.type,u=dom.find.parents(a,"class","field"),"mouseover"==r?t("add","hover"):"mouseout"==r&&t("delete","hover"),"focusin"==r?t("add","focus"):"focusout"==r&&t("delete","focus"))}function formUpdateCharCounter(e){var t={max:tryParseInt(e.maxLength),now:e.value.length};t.left=t.max-t.now;var r=document.getElementById(e.id.replace("_input","_char_counter"));r.innerHTML=t.left,updateLabel(r,"".concat(t.left,"/").concat(t.max," characters remaining"))}function formToggleDetailsEvent(e){var t=dom.find.parent(e,"tag","form"),r=dom.has(t,"class","hide-details"),a={false:"Hide",true:"Show"};return!!t&&(edit.class(t,"toggle","hide-details"),e.innerHTML=e.innerHTML.replace(a[r],a[!r]),updateLabel(e,e.title.replace(a[r],a[!r])),!0)}function formGetField(e,t){try{var r;return function(){if(void 0===e||!dom.has(e,"tag","form"))throw"A valid target form must be provided.";if(void 0===t||0==t.trim().length)throw"A valid field name must be provided."}(),((r=dom.find.child(e,"attr","name",t))||!!(r=dom.find.child(e,"attr","name","".concat(t,"[]"))))&&r}catch(e){return console.error("formGetField Error: ".concat(e)),!1}}function formAddAlert(){var e,t,r,a,o,n=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{};try{var i={info:"fa-info-circle",warning:"fa-exclamation-triangle",error:"fa-exclamation-circle"},d=mergeObj({form:null,target:!1,type:"error",message:null},n);return function(){if(null===d.form||!dom.has(d.form,"tag","form")||!dom.has(d.form,"class","configured"))throw"A valid target form must be provided.";if(!1===d.target||formGetField(d.form,d.target)||(console.warn('formAddAlert Warning: Field "'.concat(d.target,'" could not be found.')),d.target=!1),-1==["info","warning","error"].indexOf(d.type))throw"A valid type must be provided.";if(null===d.message||"string"!=typeof d.message||0==d.message.trim().length)throw"A valid message must be provided."}(),dom.has(d.form,"class","hide-alerts")?l():(e=dom.find.child(d.form,"class","alerts"),o=edit.copy(dom.find.id("form_alert_template")),edit.class(dom.find.child(dom.find.child(o,"class","icon"),"class","box-icon"),"add","fas ".concat(i[d.type])),dom.find.child(o,"class","message").innerHTML=d.message,t=o,!1!==d.target?(r=formGetField(d.form,d.target),(a=dom.find.child(dom.find.parent(r,"class","field"),"class","alerts"))?a.appendChild(t):e?e.appendChild(t):l()):e?e.appendChild(t):l()),!0}catch(e){return console.error("formAddAlert Error: ".concat(e)),!1}function l(){var e,t=newToast({settings:{id:"form_alert_toast_"+randomNum(100,9999),duration:"infinite"},content:{icon:"fas ".concat(i[d.type]),title:ucWords(d.type),body:d.message},action:{use:!1!==d.target&&formGetField(d.form,d.target),type:"button",action:function(e){var t,r,a=dom.get(e.target,"attr","data-form"),o=dom.get(e.target,"attr","data-field");a&&o&&(!(t=dom.find.id(a))||(r=formGetField(t,o))&&(e.preventDefault(),r.focus()))},name:"Show Field",label:"Show the associated field"}});t&&(e=dom.find.child(t,"class","action"),edit.attr(e,"add","data-form",d.form.id),edit.attr(e,"add","data-field",d.target))}}function formToggleState(e){var t=dom.find.children(e,"group","focusables"),r=dom.has(e,"attr","disabled")&&dom.has(e,"class","disabled-by-submit");edit.class(e,"toggle","disabled-by-submit"),isDisabled(e);var a=!0,o=!1,n=void 0;try{for(var i,d=t[Symbol.iterator]();!(a=(i=d.next()).done);a=!0){var l=i.value;(!r&&!l.disabled||r&&dom.has(l,"class","disabled-by-submit"))&&(edit.class(l,"toggle","disabled-by-submit"),isDisabled(l))}}catch(e){o=!0,n=e}finally{try{a||null==d.return||d.return()}finally{if(o)throw n}}return!r}function formHandleSubmit(e){var f=e.target,s=e.submitter,c=dom.find.children(f,"group","focusables"),m={},t=dom.has(f,"class","ajax-submit"),r=dom.has(f,"class","use-ajax-progress-bar");if(!0,e.preventDefault(),e.stopImmediatePropagation(),t){formToggleState(f),dom.find.child(f,"class","alerts").innerHTML=[],r&&lpbUpdate(20);for(var a=0;a<c.length;a++)(function(e){var t,r,a=c[e],o="fieldset"==(t=dom.get(a,"tag"))||"select"==t||"a"==t||"button"==t?t:dom.get(a,"attr","type"),n=dom.get(a,"attr","name");if("a"==o||a.disabled&&!dom.has(a,"class","disabled-by-submit"))return;if("fieldset"==o&&0<a.innerHTML.trim().length)m[n]=a.innerHTML;else if("select"==o)for(var i=dom.find.children(a,"tag","option"),d=0;d<i.length;d++){var l=i[d];if(l.selected){m[n]=l.value;break}}else{"radio"==o||"checkbox"==o?("radio"==o?dom.find.children(f,"class","radio"):dom.find.children(f,"class","checkbox"),r=n.indexOf("[]")==n.length-2,a.checked&&(r&&!m[n]&&(m[n]=[]),r?m[n].push(a.value):m[n]=a.value)):0<a.value.trim().length&&("button"==o&&a!=s||(m[n]=a.value))}})(a);newAjaxRequest({type:dom.get(f,"attr","method"),file:dom.get(f,"attr","action"),requestHeader:"form",params:m,callback:function(e){response=tryJSONParse(e),response?function(){response.statusCode;for(var t,e=response.payload.form_result,d=[response.payload.info_messages,response.warnings,response.errors],l=0,s=d;l<s.length;l++)!function(){var e=s[l];if(e){var t=!0,r=!1,a=void 0;try{for(var o,n=e[Symbol.iterator]();!(t=(o=n.next()).done);t=!0){var i=o.value;formAddAlert({form:f,target:!!i.parameter&&i.parameter,type:e==d[0]?"info":e==d[1]?"warning":e==d[2]?"error":void 0,message:i.message})}}catch(e){r=!0,a=e}finally{try{t||null==n.return||n.return()}finally{if(r)throw a}}}}();e?((t=response.payload.form_result_actions).toast&&newToast(t.toast),t.redirect&&setTimeout(function(){setTimeout(function(){var e=new RegExp("^reload$");window.location=decodeURIComponent(t.redirect.location.replace(e,""))},t.redirect.delay)},250)):setTimeout(function(){formToggleState(f)},500)}():newToast({settings:{duration:"infinite",template:"formResult"},content:{icon:"fas fa-exclamation-triangle",title:"Request Error",body:"We could not process and display the form response due to an error. Your information may or may not have been submitted properly."},action:{use:!0,type:"link",link:" ",name:"Refresh",label:"Refresh the current page"}}),r&&lpbUpdate(100)}}),r&&lpbUpdate(90,!0)}else requestToken.check(function(){f.submit()})}function formSetup(e){var t;t=""!=e.id?e.id:"form_".concat(randomNum(100,1e3)),e.id=t;edit.class(e,"add","configured")}function formUpdateField(p,y,b){var e,t,w=(e=dom.find.child(p,"attr","name",y))||(y+="[]",e=dom.find.child(p,"attr","name",y))?e:(y=y.slice(0,-2),!1),r=!!w&&dom.get(w,"tag"),x=!!w&&(t=dom.get(w,"attr","type"),"select"==r||"textarea"==r?r:t),T=!!w&&("select"==x||"checkbox"==x||"radio"==x?"multi":"single"),S=null!=b&&b.constructor.name.toLowerCase();return w?"array"==S&&"single"==T?(console.error('formUpdateField Error: The value for "'.concat(y,'" can only be an Array for Select, Radio, or Checkbox fields.')),!1):(function(){var e=b||"";if("single"==T)edit.attr(w,"update","value",e);else{var t="select"==x?dom.find.children(w,"tag","option"):dom.find.children(p,"attr","name",y),r="select"==x?"selected":"checked";if("select"==x){var a=!0,o=!1,n=void 0;try{for(var i,d=t[Symbol.iterator]();!(a=(i=d.next()).done);a=!0){var l=i.value;edit.attr(l,"remove","selected")}}catch(e){o=!0,n=e}finally{try{a||null==d.return||d.return()}finally{if(o)throw n}}}var s=!0,f=!1,c=void 0;try{for(var m,u=t[Symbol.iterator]();!(s=(m=u.next()).done);s=!0){var v=m.value,g=v.value,h=("string"==S?g==e:-1!=e.indexOf(g))?"add":"remove";edit.attr(v,h,r)}}catch(e){f=!0,c=e}finally{try{s||null==u.return||u.return()}finally{if(f)throw c}}}}(),!0):(console.error('formUpdateField Error: The field "'.concat(y,'" was not found in form "').concat(p.id,'".')),!1)}function formToggleField(e,t,r){try{var a=(f=dom.find.child(e,"attr","name",t))||(t+="[]",f=dom.find.child(e,"attr","name",t))?f:(t=t.slice(0,-2),!1);if(a){for(var o=dom.find.parent(a,"class","field"),n=0,i=["disabled","hidden","readonly","required"];n<i.length;n++)s=l=d=void 0,l=i[n],void 0!==(s=r[l])&&(d="toggle"==s?"toggle":!0===s?"add":!1===s?"remove":void 0,edit.attr(a,d,l),edit.class(o,d,l),"hidden"==l&&edit.attr(dom.find.parent(o,"tag","fieldset"),d,l));return!0}throw'formUpdateField: The field "'.concat(t,'" was not found in form "').concat(e.id,'".')}catch(e){return console.error(e),!1}var d,l,s,f}!function(){var e=setInterval(function(){globalFunctionsReady&&(clearInterval(e),function(){var e=document.forms,t=!0,r=!1,a=void 0;try{for(var o,n=e[Symbol.iterator]();!(t=(o=n.next()).done);t=!0){var i=o.value;dom.has(i,"class","no-auto-config")||dom.has(i,"class","configured")||formSetup(i)}}catch(e){r=!0,a=e}finally{try{t||null==n.return||n.return()}finally{if(r)throw a}}}(),window.addEventListener("submit",formHandleSubmit),function(){for(var e=0,t=["mouseover","mouseout","focusin","focusout"];e<t.length;e++){var r=t[e];window.addEventListener(r,formInputMouseEvent)}}(),window.addEventListener("click",function(e){var t=e.target;dom.has(t,"tag","button")&&dom.has(t,"class","form-details-toggle")&&formToggleDetailsEvent(t)}))},250)}(),ShiftCodesTK.forms={getFormData:function(e,t){var c=1<arguments.length&&void 0!==t&&t,r=dom.find.children(e,"group","focusables"),m={},a=!0,o=!1,n=void 0;try{for(var u,i=r[Symbol.iterator]();!(a=(u=i.next()).done);a=!0)(function(){var e,t=u.value,r=(e=dom.get(t,"tag"),-1!=["fieldset","select","a","button"].indexOf(e)?e:dom.get(t,"attr","type")),a=dom.get(t,"attr","name");if("a"==r||t.disabled&&!dom.has(t,"class","disabled-by-script"))return;if(-1!=["radio","checkbox"].indexOf(r))t.checked&&(a.indexOf("[]")==a.length-2?(void 0===m[a]&&(m[a]=[]),m[a].push(t.value)):m[a]=t.value);else if("select"==r){var o=dom.find.children(t,"tag","option"),n=!0,i=!1,d=void 0;try{for(var l,s=o[Symbol.iterator]();!(n=(l=s.next()).done);n=!0){var f=l.value;if(f.selected){m[a]=f.value;break}}}catch(e){i=!0,d=e}finally{try{n||null==s.return||s.return()}finally{if(i)throw d}}}else"textarea"==r?0<t.innerHTML.trim().length&&(m[a]=t.innerHTML):"button"==r&&t!=c||0<t.value.trim().length&&(m[a]=t.value)})()}catch(e){o=!0,n=e}finally{try{a||null==i.return||i.return()}finally{if(o)throw n}}return m}};