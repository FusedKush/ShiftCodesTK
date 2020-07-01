function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}var globalFunctionsReady=!0,pbIntervals={};function tryError(e,t){if("silent"==t)return console.error(e),!1;if("throw"==t)throw e;if("ignore"==t)return!1;throw e.message="".concat(e.message,"\n\r\n\rAdditionally, the behavior parameter is invalid.\n\rBehavior: ").concat(t),e}function tryToRun(r){var n=1<arguments.length&&void 0!==arguments[1]?arguments[1]:1,a=function(){if(n){return mergeObj({function:function(){return!0},attempts:!1,delay:250,behavior:"silent",logCatch:!1,customError:!1},r)}return r}();function t(){if(n<=a.attempts||!a.attempts)setTimeout(function(){tryToRun(r,n+1)},a.delay);else{var t=new Error;if(!(t.name="tryToRun Error")!==a.customError?t.message=a.customError:t.message="Max Tries Exceeded.\r\n\r\nSettings: ".concat(JSON.stringify(a)),a.logCatch&&(t.message+="\r\n\r\nCaught Error: ".concat(e)),"throw"==a.behavior)throw t;if("silent"==a.behavior)return void console.error(t);if("ignore"==a.behavior)return}}try{var o=a.function();if(!1!==o)return o;t()}catch(e){t()}}function tryParseInt(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:null,t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"silent",r=new Error;r.name="parseInt Error",r.message="Not a valid number.\n\rInt: ".concat(e);var n=parseInt(e);return isNaN(n)?tryError(r,t):n}function tryJSONParse(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:null,t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"silent",r=new Error;r.name="JSONParse Error",r.message="Not a valid JSON string.\n\rString: ".concat(e);try{return JSON.parse(e)}catch(e){return tryError(r,t)}}function mergeObj(e){var t=arguments.length,r={};for(var n=0;n<t;n++)for(var a=arguments[n],o=Object.keys(a),i=0;i<o.length;i++){var l=o[i];!function e(t,r,n){if(n&&"Object"==n.constructor.name){var a=Object.keys(n);t[r]||(t[r]={});for(var o=0;o<a.length;o++){var i=a[o],l=n[i];e(t[r],i,l)}}else t[r]=n}(r,l,a[l])}return r}function regexMatchAll(e,t){for(var r=[],n=!0===e.global?e:new RegExp(e,"g");null!==(matches=n.exec(t));)r.push(matches);return r}function newAjaxRequest(e){var t,r,a=window.XMLHttpRequest?new XMLHttpRequest:window.ActiveXObject?new ActiveXObject("Microsoft.XMLHttp"):void 0,c=((t=mergeObj({file:null,type:"GET",params:{},callback:!1,headers:{},_tokenRefreshed:!1},e)).type=t.type.toUpperCase(),"POST"!=t.type||t.headers["Content-Type"]||(t.headers["Content-Type"]="application/x-www-form-urlencoded"),t.params._token||t.params._auth_token||(t.params._token=requestToken.get()),t);function n(e){var t=0<arguments.length&&void 0!==e?e:null;for(var r in a.open(c.type,c.file,!0),c.headers){var n=c.headers[r];try{a.setRequestHeader(r,n)}catch(e){console.warn("newAjaxRequest: ".concat(r," is not a valid header or ").concat(n," is not a valid header value."));continue}}a.send(t)}return null===c.file?(console.error("newAjaxRequest Error: A file path was not specified.\r\n".concat(JSON.stringify(e))),!1):"GET"!=c.type&&"POST"!=c.type?(console.error('newAjaxRequest Error: "'.concat(c.type,'" is not a valid Request Type.\r\n').concat(JSON.stringify(e))),!1):(a.onreadystatechange=function(){if(a.readyState===XMLHttpRequest.DONE){if(401==a.status){var e=tryJSONParse(a.responseText);if(e&&"Missing or Invalid Request Token"==e.statusMessage&&!c._tokenRefreshed)return c._tokenRefreshed=!0,void requestToken.check(function(){newAjaxRequest(c)})}c.callback&&c.callback(a.responseText)}},r=function(){var e="";for(var t in c.params){var r=c.params[t];if("Array"==r.constructor.name){var n=!0,a=!1,o=void 0;try{for(var i,l=r[Symbol.iterator]();!(n=(i=l.next()).done);n=!0){var s=i.value;-1==t.indexOf("[]")&&(t+="[]"),e+="".concat(t,"=").concat(s,"&")}}catch(e){a=!0,o=e}finally{try{n||null==l.return||l.return()}finally{if(a)throw o}}}else e+="".concat(t,"=").concat(r,"&")}return e.slice(0,-1)}(),void("GET"==c.type?(c._tokenRefreshed||(-1==c.file.indexOf("?")?c.file+="?":c.file+="&",c.file+=r),n()):"POST"==c.type&&n(r)))}function datetime(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"year-month-date",t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"now",r=2<arguments.length&&void 0!==arguments[2]&&arguments[2],n=function(){if("now"==t)return new Date;if("Object"==typeof t&&"Date"==t.constructor.name)return t;var e=new Date(t);return!(!t||"Invalid Date"==e)&&e}();if(!1===n)return!1;function a(e){return"0".concat(e).slice(-2)}var o,i,l=("check"==r&&(i=n.toUTCString(),r=!(!t||-1!=i.search("(\\d{2}\\:)")&&-1==i.search("00:00:00"))),r?{year:n.getUTCFullYear(),month:n.getUTCMonth(),date:n.getUTCDate(),day:n.getUTCDay(),hour:n.getUTCHours(),minute:n.getUTCMinutes(),second:n.getUTCSeconds()}:{year:n.getFullYear(),month:n.getMonth(),date:n.getDate(),day:n.getDay(),hour:n.getHours(),minute:n.getMinutes(),second:n.getSeconds()}),s={"tmp-full":"monthN date, year hour12:minute ampm","tmp-date":"month/date/year","tmp-time12":"hour12:minute ampm","tmp-time24":"hour24:minute"},c={days:{0:"Sunday",1:"Monday",2:"Tuesday",3:"Wednesday",4:"Thursday",5:"Friday",6:"Saturday"},months:{0:"January",1:"Feburary",2:"March",3:"April",4:"May",5:"June",6:"July",7:"August",8:"September",9:"October",10:"November",11:"December"}},d={year:l.year,month:a(l.month+1),monthN:c.months[l.month].slice(0,3),monthL:c.months[l.month],date:a(l.date),day:l.day,dayN:c.days[l.day].slice(0,3),dayL:c.days[l.day],hour12:1<(o=l.hour)&&o<=12?o:12<o?o-12:12,hour24:a(l.hour),minute:a(l.minute),second:a(l.second),ampm:l.hour<=12?"AM":"PM",js:n,iso:n.toISOString()},u=new RegExp("(\\w+)","g");return s[e]?datetime(s[e],t):"js"==e?d.js:e.replace(u,function(e){var t=d[e];return!1!==t?t:e})}function dateDif(e){for(var t={},r={date:e,start:1<arguments.length&&void 0!==arguments[1]?arguments[1]:"now"},n=Object.keys(r),a=0;a<n.length;a++){var o=n[a],i=datetime("js",r[o]);t[o]=Date.UTC(i.getFullYear(),i.getMonth(),i.getDate())}var l=Math.abs(t.start-t.date);return Math.ceil(l/864e5)}function dateRel(e){var t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"now",r={date:datetime("tmp-date",e),start:datetime("tmp-date",t)},n=dateDif(r.date,r.start);return 0==n?"Today":1==n?r.date<r.start?"Yesterday":"Tomorrow":n<7&&-7<n&&(r.date<r.start?"Last":"Next")+" "+datetime("dayL",e)}var dom={try:function(e,t,r,n,a,o){var i=5<arguments.length&&void 0!==o?o:["class","tag","attr"],l=[void 0!==t&&t&&void 0!==t.getElementsByTagName,-1!=i.indexOf(r),"string"==typeof n||"number"==typeof n];if(-1==l.indexOf(!1))return a();try{var s=new Error;throw s.name="".concat(e," Error"),s.message="is not a valid",l[0]?l[1]?l[2]||(s.message="Argument 3 ".concat(s.message," class, tag, or attribute: ").concat(attr)):s.message="Argument 2 ".concat(s.message," type: ").concat(r):s.message="Argument 1 ".concat(s.message," element: ").concat(t),s}catch(s){return console.error(s),!1}},get:function(e,t,r){var n=2<arguments.length&&void 0!==r?r:"",a={class:!!e.classList&&e.classList,tag:!!e.tagName&&e.tagName.toLowerCase(),attr:!(!e.getAttribute||!e.getAttribute(n))&&e.getAttribute(n)};return dom.try("domGet",e,t,n,function(){return a[t]})},has:function(t,e,r,n){var a=3<arguments.length&&void 0!==n?n:null,o={class:function(){var e=dom.get(t,"class");return!!e&&e.contains(r)},tag:function(){return dom.get(t,"tag")==r},attr:function(){return null===a?!1!==dom.get(t,"attr",r):dom.get(t,"attr",r)==a}};return dom.try("domHas",t,e,r,function(){return o[e]()})},find:{parents:function(n,a,o,e){var i=3<arguments.length&&void 0!==e?e:null;return dom.try("domFindParents",n,a,o,function(){var e=n.parentNode,t=[],r={inputs:["input","select","textarea"],clickables:["a","button"]};for(r.focusables=r.inputs.concat(r.clickables);e&&void 0!==e.getElementsByTagName;){if("group"!=a&dom.has(e,a,o,i)||"group"==a&&-1!=r[o].indexOf(dom.get(e,"tag")))t.push(e);else if(dom.has(e,"tag","body")||dom.has(e,"tag","html"))break;e=e.parentNode}return t},["class","tag","attr","group"])},parent:function(t,r,n,a){return dom.try("domFindParent",t,r,n,function(){var e=dom.find.parents(t,r,n,a);return 0<e.length&&e[0]},["class","tag","attr","group"])},children:function(d,e,u,t){var s=3<arguments.length&&void 0!==t?t:null,r={class:function(){return d.getElementsByClassName(u)},tag:function(){return d.getElementsByTagName(u)},attr:function(){var e=d.getElementsByTagName("*"),t=[],r=!0,n=!1,a=void 0;try{for(var o,i=e[Symbol.iterator]();!(r=(o=i.next()).done);r=!0){var l=o.value;dom.has(l,"attr",u,s)&&t.push(l)}}catch(e){n=!0,a=e}finally{try{r||null==i.return||i.return()}finally{if(n)throw a}}return t},group:function(){var e={inputs:["input","select","textarea"],clickables:["a","button"]};e.focusables=e.inputs.concat(e.clickables);var t=d.getElementsByTagName("*"),r=[],n=e[u];if(n){var a=!0,o=!1,i=void 0;try{for(var l,s=t[Symbol.iterator]();!(a=(l=s.next()).done);a=!0){var c=l.value;-1!=n.indexOf(dom.get(c,"tag"))&&r.push(c)}}catch(e){o=!0,i=e}finally{try{a||null==s.return||s.return()}finally{if(o)throw i}}return r}return console.error("domFindChildren Error: Argument 4 is not a valid name: ".concat(u)),!1}};return dom.try("domFindChildren",d,e,u,function(){return r[e]()},["class","tag","attr","group"])},child:function(t,r,n,e){var a=3<arguments.length&&void 0!==e?e:null;return dom.try("domFindChild",t,r,n,function(){var e=dom.find.children(t,r,n,a);return 0<e.length&&e[0]},["class","tag","attr","group"])},id:function(e){var t=document.getElementById(e);return null!==t&&t}}},edit={try:function(e){var t=mergeObj({func:"",callback:function(){console.warning("No callback was passed to editTry.")},elm:!1,type:"",name:"",val:"",validTypes:["add","remove"]},e),r=[void 0!==t.elm&&t.elm,-1!=t.validTypes.indexOf(t.type),"string"==typeof t.name||"number"==typeof t.name,"string"==typeof t.val||"number"==typeof t.val||"boolean"==typeof t.val];try{if(-1==r.indexOf(!1))return t.callback();if(!r[0])throw"Invalid element: ".concat(t.elm);if(!r[1])throw"Invalid type: ".concat(t.type);if(!r[2])throw"Invalid class or attribute: ".concat(_typeof(t.name));if(!r[3])throw"Invalid class name or attribute value: ".concat(_typeof(t.val))}catch(e){return console.error("".concat(t.func," Error: ").concat(e)),!1}},class:function(s,c,d){return edit.try({func:"editClass",elm:s,type:c,name:d,validTypes:["add","remove","toggle"],callback:function(){var e=d.split(" "),t=!0,r=!1,n=void 0;try{for(var a,o=e[Symbol.iterator]();!(t=(a=o.next()).done);t=!0){var i=a.value,l=c;"toggle"==l&&(l=dom.has(s,"class",i)?"remove":"add"),dom.get(s,"class")[l](i)}}catch(e){r=!0,n=e}finally{try{t||null==o.return||o.return()}finally{if(r)throw n}}return!0}})},attr:function(e,t,r,n){var a=3<arguments.length&&void 0!==n?n:"";return edit.try({func:"editAttr",elm:e,type:t,name:r,val:a,validTypes:["add","update","remove","toggle"],callback:function(){return"toggle"==t&&(t=dom.has(e,"attr",r)?"remove":"add"),"add"==t|"update"==t?e.setAttribute(r,a):e.removeAttribute(r),!0}})},copy:function(t,e){var r=!(1<arguments.length&&void 0!==e)||e;return edit.try({func:"editCopy",elm:t,type:"add",callback:function(){var e="template"==dom.get(t,"tag")?t.content.children[0]:t;return e.cloneNode(r)}})}};function getMetaTag(e){var t=dom.find.child(document.head,"attr","name",e);return t||!1}function setElementState(e,t,r,n){function a(e){var t=new Error;throw t.name="setElementStateError",t.message=e,t}if(-1!=["disabled","hidden"].indexOf(e)){if(t){var o=t[e];return"toggle"==r&&(r=o?!t[e]:!dom.has(t,"attr",e)),r?edit.attr(t,"add",e,""):edit.attr(t,"remove",e),edit.attr(t,"add","aria-".concat(e),r),n&&(t.tabIndex={true:-1,false:0}[r]),!0}a("Provided element is ".concat(t,"."))}else a("".concat(e," is not a valid state."))}function isDisabled(e){return setElementState("disabled",e,1<arguments.length&&void 0!==arguments[1]?arguments[1]:"toggle",2<arguments.length&&void 0!==arguments[2]&&arguments[2])}function isHidden(e){return setElementState("hidden",e,1<arguments.length&&void 0!==arguments[1]?arguments[1]:"toggle",2<arguments.length&&void 0!==arguments[2]&&arguments[2])}function updateLabel(s,n){var e=2<arguments.length&&void 0!==arguments[2]?arguments[2]:["title","aria"],t={title:function(){s.title=n},aria:function(){edit.attr(s,"update","aria-label","label")},tooltip:function(){var e,t,r=function(){var e="data-layer-target",t=dom.get(s,"attr",e);if(t){var r=dom.find.id(t);if(r)return r}if(s.id){var n=dom.find.child(document.body,"attr",e,s.id);if(n)return n}for(var a=0,o=[s.nextElementSibling,s.previousElementSibling];a<o.length;a++){var i=o[a];if(i&&dom.has(i,"class","tooltip"))return i}var l=!!s.parentNode&&dom.find.child(s.parentNode,"class","tooltip");return l||!1}();"".concat(r.id,"_content");return r||(e=document.createElement("div"),edit.class(e,"add","layer tooltip"),edit.attr(e,"add","data-layer-delay","medium"),t=e,r=s.insertAdjacentElement("afterend",t),edit.class(s,"add","layer-target"),ShiftCodesTK.layers.setupLayer(r)),dom.find.child(r,"class","content-container").innerHTML=n,!0}};for(component in t)-1!=e.indexOf(component)&&t[component]()}function updateProgressBar(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:null,o=1<arguments.length&&void 0!==arguments[1]?arguments[1]:100,e=2<arguments.length&&void 0!==arguments[2]?arguments[2]:{};if(null===a||"progressbar"!=dom.get(a,"attr","role")){var t=new Error;throw t.name="updateProgressBar Error",t.message="A valid progress bar was not passed.\n\rProgress Bar: ".concat(a),t}var n,r,i=dom.find.child(a,"class","progress"),l=dom.find.child(a,"class","progress-count"),s=mergeObj({interval:!1,intervalDelay:1e3,intervalIncrement:5,start:null,resetOnZero:!1,useWidth:!1},e),c=a.id;!s.resetOnZero||0<o?(n=function(e){var t,r,n=0<arguments.length&&void 0!==e?e:o;a.setAttribute("data-progress",n),a.setAttribute("aria-valuenow",n),i.style.width="".concat(n,"%"),l&&(t=100-n,r=new RegExp("\\d{1,3}"),l.style.transform="translateX(".concat(t,"%)"),updateLabel(l,l.title.replace(r,n)),l.innerHTML=l.innerHTML.replace(r,n))},pbIntervals[c]&&clearInterval(pbIntervals[c].interval),!1===s.interval?n():(r=tryParseInt(a.getAttribute("data-progress"),"ignore"),null!==s.start&&r<s.start?n(s.start):n(r+s.intervalIncrement),""==a.id&&(c="progressbar_".concat(randomNum(100,1e3)),a.id=c),pbIntervals[c]={},pbIntervals[c].end=o,pbIntervals[c].increment=s.intervalIncrement,pbIntervals[c].interval=setInterval(function(){var e=a.id,t=tryParseInt(a.getAttribute("data-progress"),"throw")+pbIntervals[e].increment,r=pbIntervals[e].end;t<=r?n(t):(n(r),clearInterval(pbIntervals[e].interval),delete pbIntervals[e])},s.intervalDelay))):(a.setAttribute("data-progress",0),i.style.removeProperty("width"),l&&l.style.removeProperty("transform"))}function createElementFromHTML(e){var t=document.createElement("div");return t.innerHTML=e,t.firstChild}function deleteElement(e){if(e)return e.parentNode.removeChild(e);var t=new Error;return t.name="deleteElement Error",t.message="Argument 1 is not a valid element: ".concat(e),console.error(t),!1}var cookie={get:function(){var e=0<arguments.length&&void 0!==arguments[0]&&arguments[0];function a(e){for(var t={},r=["string","name","value"],n=0;n<r.length;n++)t[r[n]]=e[n];return t}function t(e){var t=regexMatchAll("("+e+")=([^;]+)(?:;|$)",document.cookie);if(1==t.length)return a(t[0]);if(1<t.length){for(var r=[],n=0;n<t.length;n++)r.push(a(t[n]));return r}return!1}return t(e||"[^\\s=]+")},set:function(e){var t=0<arguments.length&&void 0!==e?e:{},a=mergeObj([{name:"",value:"",path:"/",domain:!1,"max-age":789e4,expires:!1,secure:!1,samesite:"lax"},t]),o=Object.keys(a),i="";return function(){if(""==a.name)throw new Error('Could not update cookie: Property "name" is required but was not specified.\r\n\r\n'+JSON.stringify(t));i+=a.name+"=",i+=val=encodeURIComponent(a.value)}(),function(){for(var e=2;e<o.length;e++){var t=o[e],r=a[t],n="";!1!==r&&("boolean"!=typeof r&&(n="="+r),i+="; "+t+n)}}(),document.cookie=i},remove:function(t,e){function r(e){setCookie({name:t,"max-age":e,expires:e})}r(!!(!(1<arguments.length&&void 0!==e)||e)&&0)}};function randomNum(e,t){return Math.round(Math.random()*(t-e)+e)}function randomID(){for(var e,t,r=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"",n=1<arguments.length&&void 0!==arguments[1]?arguments[1]:100,a=2<arguments.length&&void 0!==arguments[2]?arguments[2]:9999,o=1;o<=20;o++)if(e=randomNum(n,a),t="".concat(r).concat(e),!dom.find.id(t))return t;return!1}function checkPlural(e){return 1!=e?"s":""}function ucWords(e){for(var t=e.split(" "),r=0;r<t.length;r++){var n=t[r];t[r]=n.charAt(0).toUpperCase()+n.substring(1)}return t.join(" ")}