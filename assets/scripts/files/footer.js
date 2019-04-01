/*********************************
  Footer Scripts
*********************************/

// *** Immediate Functions ***
// Update the footer version number
(function () {
  function executeWhenReady() {
    if (typeof serverVersion == 'string') {
      document.getElementById('footer_ver').innerHTML = serverVersion;
    }
    else {
      setTimeout (function () { executeWhenReady(); }, 100);
    }
  }

  executeWhenReady();
})();

// *** Event Listeners ***
// Remove focus when returning to top
document.getElementById('footer_return').addEventListener('click', function (e) { this.blur(); });
