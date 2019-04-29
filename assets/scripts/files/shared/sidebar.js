/*********************************
  Sidebar Scripts
*********************************/
// *** Functions ***
// Toggle the Sidebar
function toggleSB (outsideClickTrue) {
  let sb = document.getElementById('sidebar');
  let btn = {
    'nav': document.getElementById('navbar_sb'),
    'sb': document.getElementById('sidebar_toggle')
  };
  let state = sb.getAttribute('data-expanded') == 'true';

  // Updates the Visibility of the Sidebar
  function updateVis () {
    vishidden(sb, state);
  }
  // Update Sidebar and Button states
  function updateState () {
    sb.setAttribute('data-expanded', !state);
    btn.nav.setAttribute('data-pressed', !state);
    btn.nav.setAttribute('aria-pressed', !state);

    if (state === true && outsideClickTrue !== true) {
      btn.nav.focus();
    }
    else if (state === false) {
      btn.sb.focus();
    }
  }

  if (state === true) {
    updateState();
    window.removeEventListener('click', sidebarEventListenerCheckClick);
    window.removeEventListener('keydown', sidebarEventListenerCheckKey);
    setTimeout(updateVis, 300);
  }
  else {
    updateVis();
    window.addEventListener('click', sidebarEventListenerCheckClick);
    window.addEventListener('keydown', sidebarEventListenerCheckKey);
    setTimeout(updateState, 50);
  }
}
// Check for clicks outside of the Sidebar
function sidebarCheckClick (event) {
  let base = document.getElementById('sidebar');
  let state = base.getAttribute('data-expanded') == 'true';

  if (state === true) {
    let target = event.target.parentNode;
    let matches = [ document.getElementsByTagName('body')[0] ];

    for (i = 0; i < matches.length; i++) {
      if (target == matches[i]) {
        toggleSB(true);
        break;
      }
    }
  }
}
// Check for keyboard presses while Sidebar is open
function sidebarCheckKeys (event) {
  let active = document.activeElement;
  let e = {};
    (function () {
      e.toggle = document.getElementById('sidebar_toggle');
      e.links = document.getElementById('sidebar').getElementsByClassName('panel')[0].getElementsByClassName('link');
      e.last = e.links[e.links.length - 1];
    });

  if (event.shiftKey === false && event.key == 'Tab' && active == e.last || event.shiftKey === true && event.key == 'Tab' && active == e.toggle) {
    event.preventDefault();

    if (event.shiftKey === false && event.key == 'Tab' && active == e.last) {
      e.toggle.focus();
    }
    else if (event.shiftKey === true && event.key == 'Tab' && active == e.toggle) {
      e.last.focus();
    }
  }
}

// *** Event Listener Functions ***
function sidebarEventListenerCheckClick (event) {
  sidebarCheckClick(event);
}
function sidebarEventListenerCheckKey (event) {
  sidebarCheckKeys(event);
}

// *** Immediate Functions ***
// Check for Current Page and update Sidebar Links
(function () {
  let loc = window.location.pathname;
  let links = document.getElementById('sidebar').getElementsByClassName('link');

  for (i = 0; i < links.length; i++) {
    function updateLink (state) {
      links[i].setAttribute('data-selected', state);
      links[i].setAttribute('aria-selected', state);
    }

    if (links[i].pathname == loc) {
      updateLink(true);
    }
    else {
      updateLink(false);
    }
  }
})();
// Update Badges
(function () {
    newAjaxRequest('GET', '/assets/php/scripts/shift/getAlerts.php', function (request) {
      let alerts = JSON.parse(request).response.alerts;
      let links = document.getElementById('sidebar').getElementsByClassName('link');
      let template = document.getElementById('sidebar_template_badges').content.children;

      for (i = 0; i < links.length; i++) {
        let badgeID = links[i].getAttribute('data-useBadges');

        if (badgeID !== null) {
          if (alerts.new[badgeID] > 0 || alerts.expiring[badgeID] > 0) {
            let link = document.getElementById('sidebar_link_' + badgeID);
            let badges = {};
              (function () {
                badges.base = template[0].cloneNode(true);
                badges.new = template[1].cloneNode(true);
                badges.exp = template[2].cloneNode(true);
              })();
            let badgeBase;

            link.appendChild(badges.base);
            badgeBase = link.getElementsByClassName('badges')[0];

            if (alerts.new[badgeID] > 0)      { badgeBase.appendChild(badges.new); }
            if (alerts.expiring[badgeID] > 0) { badgeBase.appendChild(badges.exp); }
          }
        }
      }

      document.getElementById('sidebar_template_badges').remove();
    });
})();

// *** Event Listeners ***
document.getElementById('navbar_sb').addEventListener('click', toggleSB);
document.getElementById('sidebar_toggle').addEventListener('click', toggleSB);
window.addEventListener('click', sidebarEventListenerCheckClick);
window.addEventListener('keydown', sidebarEventListenerCheckKey);
