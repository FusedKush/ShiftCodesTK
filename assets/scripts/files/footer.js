/*********************************
  Footer Scripts
*********************************/

// *** Functions ***
// Return to top of the page
function returnToTop () {
  document.getElementsByTagName('header')[0].scrollIntoView({behavior: 'smooth'});
  document.getElementById('footer_return').blur();
}

// *** Immediate Functions ***
// Update the footer version number
(function () {
  function executeWhenReady() {
    if (typeof tkVer == 'object') {
      document.getElementById('footer_ver').innerHTML = tkVer.full;
    }
    else {
      setTimeout (function () { executeWhenReady(); }, 100);
    }
  }

  executeWhenReady();
})();

// *** Event Listeners ***
document.getElementById('footer_return').addEventListener('click', returnToTop);
