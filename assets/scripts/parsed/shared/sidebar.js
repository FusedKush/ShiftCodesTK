/*********************************
  Sidebar Scripts
*********************************/
// *** Variables ***
var addSidebarBadgesRetry; // *** Functions ***
// Update Badges

function addSidebarBadges() {
  if (typeof shiftBadgeCount != 'undefined') {
    (function () {
      var links = document.getElementById('sidebar').getElementsByTagName('a');
      var template = document.getElementById('sidebar_template_badges').content.children;
      clearInterval(addSidebarBadgesRetry);

      for (i = 0; i < links.length; i++) {
        var badgeID = links[i].getAttribute('data-use-badges');

        if (badgeID !== null) {
          if (shiftBadgeCount["new"][badgeID] > 0 || shiftBadgeCount.expiring[badgeID] > 0) {
            (function () {
              var link = links[i];
              var badges = {};

              (function () {
                badges.base = template[0].cloneNode(true);
                badges["new"] = template[1].cloneNode(true);
                badges.exp = template[2].cloneNode(true);
              })();

              var badgeBase = void 0;
              link.appendChild(badges.base);
              badgeBase = link.getElementsByClassName('badges')[0];

              if (shiftBadgeCount["new"][badgeID] > 0) {
                badgeBase.appendChild(badges["new"]);
              }

              if (shiftBadgeCount.expiring[badgeID] > 0) {
                badgeBase.appendChild(badges.exp);
              }
            })();
          }
        }
      }

      document.getElementById('sidebar_template_badges').remove();
    })();
  }
}

; // Toggle the Sidebar

function toggleSB() {
  var sb = document.getElementById('sidebar');
  var btn = {
    'nav': document.getElementById('navbar_sb'),
    'sb': document.getElementById('sidebar_toggle')
  };
  var state = sb.getAttribute('data-expanded') == 'true'; // Updates the Visibility of the Sidebar

  function updateVis() {
    vishidden(sb, state);
  } // Update Sidebar and Button states


  function updateState() {
    sb.setAttribute('data-expanded', !state);
    btn.nav.setAttribute('data-pressed', !state);
    btn.nav.setAttribute('aria-pressed', !state);

    if (state === true) {
      btn.nav.focus();
    } else if (state === false) {
      btn.sb.focus();
    }
  }

  if (state === true) {
    updateState();
    focusLockedElement = null;
    setTimeout(updateVis, 300);
  } else {
    updateVis();
    focusLockedElement = {
      element: getClass(sb, 'panel'),
      callback: toggleSB
    };
    setTimeout(updateState, 50);
  }
} // Updates the markup of the sidebar


function sidebarMarkup() {
  var sidebar = document.getElementById('sidebar');
  var li = sidebar.getElementsByTagName('li');
  var links = sidebar.getElementsByTagName('a');

  for (i = 0; i < li.length; i++) {
    var link = li[i].getElementsByTagName('a')[0];
    var id = 'sidebar_link_' + (i + 1);
    li[i].setAttribute('role', 'menuitem');
    link.classList.add('link');
    link.id = id;
    link.getElementsByClassName('name')[0].id = id + '_name';

    if (link.getAttribute('aria-label') === null) {
      link.setAttribute('aria-labelledby', id + '_name');
    }
  }

  for (i = 0; i < links.length; i++) {
    links[i].classList.add('no-focus-scroll');
  }
} // *** Immediate Functions ***
// Add required markup to sidebar entries


sidebarMarkup(); // Check for Current Page and update Sidebar Links

(function () {
  var regex = new RegExp('\\/$', 'g');
  var loc = window.location.pathname.replace(regex, '');
  var links = document.getElementById('sidebar').getElementsByClassName('link');

  for (i = 0; i < links.length; i++) {
    var updateLink = function updateLink(state) {
      links[i].setAttribute('data-selected', state);
      links[i].setAttribute('aria-selected', state);
    };

    if (links[i].getAttribute('href').replace(regex, '') == loc) {
      updateLink(true);
    } else {
      updateLink(false);
    }
  }
})(); // Update sidebar badges


addSidebarBadgesRetry = setInterval(addSidebarBadges, 250); // *** Event Listeners ***

document.getElementById('navbar_sb').addEventListener('click', toggleSB);
document.getElementById('sidebar_toggle').addEventListener('click', toggleSB);