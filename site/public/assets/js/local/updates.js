var changelogProps={limit:10,offset:0,hash:!1,firstRun:!0},changelogIdPrefix="changelog_";function getChangelogs(){!function(){for(var e="",a=Object.keys(changelogProps),n=0;n<a.length;n++){var o=a[n],g=changelogProps[o];e+="&".concat(o,"=").concat(g)}e.replace("&","?")}();var e=dom.find.id("updates_header_jump");dom.find.id("changelog_list");lpbUpdate(90,!0,{start:20}),isDisabled(e,!0)}!function(){var e=setInterval(function(){globalFunctionsReady&&pagers&&(clearInterval(e),addHashListener(changelogIdPrefix,function(e){changelogProps.offset=0,changelogProps.hash=e.replace("#".concat(changelogIdPrefix),""),getChangelogs(),changelogProps.hash=!1})||getChangelogs())},250)}();