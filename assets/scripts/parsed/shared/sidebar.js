function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

// Toggle the Sidebar
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
    toggleBodyScroll(true);
    focusLock.clear();
    setTimeout(updateVis, 300);
  } else {
    updateVis();
    toggleBodyScroll(false);
    focusLock.set(getClass(sb, 'panel'), toggleSB);
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


(function () {
  var t;
  t = setInterval(function () {
    if (tryToRun) {
      clearInterval(t);
      tryToRun({
        attempts: false,
        delay: 500,
        "function": function _function() {
          if (shiftStats) {
            var _ret = function () {
              var links = getClasses(document.getElementById('sidebar'), 'use-badge');
              var template = document.getElementById('sidebar_template_badges').content.children;

              for (i = 0; i < links.length; i++) {
                var badgeID = links[i].pathname.slice(1);
                var n = shiftStats["new"][badgeID];
                var e = shiftStats.expiring[badgeID];

                if (n > 0 || e > 0) {
                  (function () {
                    var link = links[i];
                    var badges = {};

                    (function () {
                      badges.base = copyElm(template[0]);
                      badges["new"] = copyElm(template[1]);
                      badges.exp = copyElm(template[2]);
                    })();

                    var badgeBase = void 0;
                    link.appendChild(badges.base);
                    badgeBase = link.getElementsByClassName('badges')[0];

                    if (n > 0) {
                      badgeBase.appendChild(badges["new"]);
                    }

                    if (e > 0) {
                      badgeBase.appendChild(badges.exp);
                    }
                  })();
                }
              }

              document.getElementById('sidebar_template_badges').remove();
              return {
                v: true
              };
            }();

            if (_typeof(_ret) === "object") return _ret.v;
          } else {
            return false;
          }
        }
      });
    }
  }, 500);
})(); // *** Event Listeners ***


document.getElementById('navbar_sb').addEventListener('click', toggleSB);
document.getElementById('sidebar_toggle').addEventListener('click', toggleSB);