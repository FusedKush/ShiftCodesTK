function _createForOfIteratorHelper(e,a){var r;if("undefined"==typeof Symbol||null==e[Symbol.iterator]){if(Array.isArray(e)||(r=_unsupportedIterableToArray(e))||a&&e&&"number"==typeof e.length){r&&(e=r);function t(){}var o=0;return{s:t,n:function(){return o>=e.length?{done:!0}:{done:!1,value:e[o++]}},e:function(e){throw e},f:t}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var n,i=!0,d=!1;return{s:function(){r=e[Symbol.iterator]()},n:function(){var e=r.next();return i=e.done,e},e:function(e){d=!0,n=e},f:function(){try{i||null==r.return||r.return()}finally{if(d)throw n}}}}function _unsupportedIterableToArray(e,a){if(e){if("string"==typeof e)return _arrayLikeToArray(e,a);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?_arrayLikeToArray(e,a):void 0}}function _arrayLikeToArray(e,a){(null==a||a>e.length)&&(a=e.length);for(var r=0,t=new Array(a);r<a;r++)t[r]=e[r];return t}var pagers={isLoaded:!1};function pagerState(e){var a,r=1<arguments.length&&void 0!==arguments[1]?arguments[1]:"toggle",t=_createForOfIteratorHelper(dom.find.children(e,"tag","button"));try{for(t.s();!(a=t.n()).done;){var o=a.value;dom.has(o,"class","unavailable")||("toggle"==r&&(r=!o.disabled),isDisabled(o,r))}}catch(e){t.e(e)}finally{t.f()}}function pagerUpdate(e){var a,r,t,o,s,u=1<arguments.length&&void 0!==arguments[1]?arguments[1]:1,m=pagers[e.id];function p(e,a){var r="unavailable";edit.class(e,a?"add":"remove",r),(!a&&dom.has(e,"class",r)||a)&&isDisabled(e,a)}function f(e,a,r){var t=2<arguments.length&&void 0!==r&&r,o=m.subtractoffset?m.offset:0;e.setAttribute("data-page",a),e.setAttribute("data-value",a*m.offset-o),t&&(updateLabel(e,dom.get(e,"attr","aria-label").replace(new RegExp("\\d+"),a),["aria","tooltip"]),e.childNodes[0].innerHTML=a)}if(!dom.has(e,"class","pager")||!m)return console.error("pagerUpdate Error: Provided element is not a valid Pager."),!1;if(u>m.max)return console.error("pagerUpdate Error: The new page number of ".concat(u," exceeds the maximum page number of ").concat(m.max,".")),!1;function n(n,e){for(var i=Math.floor((e-n)/2),a=function(){var e=u-i,a=u+i,r=m.min+n,t=m.max-n;if(r<=e&&a<=t)return e;if(r<=e){var o=t-2*i;return 0<o?o:1}return a<=t?r:void 0}(),r=0,t=n;t<e;t++){f(s[t],a+r,!0),r++}var o,d=_createForOfIteratorHelper(s);try{for(d.s();!(o=d.n()).done;){var c=o.value,l=tryParseInt(dom.get(c,"attr","data-page"))==u;edit.attr(c,"update","aria-pressed",l),p(c,l)}}catch(e){d.e(e)}finally{d.f()}}return a=dom.find.child(e,"class","previous"),(r=u-1)>=m.min?(f(a,r),p(a,!1)):(f(a,m.min),p(a,!0)),t=dom.find.child(e,"class","next"),(o=u+1)<=m.max?(f(t,o),p(t,!1)):(f(t,m.max),p(t,!0)),5==(s=dom.find.children(e,"class","jump")).length?(f(s[0],m.min,!0),f(s[4],m.max,!0),n(1,4)):n(0,s.length),m.now=u,pagerState(e,!1),!0}function addPagerListener(e,a){var r=pagers[e.id];return dom.has(e,"class","pager")&&r?"function"!=typeof a?(console.error("addPagerListener Error: Provided callback function is not a valid function."),!1):(r.customCallbacks.push(a),!0):(console.error("addPagerListener Error: Provided element is not a valid Pager."),!1)}function addPagerListeners(e,a){return console.warn("Deprecation Notice: addPagerListeners has been deprecated, replaced by addPagerListener(), and will be removed in the near future. Please update your existing code as soon as possible."),addPagerListener(e,a)}function updatePagerProps(u,e){var a=pagers[u.id],r=!1;if(dom.has(u,"class","pager")&&e||(console.error("addPagerListener Error: Provided element is not a valid Pager."),r=!0),void 0!==e.now&&(void 0!==e.min&&e.now<e.min?(console.error("addPagerListener Error: The provided page value of ".concat(e.now," exceeds the provided minimum value of ").concat(e.min,".")),r=!0):void 0===e.min&&e.now<a.min&&(console.error("addPagerListener Error: The provided page value of ".concat(e.now," exceeds the existing minimum value of ").concat(a.min,".")),r=!0),void 0!==e.max&&e.now>e.max?(console.error("addPagerListener Error: The provided page value of ".concat(e.now," exceeds the provided maximum value of ").concat(e.max,".")),r=!0):void 0===e.max&&e.now>a.max&&(console.error("addPagerListener Error: The provided page value of ".concat(e.now," exceeds the existing maximum value of ").concat(a.max,".")),r=!0)),void 0===e.now&&(void 0!==e.min&&a.now<e.min&&(console.error("addPagerListener Error: The existing current page value of ".concat(a.now," exceeds the provided minimum value of ").concat(e.min,".")),r=!0),void 0!==e.max&&a.now>e.max&&(console.error("addPagerListener Error: The existing current page value of ".concat(a.now," exceeds the provided maximum value of ").concat(e.max,".")),r=!0)),void 0!==e.min&&(void 0!==e.max&&e.min>e.max?(console.error("addPagerListener Error: The provided minimum page value of ".concat(e.min," exceeds the provided maximum value of ").concat(e.max,".")),r=!0):void 0!==e.max&&e.min>a.max&&(console.error("addPagerListener Error: The provided minimum page value of ".concat(e.min," exceeds the existing maximum value of ").concat(a.max,".")),r=!0)),void 0!==e.max&&null==e.min&&e.max<a.min&&(console.error("addPagerListener Error: The provided maximum page value of ".concat(e.max," exceeds the existing minimum value of ").concat(a.min,".")),r=!0),r)return!1;for(var t in 1==Object.keys(e).length&&e.now&&console.warn('updatePagerProps: Pager Property "now" should not be updated via updatePagerProps if it is the only property that needs to be changed. In these cases, use pagerUpdate() instead.'),e){var o=e[t];a[t]&&("customCallbacks"!=t?a[t]=o:console.warn('updatePagerProps: Pager Property "customCallbacks" cannot be updated via updatePagerProps. Use addPagerListener() instead.'))}return function(){var c,l,s=dom.find.children(u,"class","jump");if(1<s.length)for(var e=s.length-1;0<e;e--)deleteElement(s[e]);1<a.max&&(c=a.max<=5?a.max-1:4,l=dom.find.child(dom.find.child(u,"class","jumps"),"class","content-container"),function(){for(var e=["id","aria-describedby","data-layer-target","data-layer-targets"],a=new RegExp("(".concat(u.id,"|pager)_(jump)_(\\d+)")),r=1;r<=c;r++){var t,o="".concat(u.id,"_$2_").concat(r),n=l.appendChild(edit.copy(s[0])),i=l.appendChild(edit.copy(s[0].nextElementSibling)),d=_createForOfIteratorHelper(e);try{for(d.s();!(t=d.n()).done;)attr=t.value,newJumpValue=dom.get(n,"attr",attr),newTooltipValue=dom.get(i,"attr",attr),newJumpValue&&edit.attr(n,"update",attr,newJumpValue.replace(a,o)),newTooltipValue&&edit.attr(i,"update",attr,newTooltipValue.replace(a,o))}catch(e){d.e(e)}finally{d.f()}}}())}(),pagerUpdate(u,a.now),!0}function configurePager(n){var e=edit.copy(dom.find.id("pager_template")),i=n.id?n.id:"pager_".concat(randomNum(100,1e3));return e.id=i,function(){pagers[i]={min:1,now:1,max:1,offset:1,subtractoffset:!1,onclick:!1,customCallbacks:[]};for(var e=0,a=Object.keys(pagers[i]);e<a.length;e++){var r,t=a[e],o=n.getAttribute("data-".concat(t));o&&(r=void 0,r=tryParseInt(o,"ignore")||("true"==o||"false"==o?"true"==o:o),pagers[i][t]=r)}}(),function(){pagers[i];var e=n.getAttribute("data-label");if(e){var a,r=_createForOfIteratorHelper(dom.find.children(n,"class","jump"));try{for(r.s();!(a=r.n()).done;){var t=a.value;updateLabel(t,dom.get(t,"attr","aria-label").replace("Page",e),["aria","tooltip"])}}catch(e){r.e(e)}finally{r.f()}}}(),ShiftCodesTK.layers.setupChildLayers(e),updatePagerProps(e,{}),n.parentNode.replaceChild(e,n),e}pagerScripts=setInterval(function(){globalFunctionsReady&&(clearInterval(pagerScripts),function(){var e,a=_createForOfIteratorHelper(dom.find.children(document,"class","pager"));try{for(a.s();!(e=a.n()).done;){var r=e.value;dom.has(r,"class","no-auto-config")||dom.has(r,"class","configured")||configurePager(r)}}catch(e){a.e(e)}finally{a.f()}}(),window.addEventListener("click",function(e){if(dom.has(e.target,"class","pager-button",null,!0)){var a=dom.has(e.target,"class","pager",null,!0);if(a){var r=tryParseInt(dom.get(e.target,"attr","data-page")),t=pagers[a.id];if(r!=t.now){if(pagerState(a,!0),t.onclick&&tryToRun({attempts:20,delay:250,function:function(){var e=dom.find.id(t.onclick);return!(!e||e.disabled)&&(e.focus(),!0)},customError:'Focus Target for pager "'.concat(a.id,'" was not found or is disabled.')}),t.customCallbacks){var o,n=_createForOfIteratorHelper(t.customCallbacks);try{for(n.s();!(o=n.n()).done;){(0,o.value)(tryParseInt(dom.get(e.target,"attr","data-value")))}}catch(e){n.e(e)}finally{n.f()}}setTimeout(function(){pagerUpdate(a,r)},250)}}}}),pagers.isLoaded=!0)},250);