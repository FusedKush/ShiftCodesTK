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
    focusLock.clear()
    setTimeout(updateVis, 300);
  }
  else {
    updateVis();
    focusLock.set(getClass(sb, 'panel'), toggleSB);
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
    link.getElementsByClassName('name')[0].id = id + ('_name');

    if (link.getAttribute('aria-label') === null) {
      link.setAttribute('aria-labelledby', id + ('_name'));
    }
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
(function () {
  let t;

  t = setInterval(function () {
    if (tryToRun) {
      clearInterval(t);
      tryToRun({
        attempts: false,
        delay: 500,
        function: function () {
          if (shiftStats) {
            let links = getClasses(document.getElementById('sidebar'), 'use-badge');
            let template = document.getElementById('sidebar_template_badges').content.children;

            for (i = 0; i < links.length; i++) {
              let badgeID = links[i].pathname.slice(1);
              let n = shiftStats.new[badgeID];
              let e = shiftStats.expiring[badgeID];

              if (n > 0 || e > 0) {
                let link = links[i];
                let badges = {};
                  (function () {
                    badges.base = copyElm(template[0]);
                    badges.new = copyElm(template[1]);
                    badges.exp = copyElm(template[2]);
                  })();
                let badgeBase;

                link.appendChild(badges.base);
                badgeBase = link.getElementsByClassName('badges')[0];

                if (n > 0) { badgeBase.appendChild(badges.new); }
                if (e > 0) { badgeBase.appendChild(badges.exp); }
              }
            }

            document.getElementById('sidebar_template_badges').remove();
            return true;
          }
          else {
            return false;
          }
        }
      });
    }
  }, 500);
})();

// *** Event Listeners ***
document.getElementById('navbar_sb').addEventListener('click', toggleSB);
document.getElementById('sidebar_toggle').addEventListener('click', toggleSB);
