/*********************************
  Navbar Scripts
*********************************/

// *** Functions ***
// Updates the appearance of the Navbar
function updNav () {
  let pos = [
    window.pageYOffset,
    document.documentElement.scrollTop,
    document.body.scrollTop
  ]
  let nav = document.getElementById("navbar");
  let themeColor = {
    'setting': document.getElementById('theme_color'),
    'background': document.getElementById('theme_color_bg').content,
    'theme': document.getElementById('theme_color_tm').content
  };

  if(pos[1] > 0 || pos[2] > 0 || pos[3] > 0) {
    nav.setAttribute("data-atTop", "false");
    themeColor.setting.content = themeColor.theme;
  }
  else {
    nav.setAttribute("data-atTop", "true");
    themeColor.setting.content = themeColor.background;
  }
};

// *** Immediate Functions ***
// Immediately update the position of the Navbar
updNav();

// *** Event Listeners ***
// Watch for Scrolling
window.addEventListener("scroll", updNav);
