// Toggle the Sidebar
function toggleSB () {
  let sb = document.getElementById('sidebar');
  let btn = {
    'nav': document.getElementById('navbar_sb'),
    'sb': document.getElementById('sidebar_toggle')
  };
  let state = sb.getAttribute('aria-expanded') == 'true';

  // Updates the Visibility of the Sidebar
  function updateVis () {
    isHidden(sb, state);
  }
  // Update Sidebar and Button states
  function updateState () {
    sb.setAttribute('aria-expanded', !state);
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
    toggleBodyScroll(true);
    focusLock.clear();
    setTimeout(updateVis, 300);
  }
  else {
    updateVis();
    toggleBodyScroll(false);
    focusLock.set(dom.find.child(sb, 'class', 'panel'), toggleSB);
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
// sidebarMarkup();
// Check for Current Page and update Sidebar Links
(function () {
  let regex = new RegExp('\\/$', 'g');
  let loc = window.location.pathname.replace(regex, '');
  let links = document.getElementById('sidebar').getElementsByClassName('link');

  // for (i = 0; i < links.length; i++) {
  //   function updateLink (state) {
  //     links[i].setAttribute('data-selected', state);
  //     links[i].setAttribute('aria-selected', state);
  //   }

  //   if (links[i].getAttribute('href').replace(regex, '') == loc) {
  //     updateLink(true);
  //   }
  //   else {
  //     updateLink(false);
  //   }
  // }
})();
// *** Event Listeners ***
document.getElementById('navbar_sb').addEventListener('click', toggleSB);
document.getElementById('sidebar_toggle').addEventListener('click', toggleSB);
