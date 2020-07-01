function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}var shiftStats={total:0,new:0,expiring:0},shiftProps={game:"all",owner:!1,code:!1,filter:["active"],order:"default",limit:10,offset:0},shiftUpdates={interval:{id:0,set:function(){shiftUpdates.interval.id=setInterval(shiftUpdates.check,12e4)},clear:function(){clearInterval(shiftUpdates.interval.id)},restart:function(){shiftUpdates.clear(),shiftUpdates.set()}},stats:{},toggleIndicator:function(e,t){var d=1<arguments.length&&void 0!==t?t:0,a=dom.find.id("shift_update_indicator");dom.find.child(a,"class","counter").innerHTML=d,updateLabel(a,a.title.replace(new RegExp("\\d+"),d)),e?(a.addEventListener("click",shiftUpdates.fetch,{once:!0}),isHidden(a,!1),edit.class(a,"remove","hidden")):(edit.class(a,"add","hidden"),setTimeout(function(){isHidden(a,!0)},250))},check:function(){newAjaxRequest({file:"/assets/requests/get/shift/updates",params:{last_check:moment.utc(shiftUpdates.stats.last_check).format(),game_id:shiftProps.game},callback:function(e){var t,d=tryJSONParse(e);d&&200==d.statusCode&&(0<(t=d.payload.count)?shiftUpdates.toggleIndicator(!0,t):shiftUpdates.stats.last_check=moment.utc().valueOf())}})},fetch:function(){getCodes(),shiftUpdates.toggleIndicator(!1),shiftUpdates.stats.last_check=moment.utc().valueOf()}};function syncShiftComponents(){var e,t,d,a,m=dom.find.id("shift_header");function i(){var e=dom.find.id("shift_code_pager");return!(!e||!dom.has(e,"class","configured"))&&updatePagerProps(e,a)}!function(){var e=dom.find.child(m,"class","section badges"),t=!0,d=!1,a=void 0;try{for(var i,o=dom.find.children(e,"class","badge")[Symbol.iterator]();!(t=(i=o.next()).done);t=!0){var r,n=i.value,s=dom.get(n,"attr","data-value"),l=ucWords(s),c=shiftStats[s],f="";0<c?(f+="".concat(c," "),"total"==s?f+="SHiFT Codes Available":(r=-1!=shiftProps.filter.indexOf(s),f+="".concat(l," SHiFT Codes ").concat(r?"(Click to clear Filter)":"(Click to Filter)"),edit.attr(n,"update","aria-pressed",r))):f="total"==s?"No SHiFT Codes Available":"No ".concat(l," SHiFT Codes"),"total"!=s&&isDisabled(n,!(0<c)),edit.class(n,0<c?"remove":"add","inactive"),dom.find.child(n,"class","count").innerHTML=c,updateLabel(n,f,["tooltip"])}}catch(e){d=!0,a=e}finally{try{t||null==o.return||o.return()}finally{if(d)throw a}}}(),function(){var e=dom.find.id("shift_header_sort_filter_form"),t={sort:shiftProps.order,status_filter:shiftProps.filter,game_filter:shiftProps.game};if(!dom.has(e,"class","updated")){for(var d in t)formUpdateField(e,d,t[d]);edit.class(e,"add","updated")}}(),e=shiftProps.limit,t=shiftProps.offset,d=shiftStats.total,a={now:t/e+1,max:0<d?Math.ceil(d/e):1},i()||tryToRun({function:i})}function redeemShiftCode(e){var s=!(1<arguments.length&&void 0!==arguments[1])||arguments[1],t="string"==typeof e&&12==e.length?"code_hash":!("object"!=_typeof(e)||-1==e.constructor.name.indexOf("Element")||!dom.has(e,"class","shift-code"))&&"panel";function l(e){var t=dom.find.child(e,"class","action redeem");edit.class(e,s?"add":"remove","redeemed"),t&&(edit.attr(t,"update","aria-pressed",s),t.innerHTML=t.innerHTML.replace(s?"fa-bookmark":"fa-check",s?"fa-check":"fa-bookmark"),t.innerHTML=t.innerHTML.replace(s?"Redeem":"Redeemed",s?"Redeemed":"Redeem"),updateLabel(t,t.title.replace(s?"Mark":"Un-mark",s?"Un-mark":"Mark")))}function c(e,t){var d=!0,a=!1,i=void 0;try{for(var o,r=e[Symbol.iterator]();!(d=(o=r.next()).done);d=!0){var n=o.value,s=dom.find.child(n,"class","action redeem");s&&(isDisabled(s,!t),edit.class(s,t?"remove":"add","in-progress"))}}catch(e){a=!0,i=e}finally{try{d||null==r.return||r.return()}finally{if(a)throw i}}}if(!t)return console.error("redeemShiftCode Error: Provided SHiFT Code is not a valid Code Hash or SHiFT Code Dropdown Panel."),!1;if("panel"==t)l(e);else if("code_hash"==t){var f=dom.find.children(dom.find.id("shift_code_list"),"attr","data-code-hash",e);if(!f)return console.error('redeemShiftCode Error: No present SHiFT Code Dropdown Panels were found with a Code Hash of "'.concat(e,'". This indicates an invalid Code Hash or illegal redemption operation.')),!1;c(f,!1),newAjaxRequest({type:"POST",file:"/assets/requests/post/shift/redeem",params:{code:e,action:s?"add":"delete"},requestHeader:"form",callback:function(e){var n=tryJSONParse(e);function t(){return newToast({settings:{template:"exception"},content:{title:"Failed to redeem SHiFT Code",body:"We could not redeem this SHiFT Code due to an error. Please refresh the page and try again."}})}return!n||200!=n.statusCode&&201!=n.statusCode?(t(),!1):void setTimeout(function(){c(f,!0);var e,t,d=!0,a=!1,i=void 0;try{for(var o,r=f[Symbol.iterator]();!(d=(o=r.next()).done);d=!0){l(o.value)}}catch(e){a=!0,i=e}finally{try{d||null==r.return||r.return()}finally{if(a)throw i}}return s&&n.payload.displayToast&&(e=1==(t=n.payload.toastType)?"This SHiFT Code has been redeemed successfully! Redeemed SHiFT Codes will be marked anytime you visit ShiftCodesTK from your current browser. If your browser cookies are deleted, your redeemed SHiFT Codes may be lost.":2==t?"This SHiFT Code has been redeemed successfully! Redeemed SHiFT Codes will be marked anytime you visit ShiftCodesTK as long as you are logged in.":void 0,newToast({settings:{id:"redeemed_code_toast",duration:"infinite"},content:{icon:"fas fa-key",title:"SHiFT Code Redeemed!",body:e}})),!0},500)}})}return!0}function updateShiftCodeTimestamp(e){var t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:void 0,d=dom.find.child(dom.find.child(dom.find.child(e,"class","code-info"),"class","last-update"),"tag","dd"),a=moment.utc(t);d.innerHTML=a.fromNow(),updateLabel(d,a.format("MMMM DD, YYYY hh:mm A [UTC]"),["tooltip"])}function retrieveCodes(){var r={};function n(e){for(var t=0,d=[r.badges.new,r.badges.expiring,r.addCodeButton,r.sortFilterButton,r.pager];t<d.length;t++){var a=d[t];a&&!dom.has(a,"class","inactive")&&isDisabled(a,!e)}}function s(e){var t={};for(var d in t.overlay=dom.find.id("shift_overlay"),t.spinner=dom.find.child(t.overlay,"class","spinner"),t.error=dom.find.child(t.overlay,"class","error"),e){var a=e[d];isHidden(t[d],!a)}}function l(m){try{var h=edit.copy(dom.find.id("shift_code_template")),n={};n.header=dom.find.child(h,"class","header"),n.body=dom.find.child(h,"class","body active"),n.deletedBody=dom.find.child(h,"class","body deleted");var e,t,p="".concat("shift_code","_").concat(m.properties.code_id);return function(){var e=["id","for","data-view","data-target","data-layer-target","data-layer-targets","aria-labelledby","aria-describedby"],t=h.querySelectorAll("[".concat(e.join("], ["),"]"));h.id=p,edit.attr(h,"add","data-code-id",m.properties.code_id),null!==m.properties.code_hash&&edit.attr(h,"add","data-code-hash",m.properties.code_hash);var d=!0,a=!1,i=void 0;try{for(var o,r=t[Symbol.iterator]();!(d=(o=r.next()).done);d=!0)for(var n=o.value,s=0,l=e;s<l.length;s++){var c=l[s],f=dom.get(n,"attr",c);!1!==f&&""!=f&&edit.attr(n,"update",c,function(e){var t=e.split(", ");for(var d in t)t[d]="".concat(p,"_").concat(t[d]);return t.join(", ")}(f))}}catch(e){a=!0,i=e}finally{try{d||null==r.return||r.return()}finally{if(a)throw i}}m.states.userHasRedeemed&&redeemShiftCode(h,!0)}(),"deleted"!=m.properties.code_state?(deleteElement(n.deletedBody),dom.find.child(n.header,"class","reward").innerHTML=m.info.reward,(w=m.states).codeIsActive&&"active"==m.properties.code_state?(0==m.info.reward.trim().search("\\d{1} Golden Key(s){0,1}$")?edit.class(h,"add","basic"):edit.class(h,"add","rare"),w.codeIsNew&&edit.class(h,"add","new"),w.codeIsExpiring&&edit.class(h,"add","expiring")):"hidden"==m.properties.code_state?edit.class(h,"add","hidden"):w.codeIsActive||edit.class(h,"add","expired"),"all"==shiftProps.game&&(T=dom.find.child(n.header,"class","label game-label"),S=m.properties.game_id,C=shiftNames[S],edit.class(h,"add","game-label"),edit.class(T,"add",S),T.innerHTML=T.innerHTML.replace("Borderlands",C),updateLabel(T,"SHiFT Code for ".concat(C),["tooltip"])),w.codeWasRecentlyAdded?edit.class(h,"add","recently-added"):w.codeWasRecentlyUpdated&&edit.class(h,"add","recently-updated"),w.userIsOwner&&edit.class(h,"add","owned"),M=dom.find.child(n.header,"class","progress-bar"),P=dom.find.child(M,"class","progress"),F=0,E="",E=m.info.expiration_date?(H=(x=moment.utc(m.info.expiration_date)).diff(m.info.release_date,"hours"),k=x.diff(moment.utc(),"hours"),L=x.fromNow(!0),m.states.codeIsActive?(F=100-Math.round(k/H*100),ucWords("".concat(L," Remaining"))):(F=100,ucWords("Expired ".concat(L," Ago")))):(edit.class(M,"add","inactive"),F=0,"No Expiration Date"),edit.attr(M,"add","aria-valuenow",F),updateLabel(M,E,["tooltip"]),edit.class(P,"add",m.properties.game_id),P.style.width="".concat(F,"%"),function(){var i={dates:{}};i.dates.date="MMM DD, YYYY",i.dates.expandedDate="dddd, MMMM DD, YYYY",i.dates.time="h:mm A zz",i.dates.full="".concat(i.dates.date," ").concat(i.dates.time),i.dates.expanded="".concat(i.dates.expandedDate," ").concat(i.dates.time),i.calendars={sameDay:"[Today]",nextDay:"[Tomorrow]",nextWeek:"dddd",lastDay:"[Yesterday]",lastWeek:"[Last] dddd"};var d={release:{}};for(var e in d.release.formats=function(){var e={simple:{},full:{},expanded:{}},t=i.dates,d=i.calendars;for(var a in d)e.simple[a]="".concat(d[a]),e.full[a]="".concat(d[a],", ").concat(t.date),e.expanded[a]="".concat(d[a],", ").concat(t.expandedDate);return e.simple.sameElse=e.full.sameElse=t.date,e.expanded.sameElse=t.expandedDate,e}(),d.release.dates=function(){var e=m.info.release_date;if(e){var t=moment(e);return{simple:t.calendar(null,d.release.formats.simple),full:t.calendar(null,d.release.formats.full),expanded:t.calendar(null,d.release.formats.expanded)}}return!1}(),d.expiration={},d.expiration.formats=function(){var e={simple:{},full:{},expanded:{}},t=i.dates,d=i.calendars;for(var a in d)e.simple[a]="".concat(d[a],", ").concat(t.time),e.full[a]="".concat(d[a],", ").concat(t.full),e.expanded[a]="".concat(d[a],", ").concat(t.expanded);return e.simple.sameElse=e.full.sameElse=t.full,e.expanded.sameElse=t.expanded,e}(),d.expiration.dates=function(){var e=m.info.expiration_date;if(e){var t=moment.tz(e,m.info.timezone);return{simple:t.calendar(null,d.expiration.formats.simple),full:t.calendar(null,d.expiration.formats.full),expanded:t.calendar(null,d.expiration.formats.expanded)}}return!1}(),d){var t=d[e].dates,a={};a.main=D(e),a.simple=dom.find.child(a.main,"class","simple"),a.full=dom.find.child(a.main,"tag","dd"),t?(t.simple!=t.full?a.simple.firstChild.innerHTML=t.simple:deleteElement(a.simple),a.full.innerHTML=t.full,updateLabel(a.full,t.expanded,["tooltip"])):(edit.class(a.main,"add","inactive"),updateLabel(a.full,"No ".concat(ucWords(e)," Date"),["tooltip"]),deleteElement(a.simple),a.full.innerHTML="N/A")}}(),c=D("src"),f=dom.find.child(c,"class","link"),u=dom.find.child(c,"class","no-link"),(v=m.info.source)?(f.href=v,f.innerHTML+=v,deleteElement(u)):(edit.class(c,"add","inactive"),deleteElement(f)),_=D("notes"),null!==(b=m.info.notes)?(y=dom.find.child(_,"tag","ul"),g=-1!=b.indexOf("-")?b.replace(new RegExp("-.*","g"),function(e){return"".concat(e.replace(new RegExp("-\\s{1}","g"),"<li>"),"</li>")}):"<li>".concat(b,"</li>"),y.innerHTML=g):deleteElement(_.parentNode),function(){for(var e=m.codes,t=0,d=["pc","xbox","ps"];t<d.length;t++){var a=d[t],i=dom.find.child(n.body,"class",a),o=Object.values(e["platforms_".concat(a)]).join(" / "),r=e["code_".concat(a)];dom.find.child(i,"class","title").innerHTML=o,dom.find.child(i,"class","display").innerHTML=r,updateLabel(dom.find.child(i,"class","display"),"".concat(o," SHiFT Code"),["tooltip"]),dom.find.child(i,"class","value").value=r}}(),l=dom.find.child(n.body,"class","footer"),(a={}).container=dom.find.child(l,"class","actions"),a.redeem=dom.find.child(a.container,"class","redeem"),a.optionsMenu=dom.find.child(a.container,"class","options-menu"),m.states.codeIsActive||isDisabled(a.redeem,!0),(s={}).container=dom.find.child(l,"class","code-info"),s.id=dom.find.child(s.container,"class","id"),s.lastUpdate=dom.find.child(s.container,"class","last-update"),s.owner=dom.find.child(s.container,"class","owner"),d("id",i=m.properties.code_id,"SHiFT Code #".concat(i)),updateShiftCodeTimestamp(h,m.info.last_update),m.states.userCanEdit?(o=m.properties.owner_username,r=m.properties.owner_id,d("owner",o,"".concat(o," #").concat(r))):deleteElement(s.owner),function(){var e,t,d,a=dom.find.children(h,"class","shift-code-options-menu"),i=!0,o=!1,r=void 0;try{for(var n,s=a[Symbol.iterator]();!(i=(n=s.next()).done);i=!0)d=t=e=void 0,t=n.value,(d={}).codeID=dom.find.child(t,"class","code-id"),d.share=dom.find.child(t,"attr","data-value","share"),d.report=dom.find.child(t,"attr","data-value","report"),d.editActions=dom.find.child(t,"class","edit-actions"),d.makePublic=dom.find.child(d.editActions,"attr","data-value","make_public"),d.makePrivate=dom.find.child(d.editActions,"attr","data-value","make_private"),edit.attr(t,"add","data-code-id",m.properties.code_id),d.codeID.innerHTML=m.properties.code_id,m.states.userCanEdit?(isDisabled(d.report),e="active"==m.properties.code_state?d.makePublic:d.makePrivate,isHidden(e,!0),isDisabled(e,!0)):deleteElement(d.editActions)}catch(e){o=!0,r=e}finally{try{i||null==s.return||s.return()}finally{if(o)throw r}}}(),dropdownPanelSetup(h),multiView_setup(dom.find.child(n.body,"class","multi-view"))):(deleteElement(n.body),e=dom.find.child(n.deletedBody,"class","timestamp"),t=moment.utc(m.info.last_update),edit.class(h,"add","deleted"),dom.find.child(n.header,"class","reward").innerHTML="SHiFT Code ".concat(m.properties.code_id),e.innerHTML=t.fromNow(),updateLabel(e,t.format("MMMM DD, YYYY, hh:mm A [UTC]"),["tooltip"])),h}catch(e){return console.error("getShiftCodePanel Error: ".concat(e)),!1}function d(e,t,d){var a=2<arguments.length&&void 0!==d&&d,i=dom.find.child(s[e],"tag","dd");i.innerHTML=t,a&&updateLabel(i,a,["tooltip"])}var a,i,o,r,s,l,c,f,u,v,y,g,_,b,T,S,C,w,x,H,k,L,M,P,F,E;function D(e){return dom.find.child(dom.find.child(n.body,"class","section ".concat(e)),"class","content")}}r.header=dom.find.id("shift_header"),r.badges={},r.badges.container=dom.find.child(r.header,"class","section badges"),r.badges.active=dom.find.child(r.badges.container,"class","active"),r.badges.new=dom.find.child(r.badges.container,"class","new"),r.badges.expiring=dom.find.child(r.badges.container,"class","expiring"),r.addCodeButton=dom.find.id("shift_header_add"),r.sortFilterButton=dom.find.id("shift_header_sort_filter"),r.list=dom.find.id("shift_code_list"),r.pager=dom.find.id("shift_code_pager"),shiftUpdates.interval.clear(),n(!1),s({overlay:!0,spinner:!0,error:!1}),function(){for(var e=dom.find.children(r.list,"class","shift-code"),t=e.length-1;0<=t;t--)deleteElement(e[t])}(),lpbUpdate(50,!0,{start:15}),newAjaxRequest({file:"/assets/requests/get/shift/codes",params:shiftProps,callback:function(e){var t=tryJSONParse(e);if(t&&200==t.statusCode){lpbUpdate(75);var d=t.payload.counts,a=t.payload.shift_codes;if(d&&shiftStats!=d&&(shiftStats=d,syncShiftComponents()),0<a.length){for(var i=0;i<a.length;i++){var o=l(a[i]);o&&(o.style.animationDelay="".concat(.2*i,"s"),r.list.appendChild(o))}hashUpdate(),lpbUpdate(100),s({overlay:!1,spinner:!1,error:!1})}else lpbUpdate(100),s({overlay:!0,spinner:!1,error:!0});shiftUpdates.interval.set(),setTimeout(function(){n(!0)},500)}else lpbUpdate(100),s({overlay:!0,spinner:!1,error:!0}),ShiftCodesTK.toasts.newToast({settings:{template:"fatalException"},content:{title:"SHiFT Code Retrieval Error",body:"We could not retrieve any SHiFT Codes due to an error. Please refresh the page and try again."}})}})}function shiftCodeFormGameChangeEvent(){var e=dom.find.id("shift_code_form"),t=dom.find.child(e,"attr","name","general_game_id").value,d=dom.find.children(e,"attr","data-supported-games"),a=!0,i=!1,o=void 0;try{for(var r,n=d[Symbol.iterator]();!(a=(r=n.next()).done);a=!0){var s=r.value,l=tryJSONParse(dom.get(s,"attr","data-supported-games"));for(var c in l){var f=l[c],m=dom.find.child(s,"attr","value",c),h=dom.find.parent(m,"class","field"),p=-1!=f.indexOf(t);isDisabled(m,!p),edit.class(h,p?"remove":"add","disabled"),edit.attr(m,p?"add":"remove","checked")}}}catch(e){i=!0,o=e}finally{try{a||null==n.return||n.return()}finally{if(i)throw o}}}!function(){var t=setInterval(function(){var o,r,n,e;"undefined"!=typeof globalFunctionsReady&&"undefined"!=typeof moment&&(clearInterval(t),function(){if(props=tryJSONParse(dom.get(dom.find.id("shift_code_list"),"attr","data-shift")),props)for(var e in props)void 0!==shiftProps[e]&&(shiftProps[e]=props[e])}(),function(){for(var e=dom.find.id("shift_code_template").content.children[0],t=dom.find.children(e,"class","layer"),d=0;d<t.length;d++){var a=t[d];a.id="shift_code_layer_".concat(d),ShiftCodesTK.layers.setupLayer(a)}}(),hashState=addHashListener("shift_code_",function(e){shiftProps.offset=0,shiftProps.code=e.replace("#shift_code_",""),retrieveCodes(),shiftProps.code=!1}),hashState||retrieveCodes(),e=moment.utc().valueOf(),shiftUpdates.stats={first_check:e,last_check:e},window.addEventListener("click",function(e){var t,d,a,i,o,r,n=e.target;-1!=n.id.indexOf("shift_header_count")?(t=dom.has(n,"attr","aria-pressed","true"),d=dom.get(n,"attr","data-value"),a=shiftProps.filter,t?a.splice(a.indexOf(d),1):a.push(d),shiftProps.offset=0,retrieveCodes()):dom.has(n,"class","redeem")&&(o=!!(i=dom.find.parent(n,"class","shift-code"))&&dom.get(i,"attr","data-code-hash"),r=!!i&&dom.has(i,"class","redeemed"),i&&o&&!dom.has(i,"class","expired")&&redeemShiftCode(o,!r))}),dom.find.id("shift_header_add")&&dom.find.id("shift_header_add").addEventListener("click",function(e){var t=dom.find.id("shift_code_modal"),d=dom.find.child(t,"tag","form"),a=dom.find.children(d,"class","input");d.reset();var i=!0,o=!1,r=void 0;try{for(var n,s=a[Symbol.iterator]();!(i=(n=s.next()).done);i=!0){var l=n.value,c=dom.get(l,"attr","name");-1==c.indexOf("auth")&&formUpdateField(d,c,"")}}catch(e){o=!0,r=e}finally{try{i||null==s.return||s.return()}finally{if(o)throw r}}formUpdateField(d,"general_game_id","all"!=shiftProps.game?shiftProps.game:""),shiftCodeFormGameChangeEvent(),toggleModal(t,!0)}),r=dom.find.child(dom.find.id("shift_header"),"class","slideout"),(n=dom.find.id("shift_header_sort_filter")).addEventListener("click",function(e){edit.attr(n,"toggle","aria-pressed"),isHidden(r)}),o=dom.find.child(r,"tag","form"),dom.find.child(o,"class","submit").addEventListener("click",function(e){var t=ShiftCodesTK.forms.getFormData(o),d={game:t.game_filter,filter:t["status_filter[]"],order:t.sort};for(var a in event.preventDefault(),d){var i=d[a];void 0!==i&&(shiftProps[a]=i)}d.game&&edit.attr(document.body,"update","data-theme","all"!=d.game?d.game:"main"),edit.attr(n,"update","aria-pressed","false"),isHidden(r,!0),setTimeout(retrieveCodes,100)}),ShiftCodesTK.layers.addLayerListener("shift_code_options_menu",function(e,n){var t,s,u,d,a,v=dom.get(e,"attr","data-value"),i=dom.get(n,"attr","data-code-id");i&&("edit"==v?(t=dom.find.id("shift_code_".concat(i)),s=dom.find.child(t,"class","view edit"),newAjaxRequest({file:"/assets/requests/get/shift/codes",type:"GET",params:{code:i,filter:["inactive","active"],game:"all",limit:1,offset:0,order:"default",owner:!0},callback:function(e){var t=tryJSONParse(e);function d(e){newToast({settings:{template:"exception"},content:{title:"Failed to edit SHiFT Code",body:0<arguments.length&&void 0!==e?e:"This SHiFT Code could not be edited due to an error while downloading the SHiFT Code. Please try again later."}}),isDisabled(target,!1)}if(!t||200!=t.statusCode)return d(),!1;var a=t.payload.shift_codes[0];if(a)if(a.states.userCanEdit){var i=dom.find.child(s,"tag","form");!a.info.release_date||moment(a.info.release_date),!a.info.expiration_date||moment.tz(a.info.expiration_date,a.info.timezone);dom.has(i,"class","configured")||formSetup(i);var o={general_code_id:a.properties.code_id,general_reward:a.info.reward};for(var r in i.reset(),o){formUpdateField(i,r,o[r])}multiView_update(s),ShiftCodesTK.layers.toggleLayer(n,!1),setTimeout(function(){},500)}else newToast({settings:{duration:"infinite"},content:{title:"Failed to edit SHiFT Code",body:"You do not have permission to edit this SHiFT Code."}});else d()}})):"make_public"==v||"make_private"==v?(u=dom.find.id("shift_code_".concat(i)),ShiftCodesTK.layers.toggleLayer(n,!1),edit.class(u,"make_public"==v?"remove":"add","hidden"),dom.find.child(dom.find.child(u,"class","last-update"),"tag","dd").innerHTML=moment().fromNow(),function(){var e=dom.find.children(u,"class","shift-code-options-menu"),t=!0,d=!1,a=void 0;try{for(var i,o=e[Symbol.iterator]();!(t=(i=o.next()).done);t=!0){var r=i.value,n=dom.find.children(r,"class","visibility-toggle"),s=!0,l=!1,c=void 0;try{for(var f,m=n[Symbol.iterator]();!(s=(f=m.next()).done);s=!0){var h=f.value,p=dom.get(h,"attr","data-value")==v;isHidden(h,p),isDisabled(h,p)}}catch(e){l=!0,c=e}finally{try{s||null==m.return||m.return()}finally{if(l)throw c}}}}catch(e){d=!0,a=e}finally{try{t||null==o.return||o.return()}finally{if(d)throw a}}}()):"delete"==v&&(d=dom.find.id("shift_code_deletion_confirmation_modal"),(a=dom.find.child(d,"tag","form")).reset(),formUpdateField(a,"code_id",i),toggleDropdownMenu(n,!1),toggleModal(d,!0)))}),tryToRun({function:function(){var e=dom.find.id("shift_code_pager");return!(!e||!dom.has(e,"class","configured"))&&(addPagerListener(dom.find.id("shift_code_pager"),function(e){return shiftProps.offset=e,retrieveCodes(),!0}),!0)}}))},250)}();