function stepChange (newVal, firstRun = false) {
  let base = getClass(document.body, 'instructions');
  let scroller = getClass(getClass(base, 'steps'), 'scroller');

  scroller.style.transform = `translateX(-${(newVal * 100) - 100}%)`;

  // Handle links
  for (let link of getElements(scroller, 'focusables')) {
    let parent = findClass(link, 'up', 'step');

    if (tryParseInt(parent.className.match('\\d+')[0]) == newVal) {
      disenable(link, false, true);
    }
    else {
      disenable(link, true, true);
    }
  }

  // Load images
  for (let step of [newVal, newVal++, newVal--]) {
    if (step > 0 && step <= scroller.childNodes.length) {
      parseWebpImages(getClass(base, step));
    }
  }
}

// Initial Setup
(function () {
  let t = setInterval(function () {
    if (globalFunctionsReady) {
      let base = getClass(document.body, 'instructions');
      let setup = getClass(document.body, 'setup');
      let steps = getClasses(setup, 'step');
      let scroller = getClass(base, 'scroller');
      let game = window.location.pathname.replace('/help/how-to-redeem/', '');

      clearInterval(t);

      // Setup steps
      (function () {
        for (let step of steps) {
          let stepB;
            tryToRun({
              function: function () {
                try {
                  stepB = getTemplate('how_to_redeem_step_template');

                  if (stepB) {
                    return true;
                  }
                }
                catch (e) {
                  return false;
                }
              }
            });
          let e = {};
            (function () {
              for (let c of ['img-container', 'img', 'title', 'description']) {
                e[c] = getClass(stepB, c);
              }
            })();
          let stepNum = step.className.match('\\d+')[0];
          let title = `Step ${stepNum}`;
          let description = step.innerHTML;
          let webp = tryJSONParse(e.img.getAttribute('data-webp'));

          addClass(stepB, stepNum);
          e.title.innerHTML = title;
          e.description.innerHTML = description;

          webp.path += `${game}/${stepNum}`;
          e.img.setAttribute('data-webp', JSON.stringify(webp));
          e.img.setAttribute('data-fullscreen', title);
          e.img.alt = `Image for ${title}`;

          scroller.appendChild(stepB);
        }

        setup.parentNode.removeChild(setup);
      })();
      // Setup pager
      (function () {
        let pager = document.getElementById('instructions_pager');

        pager.setAttribute('data-max', steps.length);
        pager = configurePager(pager);

        for (let button of getTags(pager, 'button')) {
          button.addEventListener('click', function (e) {
            let val = tryParseInt(this.getAttribute('data-value'));

            if (!(this.getAttribute('aria-pressed') == 'true')) {
              stepChange(val);
            }
          });
        }
      })();
      // Hash Listener
      addHashListener('step_', function (hash) {
        let val = tryParseInt(hash.replace('#step_', ''));

        pagerUpdate(document.getElementById('instructions_pager'), val);
        stepChange(val);
      });
      // Startup
      if (window.location.hash.search('#step_') != 0) {
        stepChange(1);
      }
    }
  }, 250);
})();
