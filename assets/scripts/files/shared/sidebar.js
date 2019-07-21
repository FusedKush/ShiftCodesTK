/*********************************
  Sidebar Scripts
*********************************/
// *** Variables ***
var addSidebarBadgesRetry;

// *** Functions ***
// Update Badges
function addSidebarBadges () {
  if (typeof shiftBadgeCount != 'undefined') {
    let links = document.getElementById('sidebar').getElementsByTagName('a');
    let template = document.getElementById('sidebar_template_badges').content.children;

    clearInterval(addSidebarBadgesRetry);

    for (i = 0; i < links.length; i++) {
      let badgeID = links[i].getAttribute('data-use-badges');

      if (badgeID !== null) {
        if (shiftBadgeCount.new[badgeID] > 0 || shiftBadgeCount.expiring[badgeID] > 0) {
          let link = links[i];
          let badges = {};
            (function () {
              badges.base = template[0].cloneNode(true);
              badges.new = template[1].cloneNode(true);
              badges.exp = template[2].cloneNode(true);
            })();
          let badgeBase;

          link.appendChild(badges.base);
          badgeBase = link.getElementsByClassName('badges')[0];

          if (shiftBadgeCount.new[badgeID] > 0)      { badgeBase.appendChild(badges.new); }
          if (shiftBadgeCount.expiring[badgeID] > 0) { badgeBase.appendChild(badges.exp); }
        }
      }
    }

    document.getElementById('sidebar_template_badges').remove();
  }
};
// Toggle the Sidebar
function toggleSB () {
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

    if (state === true) {
      btn.nav.focus();
    }
    else if (state === false) {
      btn.sb.focus();
    }
  }

  if (state === true) {
    updateState();
    focusLockedElement = null;
    setTimeout(updateVis, 300);
  }
  else {
    updateVis();
    focusLockedElement = {
      element: getClass(sb, 'panel'),
      callback: toggleSB
    };
    setTimeout(updateState, 50);
  }
}
// Updates the markup of the sidebar
function sidebarMarkup () {
  let sidebar = document.getElementById('sidebar');
  let li = sidebar.getElementsByTagName('li');
  let links = sidebar.getElementsByTagName('a');

  for (i = 0; i < li.length; i++) {
    let link = li[i].getElementsByTagName('a')[0];
    let id = ('sidebar_link_') + (i + 1);

    li[i].setAttribute('role', 'menuitem');
    link.classList.add('link');
    link.id = id;
    link.setAttribute('aria-labelledby', id + ('_name'));
    link.getElementsByClassName('name')[0].id = id + ('_name');
  }
  for (i = 0; i < links.length; i++) {
    links[i].classList.add('no-focus-scroll');
  }
}

// *** Immediate Functions ***
// Add required markup to sidebar entries
sidebarMarkup();
// Check for Current Page and update Sidebar Links
(function () {
  let regex = new RegExp('\\/$', 'g');
  let loc = window.location.pathname.replace(regex, '');
  let links = document.getElementById('sidebar').getElementsByClassName('link');

  for (i = 0; i < links.length; i++) {
    function updateLink (state) {
      links[i].setAttribute('data-selected', state);
      links[i].setAttribute('aria-selected', state);
    }

    if (links[i].getAttribute('href').replace(regex, '') == loc) {
      updateLink(true);
    }
    else {
      updateLink(false);
    }
  }
})();
// Update sidebar badges
 addSidebarBadgesRetry = setInterval(addSidebarBadges, 250);

// *** Event Listeners ***
document.getElementById('navbar_sb').addEventListener('click', toggleSB);
document.getElementById('sidebar_toggle').addEventListener('click', toggleSB);
