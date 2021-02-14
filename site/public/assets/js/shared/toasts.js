function _createForOfIteratorHelper(t,e){var i;if("undefined"==typeof Symbol||null==t[Symbol.iterator]){if(Array.isArray(t)||(i=_unsupportedIterableToArray(t))||e&&t&&"number"==typeof t.length){i&&(t=i);function a(){}var o=0;return{s:a,n:function(){return o>=t.length?{done:!0}:{done:!1,value:t[o++]}},e:function(t){throw t},f:a}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var n,s=!0,r=!1;return{s:function(){i=t[Symbol.iterator]()},n:function(){var t=i.next();return s=t.done,t},e:function(t){r=!0,n=t},f:function(){try{s||null==i.return||i.return()}finally{if(r)throw n}}}}function _unsupportedIterableToArray(t,e){if(t){if("string"==typeof t)return _arrayLikeToArray(t,e);var i=Object.prototype.toString.call(t).slice(8,-1);return"Object"===i&&t.constructor&&(i=t.constructor.name),"Map"===i||"Set"===i?Array.from(t):"Arguments"===i||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i)?_arrayLikeToArray(t,e):void 0}}function _arrayLikeToArray(t,e){(null==e||e>t.length)&&(e=t.length);for(var i=0,a=new Array(e);i<e;i++)a[i]=t[i];return a}!function(){var i=setInterval(function(){var t,a,e;"undefined"!=typeof ShiftCodesTK&&"undefined"!=typeof globalFunctionsReady&&(clearInterval(i),ShiftCodesTK.toasts={ready:!1,activeToasts:{},queuedToasts:{},containers:((e={}).main=dom.find.id("toasts"),e.activeToasts=dom.find.child(e.main,"class","active-toasts"),e.queuedToasts=dom.find.child(e.main,"class","queued-toasts"),e.serverSideToasts=dom.find.child(dom.find.id("data"),"class","server-side-toasts"),e),updateTimer:function(t,e,i){var a,o=1<arguments.length&&void 0!==e?e:"toggle",n=2<arguments.length&&void 0!==i&&i;try{!function(){if(!t||!dom.has(t,"class","toast"))throw"".concat(t," is not a valid toast element.");if(-1==["start","toggle","stop"].indexOf(o))throw'"'.concat(o,'" is not a valid Update Type.')}();var s,r=this.activeToasts[t.id];return!1!==n&&(a={infinite:-1,short:3e3,medium:6e3,long:1e4},s="string"==typeof n&&void 0!==a[n]?a[n]:"number"==typeof n?n:0,dom.find.child(t,"class","progress-bar").childNodes[0].style.animationDuration=-1<s?"".concat(s,"ms"):"",r.duration=s),"start"==o||"toggle"==o&&!dom.has(t,"class","expiring")?0<r.duration&&(edit.class(t,"add","expiring"),r&&(r.timer=setTimeout(function(){this.updateTimer(t,"stop"),this.removeToast(t)}.bind(this),r.duration))):(clearTimeout(r.timer),edit.class(t,"remove","expiring")),!0}catch(t){return console.error("toasts.updateTimer Error: ".concat(t)),!1}},newToast:function(t){var e,i,a,o,n,s,r,d,c,l=0<arguments.length&&void 0!==t?t:{};try{!function(){if(!l||"Object"!=l.constructor.name)throw"Provided configuration is not a valid configuration object."}();var u=(s={settings:{id:function(){for(var t,e=ShiftCodesTK.toasts,i="";t=randomNum(100,999),i="".concat("toast","_").concat(t),void 0!==e.activeToasts[i]||void 0!==e.queuedToasts[i];);return i}(),duration:"medium",template:!1,callback:!1},content:{icon:"fas fa-bullhorn",title:"",body:""},actions:[]},r={content:"",title:"",callback:!1,link:!1,closeToast:!1},d={actionConfirmation:{settings:{duration:"short"},content:{icon:"fas fa-check"}},fatalException:{settings:{duration:"infinite"},content:{icon:"fas fa-exclamation-triangle",title:"An error has occurred"},actions:[{content:"Refresh",title:"Refresh the page and try again",link:" "}]},formSuccess:{settings:{duration:"long"},content:{icon:"fas fa-check",title:"Success!",body:"The form was submitted successfully."}}},c=mergeObj(s,l),!1!==(n=c.settings.template)&&void 0!==d[n]&&(c=mergeObj(s,d[n],l)),function(){var t=c.actions;for(var e in t){var i=t[e];t[e]=mergeObj(r,i)}}(),c),f=edit.copy(dom.find.id("toast_template"));return f.id=u.settings.id,f.innerHTML=f.innerHTML.replaceAll(new RegExp("toast_template","g"),f.id),edit.class(dom.find.child(f,"class","icon").childNodes[0],"add",u.content.icon),a=dom.find.child(f,"class","title"),o="".concat(u.settings.id,"_title"),a.innerHTML=u.content.title,a.id=o,edit.attr(f,"add","aria-labelledby",o),e=dom.find.child(f,"class","body"),i="".concat(u.settings.id,"_body"),e.innerHTML=u.content.body,e.id=i,edit.attr(f,"add","aria-describedby",i),function(){var t,e=dom.find.child(f,"class","actions"),i=_createForOfIteratorHelper(u.actions);try{for(i.s();!(t=i.n()).done;){var a=t.value,o=!1!==a.link,n=document.createElement(o?"a":"button");o?(edit.class(n,"add","button"),edit.attr(n,"add","href",a.link)):edit.class(n,"add","styled"),edit.class(n,"add","action"),n.innerHTML=a.content,a.closeToast&&(edit.class(n,"add","dismiss-toast"),edit.attr(n,"add","aria-controls",u.settings.id)),e.appendChild(n),a.title&&updateLabel(n,a.title,["tooltip"])}}catch(t){i.e(t)}finally{i.f()}}(),this.addToast(f,u)}catch(t){return console.error("toasts.newToast Error: ".concat(t)),!1}},addToast:function(t,e){if(this.ready&&void 0===this.activeToasts[e.settings.id]&&Object.keys(this.activeToasts).length<5){var i=e.settings.duration;this.activeToasts[e.settings.id]={duration:0,callback:e.settings.callback,timer:0},t=this.containers.activeToasts.appendChild(t),isHidden(t,!1),setTimeout(function(){this.updateTimer(t,"start",e.settings.duration)}.bind(this),-1!=i?200:0)}else{if(void 0!==this.queuedToasts[e.settings.id])return!1;t=this.containers.queuedToasts.appendChild(t),this.queuedToasts[e.settings.id]={toast:t,configuration:e}}return t},removeToast:function(t){try{!function(){if(!t||!dom.has(t,"class","toast"))throw"".concat(t," is not a valid toast element.")}();var e=t.id;return isHidden(t,!0),setTimeout(function(){this.containers.activeToasts.removeChild(t),delete this.activeToasts[e],function(){var e=Object.keys(this.queuedToasts);0<e.length&&setTimeout(function(){var t=this.queuedToasts[e[0]];this.addToast(t.toast,t.configuration),delete this.queuedToasts[e[0]]}.bind(this),500)}.bind(this)()}.bind(this),300),!0}catch(t){return console.error("toasts.updateTimer Error: ".concat(t)),!1}},toastEvent:function(t){var e,i,a,o,n,s,r=ShiftCodesTK.toasts;r.ready&&0<Object.keys(r.activeToasts).length&&!dom.has(t.target,"class","toast-list")&&(e=t.type,!1!==(i=dom.has(t.target,"class","toast",null,!0))&&(a=r.activeToasts[i.id],"mouseover"==e||"mouseout"==e?(o=0<a.duration,n=dom.has(i,"class","expiring"),("mouseover"==e&&n||"mouseout"==e&&!n&&o)&&r.updateTimer(i,n?"stop":"start")):(s=dom.has(t.target,"class","action",null,!0))&&(dom.has(s,"class","dismiss-toast")&&setTimeout(function(){r.removeToast(i)},50),"function"==typeof a.callback&&a.callback(s,t))))}},a=ShiftCodesTK.toasts,t=dom.find.id("toast_template").content.children[0],dom.find.child(t,"class","dedicated"),function(){for(var t=0,e=["mouseover","mouseout","click"];t<e.length;t++){var i=e[t];a.containers.activeToasts.addEventListener(i,a.toastEvent)}}.bind(a)(),addPageLoadHook(function(){var t=ShiftCodesTK.toasts;setTimeout(function(){this.ready=!0,function(){var t=this.containers.serverSideToasts;if(0<t.childNodes.length){var e,i=_createForOfIteratorHelper(t.childNodes);try{for(i.s();!(e=i.n()).done;){var a=e.value,o=tryJSONParse(a.innerHTML);o&&this.newToast(o)}}catch(t){i.e(t)}finally{i.f()}deleteElement(t)}}.bind(this)(),function(){for(var t=this.queuedToasts,e=Object.keys(t),i=e.length-1;0<=i;i--){var a=t[e[i]];if(!1===this.addToast(a.toast,a.configuration))break;delete t[e[i]]}}.bind(this)()}.bind(t),2500)}))},250)}();