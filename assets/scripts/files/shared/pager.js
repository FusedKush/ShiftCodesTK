// Pager scripts
var pagers = {};

function pagerState (pager, isDisabled) {
  let buttons = getTags(pager, 'button');

  for (let button of buttons) {
    if (!hasClass(button, 'unavailable')) {
      disenable(button, isDisabled);
    }
  }
}
function pagerUpdate (pager, newPage = 1, firstRun = false) {
  let props = pagers[pager.id];
  let direction = (function () {
    if (newPage > props.now) { return 'back'; }
    else                     { return 'forward'; }
  })();

  function toggleState(button, state) {
    let classname = 'unavailable';

    if (!state && hasClass(button, classname) || state) {
      disenable(button, state);
    }
    if (state) {
      addClass(button, classname);
    }
    else {
      delClass(button, classname);
    }
  }
  function update(button, val, jump = false) {
    let negativeOffset = (function () {
      if (props.subtractOffset) { return props.offset; }
      else                      { return 0; }
    })();

    button.setAttribute('data-page', val);
    button.setAttribute('data-value', ((val * props.offset) - negativeOffset));

    if (jump) {
      let regex = new RegExp('\\d+');

      updateLabel(button, button.title.replace(regex, val));
      button.childNodes[0].innerHTML = val;
    }
  }

  // Onclick
  (function () {
    let p = props.onclick;


  })();
  // Previous
  (function () {
    let button = getClass(pager, 'previous');
    let newVal = newPage - 1;

    if (newVal >= props.min) {
      update(button, newVal);
      toggleState(button, false);
    }
    else {
      update(button, props.min);
      toggleState(button, true);
    }
  })();
  // Next
  (function () {
    let button = getClass(pager, 'next');
    let newVal = newPage + 1;

    if (newVal <= props.max) {
      update(button, newVal);
      toggleState(button, false);
    }
    else {
      update(button, props.max);
      toggleState(button, true);
    }
  })();
  // Jumps
  (function () {
    let jumps = getClasses(pager, 'jump');

    function updateJumps(start, end) {
      let jumpsOffset = Math.floor((end - start) / 2);
      let startVal = (function () {
        let s = newPage - jumpsOffset;
        let e = newPage + jumpsOffset;
        let min = props.min + start;
        let max = props.max - start;

        if (s >= min && e <= max) { return s; }
        else if (s >= min) {
          let val = max - (jumpsOffset * 2);

          if (val > 0) { return val; }
          else         { return 1; }
        }
        else if (e <= max)        { return min; }
      })();
      let updateCount = 0;

      function updatePress (button, state) {
        button.setAttribute('aria-pressed', state);
      }

      for (let i = start; i < end; i++) {
        let jump = jumps[i];

        update(jump, startVal + updateCount, true);
        updateCount++;
      }
      for (let jump of jumps) {
        if (tryParseInt(jump.getAttribute('data-page')) == newPage) {
          updatePress(jump, true);
        }
        else {
          updatePress(jump, false);
        }
      }
    }

    if (jumps.length == 5) {
      if (firstRun) {
        update(jumps[0], props.min, true);
        update(jumps[4], props.max, true);
      }

      updateJumps(1, 4);
    }
    else {
      updateJumps(0, jumps.length);
    }
  })();

  props.now = newPage;
  pagerState(pager, false);
}
function pagerEvent (event) {
  let t = event.currentTarget;
  let pager = findClass(t, 'up', 'pager');
  let val = tryParseInt(t.getAttribute('data-page'));
  let props = pagers[pager.id];

  if (val != props.now) {
    pagerState(pager, true);

    if (props.onclick) {
      tryToRun({
        attempts: 20,
        delay: 250,
        function: function () {
          let target = document.getElementById(props.onclick);

          if (target && !target.disabled) {
            target.focus();
            return true;
          }
          else {
            return false;
          }
        },
        customError: `Could not find focus target for pager "${pager.id}."`
      });
    }

    setTimeout(function() {
      pagerUpdate(pager, val);
    }, 250);
  }
}
function configurePager (pager) {
  let p = getTemplate('pager_template');
  let id = (function () {
    if (pager.id != '') { return pager.id; }
    else                { return `pager_${randomNum(100, 1000)}`; }
  })();

  p.id = id;

  // Store props
  (function () {
    let defaultProps = {}
        defaultProps.now = defaultProps.min = defaultProps.max = defaultProps.offset = 1;
        defaultProps.subtractOffset = false;
        defaultProps.onclick = false;
    let props = Object.keys(defaultProps);

    pagers[id] = {};

    for (let prop of props) {
      let attr = pager.getAttribute(`data-${prop}`);
      let int = tryParseInt(attr, 'ignore');

      if (int) {
        attr = int;
      }
      if (attr) {
        pagers[id][prop] = attr;
      }
      else {
        pagers[id][prop] = defaultProps[prop];
      }
    }
  })();
  // Setup buttons
  (function () {
    let props = pagers[id];

    if (props.max > 1) {
      let copies = (function () {
        if (props.max <= 5) { return props.max; }
        else                { return 5; }
      })();
      let jumps = getClass(getClass(p, 'jumps'), 'content-container');

      for (let i = 2; i <= copies; i++) {
        jumps.appendChild(copyElm(getTag(jumps, 'button')));
      }
      for (let button of getTags(p, 'button')) {
        button.addEventListener('click', pagerEvent);
      }
    }
  })();

  pagerUpdate(p, pagers[id].now, true);
  pager.parentNode.replaceChild(p, pager);
  return p;
}

pagerScripts = setInterval(function () {
  if (globalFunctionsReady) {
    clearInterval(pagerScripts);
    // Configure present Pagers
    (function () {
      let pagers = getClasses(document, 'pager');

      for (let p of pagers) {
        if (!hasClass(p, 'no-auto-config') && !hasClass(p, 'configured')) {
          configurePager(p);
        }
      }
    })();
  }
}, 250);
