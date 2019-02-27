/*********************************
  ShiftCodesTK Server Version
*********************************/

serverVersion = {};
/*******************************/
  serverVersion.major = 1;
  serverVersion.minor = 0;
  serverVersion.patch = 3;
/*******************************/
(function () {
  let keys = Object.keys(serverVersion);
  let con = '';

  for (i = 0; i < 3; i++) {
    con += serverVersion[keys[i]];

    if (i != 2) { con += '.'; }
  }

  serverVersion.version = con;
})();
