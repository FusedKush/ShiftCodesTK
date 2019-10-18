// *** Functions ***
// Updates the appearance of the Navbar
function updNav() {
  var pos = [window.pageYOffset, document.documentElement.scrollTop, document.body.scrollTop];
  var nav = document.getElementById("navbar");
  var themeColor = {
    'setting': document.getElementById('theme_color'),
    'background': document.getElementById('theme_color_bg').content,
    'theme': document.getElementById('theme_color_tm').content
  };

  if (pos[1] > 0 || pos[2] > 0 || pos[3] > 0) {
    nav.setAttribute("data-at-top", false);
    themeColor.setting.content = themeColor.theme;
  } else {
    nav.setAttribute("data-at-top", true);
    themeColor.setting.content = themeColor.background;
  }
}

; // Update Loader Progress Bar

function lpbUpdate(progress) {
  var interval = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var additionalOptions = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var settings = {
    duration: 400,
    buffer: 100
  };
  settings.total = settings.duration + settings.buffer;
  var options = mergeObj(additionalOptions, {
    interval: interval,
    resetOnZero: true
  });
  var pb = document.getElementById('loader_pb');
  var now = tryParseInt(pb.getAttribute('data-progress'), 'ignore');

  function run() {
    function update() {
      var progressVal = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : progress;
      updateProgressBar(pb, progressVal, options);
    } // Reset for start


    if (!hasClass(pb, 'is-loading')) {
      addClass(pb, 'is-loading');
      update();
    } else {
      update();
    } // Reset when complete


    if (progress == 100) {
      setTimeout(function () {
        delClass(pb, 'is-loading');
        setTimeout(function () {
          update(0);
        }, settings.total);
      }, settings.total);
    }
  }

  if (progress > now || now == 0) {
    if (now != 0) {
      setTimeout(run, settings.buffer);
    } else {
      run();
    }
  }
} // *** Immediate Functions ***
// Immediately update the position of the Navbar


updNav(); // *** Event Listeners ***
// Watch for Scrolling

window.addEventListener("scroll", updNav);