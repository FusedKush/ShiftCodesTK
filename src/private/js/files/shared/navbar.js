// *** Functions ***
// Updates the appearance of the Navbar
function updNav () {
  // let pos = [
  //   window.pageYOffset,
  //   document.documentElement.scrollTop,
  //   document.body.scrollTop
  // ];
  let pos = ShiftCodesTK.client.scroll;
  let nav = document.getElementById("navbar");
  let themeColor = {
    'setting': getMetaTag('theme-color'),
    'background': getMetaTag('tk-bg-color').content,
    'theme': getMetaTag('tk-theme-color').content
  };

  if(pos > 2) {
    nav.setAttribute("data-at-top", false);
    themeColor.setting.content = themeColor.theme;
  }
  else if (!dom.has(document.body, 'class', 'scroll-disabled')) {
    nav.setAttribute("data-at-top", true);
    themeColor.setting.content = themeColor.background;
  }
};
// Update Loader Progress Bar
function lpbUpdate (progress, interval = false, additionalOptions = {}) {
  let settings = {
    duration: 400,
    buffer: 100
  };
      settings.total = settings.duration + settings.buffer;
  let options = mergeObj(additionalOptions, { interval: interval, resetOnZero: true });
  let pb = document.getElementById('loader_pb');
  let now = tryParseInt(pb.getAttribute('data-progress'), 'ignore');

  function run() {
    function update (progressVal = progress) {
      updateProgressBar(pb, progressVal, options);
    }

    // Reset for start
    if (!dom.has(pb, 'class', 'is-loading')) {
      edit.class(pb, 'add', 'is-loading');
      update();
    }
    else {
      update();
    }
    // Reset when complete
    if (progress == 100) {
      setTimeout(function () {
        edit.class(pb, 'remove', 'is-loading');

        setTimeout(function () {
          update(0);
        }, settings.total);
      }, settings.total);
    }
  }

  if (progress > now || now == 0) {
    if (now != 0) {
      setTimeout(run, settings.buffer);
    }
    else {
      run();
    }
  }
}

// *** Immediate Functions ***
(function () {
  let interval = setInterval(function () {
    if (globalFunctionsReady && typeof ShiftCodesTK.client !== 'undefined') {
      debugger;
      clearInterval(interval);

      // Update the position of the Navbar
      updNav();

      // *** Event Listeners ***
      // Watch for Scrolling
      window.addEventListener("scroll", updNav);

      // Setup Layers 
      // (function () {
      //   const layers = dom.find.children(dom.find.id("navbar"), 'class', 'layer');

      //   for (let layer of layers) {
      //     if (!dom.has(layer, 'class', 'configured')) {
      //       ShiftCodesTK.layers.setupLayer(layer);
      //     }
      //   }
      // })();

      // Site Settings
      // ShiftCodesTK.forms.registerHook('/assets/requests/post/js/update-site-settings', 'afterSubmit', function (form, formData, formProps, response) {
      //   ShiftCodesTK.toasts.newToast({
      //     settings: {
      //       id: 'site_settings_updated_toast',
      //       duration: 1500
      //     },
      //     content: {
      //       title: 'Site Settings Updated!',
      //       icon: 'fas fa-cogs'
      //     }
      //   });
      // });
      
    }
  }, 250);
})();
