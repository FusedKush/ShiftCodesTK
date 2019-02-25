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
  document.getElementById('footer_ver').innerHTML = tkVer.full;
})();

// *** Event Listeners ***
document.getElementById('footer_return').addEventListener('click', returnToTop);
