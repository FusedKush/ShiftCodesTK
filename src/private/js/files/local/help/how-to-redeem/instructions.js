function stepChange (newVal, firstRun = false) {
  let base = dom.find.child(document.body, 'class', 'instructions');
  let scroller = dom.find.child(dom.find.child(base, 'class', 'steps'), 'class', 'scroller');

  scroller.style.transform = `translateX(-${(newVal * 100) - 100}%)`;

  // Handle links
  for (let link of dom.find.children(scroller, 'group', 'focusables')) {
    let parent = dom.find.parent(link, 'class', 'step');

    if (tryParseInt(parent.className.match('\\d+')[0]) == newVal) {
      isDisabled(link, false, true);
    }
    else {
      isDisabled(link, true, true);
    }
  }

  // Load images
  for (let step of [newVal, newVal++, newVal--]) {
    if (step > 0 && step <= scroller.childNodes.length) {
      parseWebpImages(dom.find.child(base, 'class', step));
    }
  }
}

// Initial Setup
(function () {
  let t = setInterval(function () {
    if (globalFunctionsReady) {
      let base = dom.find.child(document.body, 'class', 'instructions');
      let setup = dom.find.child(document.body, 'class', 'setup');
      let steps = dom.find.children(setup, 'class', 'step');
      let scroller = dom.find.child(base, 'class', 'scroller');
      let game = window.location.pathname.replace('/help/how-to-redeem/', '');

      clearInterval(t);

      // Setup steps
      (function () {
        for (let step of steps) {
          let stepB;
            tryToRun({
              function: function () {
                try {
                  stepB = edit.copy(document.getElementById('how_to_redeem_step_template'));

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
                e[c] = dom.find.child(stepB, 'class', c);
              }
            })();
          let stepNum = step.className.match('\\d+')[0];
          let title = `Step ${stepNum}`;
          let description = step.innerHTML;
          let webp = tryJSONParse(e.img.getAttribute('data-webp'));

          edit.class(stepB, 'add', stepNum);
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

        for (let button of dom.find.children(pager, 'tag', 'button')) {
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
