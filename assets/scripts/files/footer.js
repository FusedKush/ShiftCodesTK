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
// Add the appropriate Return to Top element
(function () {
  let top = document.getElementsByTagName('header')[0];
  let support = typeof top.scrollIntoView == 'function';
  // Create Button or Link based on scrollIntoView support
  let e = (function () {
    if (support === true) { return document.createElement('button'); }
    else                  { return document.createElement('a'); }
  })();
  let icon = document.createElement('span');
  let label = 'Return to Top';

  // Element Properties
  e.classList.add('return');
  e.id = 'footer_return';
  e.title = label;
  e.setAttribute('aria-label', label);

  // Link-specific properties
  if (support === false) {
    e.href = '#';
    e.setAttribute('data-internalLink', true);
  }

  // Icon Properties
  icon.classList.add('fas');
  icon.classList.add('fa-arrow-alt-circle-up');

  // Add to Footer
  e.appendChild(icon);
  document.getElementById('footer').getElementsByClassName('content-wrapper')[0].appendChild(e);

  // Button-specific listener
  if (support === true) {
    document.getElementById('footer_return').addEventListener('click', (function (e) {
      top.scrollIntoView({behavior: 'smooth'});
      this.blur();
    }));
  }
})();
