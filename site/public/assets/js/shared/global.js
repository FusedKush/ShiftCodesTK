function _createForOfIteratorHelper(e,t){var a;if("undefined"==typeof Symbol||null==e[Symbol.iterator]){if(Array.isArray(e)||(a=_unsupportedIterableToArray(e))||t&&e&&"number"==typeof e.length){a&&(e=a);function o(){}var r=0;return{s:o,n:function(){return r>=e.length?{done:!0}:{done:!1,value:e[r++]}},e:function(e){throw e},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var n,i=!0,s=!1;return{s:function(){a=e[Symbol.iterator]()},n:function(){var e=a.next();return i=e.done,e},e:function(e){s=!0,n=e},f:function(){try{i||null==a.return||a.return()}finally{if(s)throw n}}}}function _unsupportedIterableToArray(e,t){if(e){if("string"==typeof e)return _arrayLikeToArray(e,t);var a=Object.prototype.toString.call(e).slice(8,-1);return"Object"===a&&e.constructor&&(a=e.constructor.name),"Map"===a||"Set"===a?Array.from(e):"Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)?_arrayLikeToArray(e,t):void 0}}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var a=0,o=new Array(t);a<t;a++)o[a]=e[a];return o}var globalScrollTimer,hashTargetTimeout,lastFocus,ShiftCodesTK={},loadEventFired=!1,globalScriptLoaded=!0,globalScrollUpdates=0,focusLock={set:function(e,t){focusLock.active={},focusLock.active.elements=e,focusLock.active.callback=t},clear:function(){focusLock.active=!1},handle:function(e){var t=e.type,r=e.target;if(focusLock.active){var n,i,a,s=focusLock.active.elements,o=function(){var e=[];if(e.push(ShiftCodesTK.toasts.containers.activeToasts),e.push(ShiftCodesTK.layers.layerContainer),Array.isArray(s)){var t,a=_createForOfIteratorHelper(s);try{for(a.s();!(t=a.n()).done;){var o=t.value;e.push(o)}}catch(e){a.e(e)}finally{a.f()}}else e.push(s);return e}();if("mousedown"==t){do{var l,d=_createForOfIteratorHelper(o);try{for(d.s();!(l=d.n()).done;){var c=l.value;if(r==c)return!0}}catch(e){d.e(e)}finally{d.f()}r=r.parentNode}while(r);focusLock.active.callback()}else{"keydown"==t&&(n=function(){e.preventDefault(),focusLock.active.callback()},"Tab"==e.key?(e.preventDefault(),i=function(){var e=[];if(Array.isArray(s)){var t,a=_createForOfIteratorHelper(s);try{for(a.s();!(t=a.n()).done;)var o=t.value,e=e.concat(dom.find.children(o,"group","focusables",!0))}catch(e){a.e(e)}finally{a.f()}}else e=e.concat(dom.find.children(s,"group","focusables",!0));return e}(),a=function(){var e=i.indexOf(r);if(-1!=e){var t={previous:0<e?e-1:i.length-1,pos:e,next:e<i.length-1?e+1:0};for(var a in t){var o=t[a];t[a]=i[o]}return console.info(t),t}n()}(),e.shiftKey?a.previous.focus():a.next.focus()):"Escape"==e.key&&n())}}},active:!1},shiftStats=!1,hashListeners={},requestToken={tagName:"tk-request-token",headerName:"x-request-token",check:function(l){newAjaxRequest({file:"/assets/requests/get/token",callback:function(e){var t=requestToken.get(),a=tryJSONParse(e);if(a&&200==a.statusCode){var o=a.payload.token;if("unchanged"!=o){var r=dom.find.children(document.body,"attr","name","auth_token");edit.attr(getMetaTag(requestToken.tagName),"add","content",o);var n,i=_createForOfIteratorHelper(r);try{for(i.s();!(n=i.n()).done;){var s=n.value;edit.attr(s,"add","value",o)}}catch(e){i.e(e)}finally{i.f()}"function"==typeof l&&l(o,t)}}else ShiftCodesTK.toasts.newToast({settings:{template:"exception"},content:{body:"Your request token could not be updated due to an error. This may affect the site until refreshed."}})}})},get:function(){return getMetaTag(requestToken.tagName).content}},shiftNames={bl1:"Borderlands: GOTY",bl2:"Borderlands 2",bl3:"Borderlands 3",tps:"Borderlands: The Pre-Sequel"};function parseWebpImages(e){var t=document.body.getAttribute("data-webp-support");if(null!==t){var a="true"==t,o=e.getElementsByTagName("*");for(i=0;i<o.length;i++){var r=o[i].getAttribute("data-webp"),n=void 0;null!==r&&((n=JSON.parse(r)).fullPath=n.path,!0==a?n.fullPath+=".webp":!1==a&&(n.fullPath+=n.alt),"bg"==n.type?o[i].style.backgroundImage="url("+n.fullPath+")":"img"==n.type&&(o[i].src=n.fullPath),o[i].removeAttribute("data-webp"))}}else setTimeout(function(){parseWebpImages(e)},250)}function webpSupportUpdate(e){document.body.setAttribute("data-webp-support",e),parseWebpImages(document),document.getElementsByClassName("webp-support")[0].remove()}function addFocusScrollListeners(e){var t=e.getElementsByTagName("*");for(i=0;i<t.length;i++){var a=t[i];"BUTTON"!=a.tagName&&"A"!=a.tagName&&"INPUT"!=a.tagName&&"SELECT"!=a.tagName&&"TEXTAREA"!=a.tagName||!1===a.classList.contains("no-focus-scroll")&&a.addEventListener("focusin",function(e){updateScroll(this)})}}function updateScroll(o){if(!dom.has(o,"class","clipboard-copy")&&!dom.has(o,"class","hidden")){var e=[document.documentElement,document.body],t=null!=(u=o.getAttribute("data-scrollPaddingTop"))?u:0,a=null!=(c=o.getAttribute("data-scrollPaddingBottom"))?c:0,r=64+t,n=e[1].getBoundingClientRect().height-a,i=16,s={};s.base=function(){var e,t=o.tagName.toLowerCase();if("input"!=t&&"select"!=t&&"textarea"!=t)e=o;else for(var a=o;;){if(!0===a.classList.contains("input-container")){e=a;break}a=a.parentNode}return e.getBoundingClientRect()}(),s.top=s.base.top-i,s.bottom=s.base.bottom+i;var l=s.top<r,d=s.bottom>n;if(!0===l)for(x=0;x<e.length;x++)e[x].scrollTop-=r-s.top;else if(!0===d)for(x=0;x<e.length;x++)e[x].scrollTop+=s.bottom-n;!0!==l&&!0!==d||(globalScrollUpdates=0)}var c,u}function toggleBodyScroll(){var e,t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"toggle",a=document.body,o="scroll-disabled",r="data-last-scroll",n=dom.has(a,"class",o);return a.scrollHeight>window.innerHeight&&(t!=!n&&("toggle"==t&&(t=n),t?(edit.class(a,"remove",o),a.style.removeProperty("top"),window.scrollTo(0,tryParseInt(a.getAttribute(r))),a.removeAttribute(r)):(e=window.pageYOffset,a.setAttribute(r,e),setTimeout(function(){a.style.top="-".concat(e,"px"),edit.class(a,"add",o)},50)),!0))}function changeSiteTheme(e){var t=ShiftCodesTK.global.themeColors;delete t.bg;var a={theme:getMetaTag("theme-color"),stored:getMetaTag("tk-theme-color")};return-1==Object.keys(t).indexOf(e)?(console.error('changeSiteTheme Error: "'.concat(e,'" is not a valid theme.')),!1):(edit.attr(document.body,"update","data-theme",e),a.theme.content==a.stored.content&&(a.theme.content=t[e]),a.stored.content=t[e],!0)}function hashUpdate(){var e,t=window.location.hash,a=""!=t;!function(){var e=document.getElementsByTagName("*");for(i=0;i<e.length;i++)null!==e[i].getAttribute("data-hashtarget-highlighted")&&"#"+e[i].id!=t&&(e[i].removeAttribute("data-hashtarget-highlighted"),e[i].removeEventListener("mouseover",globalListenerHashTargetHover),e[i].removeEventListener("mouseout",globalListenerHashTargetAway)),null!==e[i].getAttribute("data-hashtarget")&&"#"+e[i].id!=t&&(e[i].removeAttribute("data-hashtarget"),e[i].removeEventListener("mouseover",globalListenerHashTargetHover),e[i].removeEventListener("mouseout",globalListenerHashTargetAway))}(),history.replaceState?history.replaceState(null,null,t):window.location.hash=t,!0!=a||!0==(null!==(e=document.getElementById(t.replace("#",""))))&&("true"!=e.getAttribute("data-hashtarget")&&(e.setAttribute("data-hashtarget","visible"),e.addEventListener("mouseover",globalListenerHashTargetHover),e.addEventListener("mouseout",globalListenerHashTargetAway),e.addEventListener("focusin",globalListenerHashTargetHover),e.addEventListener("focusout",globalListenerHashTargetAway)),updateScroll(e))}function checkHash(){var e=0<arguments.length&&void 0!==arguments[0]&&arguments[0],t=window.location.hash;function a(e){return 0==t.search(new RegExp("^#".concat(e)))&&(hashListeners[e](t),1)}if(e){if(a(e))return!0}else for(var o=Object.keys(hashListeners),r=0;r<o.length;r++)if(a(o[r]))return!0;return!1}function addHashListener(e,t){return hashListeners[e]=t,checkHash(e)}function old_copyToClipboard(e){var o=e.currentTarget;(function(){for(var e=parseInt(o.getAttribute("data-copy-target")),t=o,a=0;a<e;a++)t=t.parentNode;return dom.find.child(t,"class","clipboard-copy")})().select(),document.execCommand("copy"),o.classList.remove("animated"),setTimeout(function(){o.classList.add("animated"),newToast({settings:{duration:"short",id:"clipboard-copy"},content:{icon:"fas fa-clipboard",title:"Copied to Clipboard",body:"This may not work in all browsers"},close:{use:!1}})},25)}function selectNode(e){try{var t=window.getSelection(),a=new Range;return a.selectNodeContents(e),t.removeAllRanges(),t.addRange(a),t}catch(e){return console.error('selectNode Error: "'.concat(e,'"')),!1}}function copyToClipboard(e){var t,a,o,r,n,i,s,l,d=(t=dom.get(e,"tag"),a=document.createElement("pre"),edit.class(a,"add","copy-to-clipboard-temp-node"),-1!=["input","textarea","select"].indexOf(t)?a.textContent=e.value:a.textContent=e.textContent,a=dom.find.id("data").appendChild(a)),c=selectNode(d),u=document.execCommand("copy");return i=ShiftCodesTK.toasts,o={shared:{settings:{id:"copied_to_clipboard"},content:{icon:"fas fa-clipboard"}},true:{settings:{duration:"short"},content:{title:"Copied to Clipboard!"}},false:{settings:{duration:"infinite",callback:function(e){var t,a=dom.find.parent(e,"class","toast"),o=(t=dom.get(a,"attr","data-range"),dom.find.id(t));if(!dom.has(e,"class","dedicated"))return selectNode(o);0==o.id.indexOf("range_")&&edit.attr(o,"remove","id")}},content:{title:"Could not Copy to Clipboard",body:"This might work in a different browser, but you can just manually select the text instead."},actions:[{content:"Select Text",title:"Selects and highlights the text to be manually copied to the clipboard"}]}},s=mergeObj(o.shared,o[u]),l=i.newToast(s),u||(n=(r=c.getRangeAt(0).commonAncestorContainer).id?r.id:randomID("range_",1e4,999999),r.id=n,edit.attr(l,"add","data-range",n)),c.removeAllRanges(),deleteElement(d),u}function fixClickableContent(e){for(var t=e.childNodes,a=0;a<t.length;a++){if("#text"==t[a].nodeName)return e.innerHTML="<span>".concat(e.innerHTML,"</span>"),!0}}function btnPressToggle(e){e.addEventListener("click",function(e){var t=e.currentTarget,a=dom.has(t,"attr","aria-pressed","true");t.setAttribute("aria-pressed",!a),setTimeout(function(){},500)})}function globalListenerLoadClearScroll(){globalScrollUpdates=0,window.removeEventListener("load",globalListenerLoadClearScroll)}function globalListenerHashTargetHover(e){var t=this;hashTargetTimeout=setTimeout(function(){t.setAttribute("data-hashtarget","seen"),t.removeEventListener("mouseover",globalListenerHashTargetHover),t.removeEventListener("mouseout",globalListenerHashTargetAway),t.removeEventListener("focusin",globalListenerHashTargetHover),t.removeEventListener("focusout",globalListenerHashTargetAway),history.pushState?history.pushState(null,null,window.location.href.split("#")[0]):window.location.hash="##"},750)}function globalListenerHashTargetAway(){clearTimeout(hashTargetTimeout)}function updateClientCursorProperties(e){var t={x:e.clientX,y:e.clientY,target:e.target};return ShiftCodesTK.client.cursor=t}function execGlobalScripts(){function e(){for(var e=0,t=[window.pageYOffset,document.documentElement.scrollTop,document.body.scrollTop];e<t.length;e++){var a=t[e];if(void 0!==a&&null!=a)return void(ShiftCodesTK.client.scroll=a)}}var t;"boolean"==typeof globalFunctionsReady&&"function"==typeof moment?(ShiftCodesTK.local={},ShiftCodesTK.global={themeColors:tryJSONParse(getMetaTag("tk-theme-colors").content)},ShiftCodesTK.shift=function(){var e={platforms:{},games:{}};try{var t=dom.find.id("shift_data");if(!t)throw new Error("No SHiFT Platform & Game Data was found.");var a,o=_createForOfIteratorHelper(["platforms","games"]);try{for(o.s();!(a=o.n()).done;){var r=a.value,n=dom.find.child(t,"class",r);if(!n)throw new Error("SHiFT ".concat(ucWords(r)," was not found."));var i=tryJSONParse(n.innerHTML,"ignore");if(!i)throw new Error("SHiFT ".concat(ucWords(r)," could not be parsed."));e[r]=i}}catch(e){o.e(e)}finally{o.f()}deleteElement(t)}catch(e){console.error("An error occurred while parsing SHiFT Platform & Game Data: ".concat(e))}finally{return e}}(),ShiftCodesTK.relative_dates={class_name:"relative-date-updated",attribute_name:"data-relative-date",interval:{duration:15e4,id:null,start:function(){return null!==this.id?(console.error("The Relative Date Refresh Interval has already been started."),!1):(this.id=setInterval(ShiftCodesTK.relative_dates.refresh_all_elements,this.duration),!0)},stop:function(){return null===this.id?(console.error("The Relative Date Refresh Interval has not been started."),!1):(clearInterval(this.id),!(this.id=null))}},refresh_element:function(e){var t=dom.get(e,"attr",this.attribute_name);if(t){var a=moment(t);if(a)return e.innerHTML="".concat(ucWords(a.fromNow(!0))," ago"),!0;console.error('"'.concat(t,'" is not a valid Date Timestamp for the target element.'))}else console.error("A Date Timestamp was not provided for the target element.");return!1},refresh_all_elements:function(){var o=ShiftCodesTK.relative_dates,e=dom.find.children(document.body,"attr",o.attribute_name),r=[];if(e){var t,a=_createForOfIteratorHelper(e);try{for(a.s();!(t=a.n()).done;){var n=t.value;dom.get(n,"attr",o.attribute_name)&&(o.refresh_element(n),edit.class(n,"add",o.class_name),r.push(n))}}catch(e){a.e(e)}finally{a.f()}setTimeout(function(){var e,t=_createForOfIteratorHelper(r);try{for(t.s();!(e=t.n()).done;){var a=e.value;edit.class(a,"remove",o.class_name)}}catch(e){t.e(e)}finally{t.f()}},1e3)}return r}},(t=document.createElement("img")).classList.add("webp-support"),t.onload=function(){webpSupportUpdate(!0)},t.onerror=function(){webpSupportUpdate(!1)},t.src="/assets/img/webp_support.webp",document.body.appendChild(t),hashUpdate(),function(){for(var e=dom.find.children(document,"group","clickables"),t=0;t<e.length;t++)fixClickableContent(e[t])}(),window.addEventListener("click",function(e){var t;(button=dom.has(e.target,"class","o-pressed",null,!0))&&(t=dom.has(button,"attr","aria-pressed","true"),console.info(button,dom.get(button,"attr","aria-pressed"),dom.has(button,"attr","aria-pressed","true")),edit.attr(button,"update","aria-pressed",!t),setTimeout(function(){},500))}),function(){var e,t,a={};a.main=dom.find.id("containers"),a.stylesheets=(e=dom.find.children(document.head,"attr","rel","stylesheet"))[e.length-1],a.scripts=(t=dom.find.children(document.body,"tag","script"))[t.length-1],a.templates=dom.find.id("templates");var o={stylesheets:dom.find.children(document.body,"attr","rel","stylesheet"),scripts:dom.find.children(document.body,"tag","script"),templates:dom.find.children(document.body,"tag","template")};for(var r in o)for(var n=a[r],i=o[r],s=i.length-1;0<=s;s--){var l=i[s];l==n||n.contains(l)||(-1!=["stylesheets","scripts"].indexOf(r)?n.insertAdjacentElement("afterend",l):n.appendChild(l))}}(),window.addEventListener("hashchange",function(e){event.preventDefault(),checkHash(),hashUpdate()}),window.addEventListener("load",globalListenerLoadClearScroll),function(){var e=document.getElementsByTagName("a");for(i=0;i<e.length;i++)""!=e[i].hash&&e[i].addEventListener("click",hashUpdate)}(),window.addEventListener("mousedown",focusLock.handle),window.addEventListener("keydown",focusLock.handle),ShiftCodesTK.client={cursor:{x:0,y:0,target:0},scroll:0},e(),window.addEventListener("mousemove",updateClientCursorProperties),document.body.addEventListener("mouseleave",updateClientCursorProperties),window.addEventListener("scroll",e),window.addEventListener("click",function(e){var n=!!(n=dom.has(e.target,"class","copy-to-clipboard",null,!0))&&n;if(n)try{var t=function(){var e=dom.get(n,"attr","data-copy");if(e){var t=tryParseInt(e,"ignore");if(!1===t){var a=dom.find.id(e);if(a)return a;throw'Provided element "'.concat(e,'" does not exist.')}for(var o=n,r=0;r<t;r++){if(!o.parentNode)throw'Parent number "'.concat(t,'" does not exist.');o=o.parentNode}return dom.find.child(o,"class","copy-content")}return!!n.parentNode&&dom.find.child(n.parentNode,"class","copy-content")}();t&&(copyToClipboard(t)||(isDisabled(n,!0),ShiftCodesTK.layers.updateTooltip(n,"Could not be copied to the Clipboard.",{delay:"none"})))}catch(e){return console.error("copyToClipboard Error: ".concat(e,'"')),!1}}),ShiftCodesTK.relative_dates.refresh_all_elements(),ShiftCodesTK.relative_dates.interval.start(),window.addEventListener("click",function(e){var t=dom.has(e.target,"attr","data-alias",null,!0);if(t){var a=dom.get(t,"attr","data-alias"),o=dom.find.id(a);return o?void 0!==o.click?(o.click(),!0):void 0!==o.focus?(o.focus(),!0):(console.warn('Button Alias "'.concat(a,'" could not be aliased.')),!1):(console.warn('Button Alias "'.concat(a,'" was not found.')),!1)}}),ShiftCodesTK.requests.savedRequests.saveRequest("profile_card_modal",{parameters:{user_id:""},request:{path:"/assets/requests/get/account/profile-card",callback:function(e){var t,a,o,r;e&&void 0!==e.payload&&e.payload[0]&&(t=dom.find.id("profile_card_modal"),r=dom.find.child(t,"class","body"),a=dom.find.child(r,"class","content-container"),o=createElementFromHTML(e.payload[0]),a.innerHTML=o.outerHTML,multiView_setup(a.childNodes[0]),ShiftCodesTK.modals.toggleModal(t,!0))}}})):setTimeout(execGlobalScripts,250)}execGlobalScripts(),window.addEventListener("load",function(){loadEventFired=!0});