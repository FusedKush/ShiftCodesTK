function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function _toConsumableArray(e){return _arrayWithoutHoles(e)||_iterableToArray(e)||_unsupportedIterableToArray(e)||_nonIterableSpread()}function _nonIterableSpread(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function _unsupportedIterableToArray(e,t){if(e){if("string"==typeof e)return _arrayLikeToArray(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?_arrayLikeToArray(e,t):void 0}}function _iterableToArray(e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e))return Array.from(e)}function _arrayWithoutHoles(e){if(Array.isArray(e))return _arrayLikeToArray(e)}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}var globalFunctionsReady=!0,pbIntervals={};function thrownTryError(e,t){if("silent"==t)return console.error(e),!1;if("throw"==t)throw e;if("ignore"==t)return!1;throw e.message="".concat(e.message,"\n\r\n\rAdditionally, the behavior parameter is invalid.\n\rBehavior: ").concat(t),e}function tryParseInt(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:null,t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"silent",r=new Error;r.name="parseInt Error",r.message="Not a valid number.\n\rInt: ".concat(e);var n=parseInt(e);return isNaN(n)?thrownTryError(r,t):n}function tryJSONParse(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:null,t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"silent",r=new Error;r.name="JSONParse Error",r.message="Not a valid JSON string.\n\rString: ".concat(e);try{return JSON.parse(e)}catch(e){return thrownTryError(r,t)}}function tryToRun(r){var n=1<arguments.length&&void 0!==arguments[1]?arguments[1]:1,a=function(){if(n){return mergeObj({function:function(){return!0},attempts:!1,delay:250,behavior:"silent",logCatch:!1,customError:!1},r)}return r}();function t(){if(n<=a.attempts||!a.attempts)setTimeout(function(){tryToRun(r,n+1)},a.delay);else{var t=new Error;if(!(t.name="tryToRun Error")!==a.customError?t.message=a.customError:t.message="Max Tries Exceeded.\r\n\r\nSettings: ".concat(JSON.stringify(a)),a.logCatch&&(t.message+="\r\n\r\nCaught Error: ".concat(e)),"throw"==a.behavior)throw t;if("silent"==a.behavior)return void console.error(t);if("ignore"==a.behavior)return}}try{a.function()||t()}catch(e){t()}}function setElementState(e,t,r,n){function a(e){var t=new Error;throw t.name="setElementStateError",t.message=e,t}if(-1!=["disabled","hidden"].indexOf(e)){if(t){var o=t[e];return"toggle"==r&&(r=o?!t[e]:null!==t.getAttribute(e)),o?t[e]=r:(r?t.setAttribute(e,""):t.removeAttribute(e),t.setAttribute("aria-".concat(e),r)),n&&(t.tabIndex={true:-1,false:0}[r]),!0}a("Provided element is ".concat(t,"."))}else a("".concat(e," is not a valid state."))}function isDisabled(e){return setElementState("disabled",e,1<arguments.length&&void 0!==arguments[1]?arguments[1]:"toggle",2<arguments.length&&void 0!==arguments[2]&&arguments[2])}function isHidden(e){return setElementState("hidden",e,1<arguments.length&&void 0!==arguments[1]?arguments[1]:"toggle",2<arguments.length&&void 0!==arguments[2]&&arguments[2])}function disenable(e,t,r){return isDisabled(e,t,r)}function vishidden(e,t,r){return isHidden(e,t,r)}function updateLabel(e,t){e.title=t,e.setAttribute("aria-label",t)}function newAjaxRequest(e){var t,r,n=window.XMLHttpRequest?new XMLHttpRequest:window.ActiveXObject?new ActiveXObject("Microsoft.XMLHttp"):void 0,a=mergeObj({type:"GET",file:null,callback:function(e){return e},params:"none",requestHeader:"default",catchErrors:!0},e);function o(e){var t=new Error;throw t.name="newAjaxRequest Error",t.message='An error occurred with Ajax Request "'.concat(a.type,": ").concat(a.file,'".\n\rError: ').concat(e),t}null!==a.file?(r=new RegExp("\\s+","g"),t=a.file.replace(r,""),n.onreadystatechange=function(){function e(){if(n.readyState===XMLHttpRequest.DONE)if(200===n.status)a.callback(n.responseText);else if(a.catchErrors)throw"Status Code ".concat(n.status," returned.")}if(a.catchErrors)try{e()}catch(e){o(e)}else e()},n.open(a.type,t,!0),"form"==a.requestHeader&&n.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),"none"==a.params?n.send():n.send(a.params)):o("File path was not specified.\n\rProperties: ".concat(JSON.stringify(a)))}function datetime(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"y-m-d",t=1<arguments.length&&void 0!==arguments[1]&&arguments[1],r=2<arguments.length&&void 0!==arguments[2]?arguments[2]:"check",n=function(){if(t){var e=new Date(t);return"Invalid Date"!=e&&e}return new Date}();if(!1===n)return!1;function a(e){return"0".concat(e).slice(-2)}var o,i=("check"==r&&(r=!(!t||-1!=t.search("(\\d{2}\\:)")&&-1==t.search("00:00:00"))),r?{year:n.getUTCFullYear(),month:n.getUTCMonth(),date:n.getUTCDate(),day:n.getUTCDay(),hour:n.getUTCHours(),minute:n.getUTCMinutes(),seconds:n.getUTCSeconds()}:{year:n.getFullYear(),month:n.getMonth(),date:n.getDate(),day:n.getDay(),hour:n.getHours(),minute:n.getMinutes(),seconds:n.getSeconds()}),s={"tmp-full":"monthN date, year hour12:minute ampm","tmp-date":"month/date/year","tmp-time12":"hour12:minute ampm","tmp-time24":"hour24:minute"},l={days:{0:"Sunday",1:"Monday",2:"Tuesday",3:"Wednesday",4:"Thursday",5:"Friday",6:"Saturday"},months:{0:"January",1:"Feburary",2:"March",3:"April",4:"May",5:"June",6:"July",7:"August",8:"September",9:"October",10:"November",11:"December"}},u={year:i.year,month:a(i.month+1),monthN:l.months[i.month].slice(0,3),monthL:l.months[i.month],date:a(i.date),day:i.day,dayN:l.days[i.day].slice(0,3),dayL:l.days[i.day],hour12:1<(o=i.hour)&&o<=12?o:12<o?o-12:12,hour24:i.hour,minute:a(i.minute),second:a(i.second),ampm:i.hour<=12?"AM":"PM",js:n,iso:n.toISOString()},c=new RegExp("(\\w+)","g");return s[e]?datetime(s[e],t):"js"==e?u.js:e.replace(c,function(e){var t=u[e];return t||e})}function dateDif(e){for(var t={},r={date:e,start:1<arguments.length&&void 0!==arguments[1]&&arguments[1]},n=Object.keys(r),a=0;a<n.length;a++){var o=n[a],i=datetime("js",r[o]);t[o]=Date.UTC(i.getFullYear(),i.getMonth(),i.getDate())}var s=Math.abs(t.start-t.date);return Math.ceil(s/864e5)}function dateRel(e){var t=1<arguments.length&&void 0!==arguments[1]&&arguments[1],r={date:datetime("tmp-date",e),start:datetime("tmp-date",t)},n=dateDif(r.date,r.start);return 0==n?"Today":1==n?r.date<r.start?"Yesterday":"Tomorrow":n<7&&-7<n&&datetime("dayL",e)}function randomNum(e,t){return Math.round(Math.random()*(t-e)+e)}function modifyClass(e,t,r){if(null!=e){if("contains"==r)return e.classList[r](t);var n,a=(o=t,i=new RegExp(" ","g"),o=o.replace(i,'", "'),tryJSONParse(o='["'.concat(o,'"]')));return(n=e.classList)[r].apply(n,_toConsumableArray(a)),!0}var o,i,s=new Error;throw s.name="".concat(r,"Class Error"),s.message="Passed element is undefined.",s}function hasClass(e,t){return modifyClass(e,t,"contains")}function addClass(e,t){return modifyClass(e,t,"add")}function delClass(e,t){return modifyClass(e,t,"remove")}function toggleClass(e,t){(hasClass(e,t)?delClass:addClass)(e,t)}function getClass(e,t){return e.getElementsByClassName(t)[0]}function getClasses(e,t){return e.getElementsByClassName(t)}function getTags(e,t){return e.getElementsByTagName(t)}function getTag(e,t){return getTags(e,t)[0]}function hasAttr(e,t){return null!==e.getAttribute(t)}function getElements(e,o){for(var i=getTags(e,"*"),s=[],t=0;t<i.length;t++)!function(e){var t=i[e],r=t.tagName.toLowerCase();function n(){s.push(t)}if("string"==typeof o)"focusables"==o?"a"!=r&&"button"!=r&&"input"!=r&&"select"!=r&&"textarea"!=r||n():"clickables"==o?"a"!=r&&"button"!=r||n():"inputs"==o&&("input"!=r&&"select"!=r&&"textarea"!=r||n());else{if(!("object"==_typeof(o)&&0<o.length))throw new TypeError('Function "getElements" was called with an invalid element list.');for(var a=0;a<o.length;a++){if(r==o[a]){n();break}}}}(t);return s}function getElement(e){return"string"==typeof e?document.getElementById(e):e}function copyElm(e){var t=!(1<arguments.length&&void 0!==arguments[1])||arguments[1];return e.cloneNode(t)}function getTemplate(e){var t=!(1<arguments.length&&void 0!==arguments[1])||arguments[1],r=getElement(e);if(null!=r)return"TEMPLATE"==r.tagName?copyElm(r.content.children[0].cloneNode(t)):r.children[0].cloneNode(t);throw"getTemplate called on an undefined element: "+templateID}function deleteElm(e){var t=getElement(e);return t.parentNode.removeChild(t)}function findAttr(e,o,i,s,l){var u;return function e(t){if("BODY"!=t.tagName){var r=t.getAttribute(s);if(null!=r&&""!=r&&("exist"==i||"match"==i&&r==l||"not-match"==i&&r!=l))return u=t,1;if("up"==o)e(t.parentNode);else for(var n=t.children,a=0;a<n.length;a++)e(n[a])}}(e.parentNode),u}function findClass(e,a,o){var i;return function e(t){if("BODY"!=t.tagName)if(!0!==hasClass(t,o))if("up"==a)e(t.parentNode);else for(var r=t.children,n=0;n<r.length;n++)e(r[n]);else i=t;else i=!1}(e.parentNode),i}function reachElement(e,a,o){var i,s=3<arguments.length&&void 0!==arguments[3]?arguments[3]:"class";return function e(t){if("class"==s&&!0===hasClass(t,o)||"tag"==s&&t.tagName==o.toUpperCase()||"attr"==s&&void 0!==t[o]&&""!=t[o])return i=t,1;if("up"==a)e(t.parentNode);else for(var r=t.children,n=0;n<r.length;n++)e(r[n])}(e),i}function mergeObj(e){var t=arguments.length,r={};for(var n=0;n<t;n++)for(var a=arguments[n],o=Object.keys(a),i=0;i<o.length;i++){var s=o[i];!function e(t,r,n){if(n&&"Object"==n.constructor.name)for(var a=Object.keys(n),o=0;o<a.length;o++){var i=a[o],s=n[i];t[r]||(t[r]={}),e(t[r],i,s)}else t[r]=n}(r,s,a[s])}return r}function regexMatchAll(e,t){for(var r=[],n=!0===e.global?e:new RegExp(e,"g");null!==(matches=n.exec(t));)r.push(matches);return r}function getCookie(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"all";function a(e){for(var t={},r=["string","name","value"],n=0;n<r.length;n++)t[r[n]]=e[n];return t}function t(e){var t=regexMatchAll("("+e+")=([^;]+)(?:;|$)",document.cookie);if(1==t.length)return a(t[0]);if(1<t.length){for(var r=[],n=0;n<t.length;n++)r.push(a(t[n]));return r}return!1}return t("all"==e?"[^\\s=]+":e)}function setCookie(e){var a=mergeObj([{name:"",value:"",path:"/",domain:!1,"max-age":789e4,expires:!1,secure:!1,samesite:"lax"},e]),o=Object.keys(a),i="";return function(){if(""==a.name)throw new Error('Could not update cookie: Property "name" is required but was not specified.\r\n\r\n'+JSON.stringify(e));i+=a.name+"=",i+=val=encodeURIComponent(a.value)}(),function(){for(var e=2;e<o.length;e++){var t=o[e],r=a[t],n="";!1!==r&&("boolean"!=typeof r&&(n="="+r),i+="; "+t+n)}}(),document.cookie=i}function deleteCookie(t){function e(e){setCookie({name:t,"max-age":e,expires:e})}e("immediately"==(1<arguments.length&&void 0!==arguments[1]?arguments[1]:"immediately")&&0)}function updateProgressBar(){var n=0<arguments.length&&void 0!==arguments[0]?arguments[0]:null,r=1<arguments.length&&void 0!==arguments[1]?arguments[1]:100,e=2<arguments.length&&void 0!==arguments[2]?arguments[2]:{};if(null===n||"progressbar"!=n.getAttribute("role")){var t=new Error;throw t.name="updateProgressBar Error",t.message="A valid progress bar was not passed.\n\rProgress Bar: ".concat(n),t}var a,o,i=getClass(n,"progress"),s=mergeObj({interval:!1,intervalDelay:1e3,intervalIncrement:5,start:null,resetOnZero:!1,useWidth:!1},e),l=n.id;!s.resetOnZero||0<r?(a=function(e){var t=0<arguments.length&&void 0!==e?e:r;n.setAttribute("data-progress",t),n.setAttribute("aria-valuenow",t),s.useWidth?i.style.width="".concat(t,"%"):i.style.transform="translateX(".concat(t,"%)")},pbIntervals[l]&&clearInterval(pbIntervals[l].interval),!1===s.interval?a():(o=tryParseInt(n.getAttribute("data-progress"),"ignore"),null!==s.start&&o<s.start?a(s.start):a(o+s.intervalIncrement),""==n.id&&(l="progressbar_".concat(randomNum(100,1e3)),n.id=l),pbIntervals[l]={},pbIntervals[l].end=r,pbIntervals[l].increment=s.intervalIncrement,pbIntervals[l].interval=setInterval(function(){var e=n.id,t=tryParseInt(n.getAttribute("data-progress"),"throw")+pbIntervals[e].increment,r=pbIntervals[e].end;t<=r?a(t):(a(r),clearInterval(pbIntervals[e].interval),delete pbIntervals[e])},s.intervalDelay))):(n.setAttribute("data-progress",0),i.style.removeProperty("transform"))}function checkPlural(e){return 1!=e?"s":""}