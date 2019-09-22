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
    nav.setAttribute("data-at-top", false);
    themeColor.setting.content = themeColor.theme;
  }
  else {
    nav.setAttribute("data-at-top", true);
    themeColor.setting.content = themeColor.background;
  }
};
// Update Loader Progress Bar
function lpbUpdate (progress, interval = false) {
  let settings = {
    fadeDuration: 300,
    barDuration: 400,
    buffer: 100
  };
  let pb = document.getElementById('loader_pb');

  function update () {
    if (interval === false) {
      updateProgressBar(pb, progress);
    }
    else {
      updateProgressBar(pb, progress, { interval: true });
    }
  }

  // Reset for start
  if (!hasClass(pb, 'is-loading')) {
    addClass(pb, 'is-loading');
    update();
  }
  else {
    update();
  }
  // Reset when complete
  if (progress == 100) {
    setTimeout(function () {
      delClass(pb, 'is-loading');

      setTimeout(function () {
        updateProgressBar(pb, 0);
      }, (settings.fadeDuration + settings.buffer));
    }, (settings.barDuration + settings.buffer));
  }
}

// *** Immediate Functions ***
// Immediately update the position of the Navbar
updNav();

// *** Event Listeners ***
// Watch for Scrolling
window.addEventListener("scroll", updNav);
