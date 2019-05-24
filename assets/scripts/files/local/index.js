/*********************************
  Index Page Scripts
*********************************/

// *** Variables ***
var addLandingFlagsRetry;

// *** Functions ***
function addLandingFlags () {
  if (typeof shiftBadgeCount != 'undefined') {
    let flags = {
      'template': document.getElementById('flag_template')
    };
    let buttons = document.getElementById('banner').getElementsByTagName('a');

    clearInterval(addLandingFlagsRetry);

    for (i = 0; i < buttons.length; i++) {
      let button = buttons[i];
      let buttonName = button.className;

      if (shiftBadgeCount.new[buttonName] > 0 || shiftBadgeCount.expiring[buttonName] > 0) {
          (function () {
            flags.root = flags.template.content.children[0].cloneNode(true);
            flags.new = flags.root.getElementsByClassName('flag new')[0];
            flags.exp = flags.root.getElementsByClassName('flag exp')[0];
          })();

        if (shiftBadgeCount.new[buttonName] == 0)      { flags.new.remove(); }
        if (shiftBadgeCount.expiring[buttonName] == 0) { flags.exp.remove(); }

        button.appendChild(flags.root);
      }
    }
  }
}

// *** Immediate Functions ***
// Add landing flags
addLandingFlagsRetry = setInterval(addLandingFlags, 250);
