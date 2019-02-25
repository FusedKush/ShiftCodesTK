/*********************************
  SHiFT Scripts
*********************************/

// *** Variables ***
let shiftData = {};
  (function () {
    shiftData.base = JSON.parse(document.body.getAttribute('data-shiftData'));
    shiftData.id = shiftData.base.id;
    shiftData.name = shiftData.base.name;
  })();

// *** Functions ***
// Toggle Panel Sort
function toggleSort() {
  let feed = {}; // Panel Feed
    (function () {
      feed.base = document.getElementById('panel_feed');
      feed.content = feed.base.children;
      feed.count = feed.content.length;
    })();
  let sort = {}; // Sort Button
    (function () {
      sort.button = document.getElementById('shift_header_sort');
      sort.icon = sort.button.getElementsByClassName('fas')[0];
    })();
  let strings = {
    'states': {
      true: 'default',
      false: 'flipped'
    },
    'icons': {
      true: 'fa-sort-amount-down',
      false: 'fa-sort-amount-up'
    },
    'labels': {
      true: 'Sorted by Newest Codes First. Click to change sort.',
      false: 'Sorted by Oldest Codes First. Click to change sort.'
    }
  };
  let currentSort = feed.base.getAttribute('data-sort') == 'default';
  let x = -1;
  let panelCache = [];
  let panelState;

  function clonePanel (pos) {
    panelCache.push(feed.content[pos].cloneNode(true));
    feed.content[pos].remove();
  }
  function addPanel (i) {
    feed.base.appendChild(panelCache[i]);
    x++;
    panelState = feed.content[x].getAttribute('data-expanded') == 'true';
    updatePanelTiming(feed.content[x], (x + 1));
    updatePanelListeners(feed.content[x], 'setup-copyOnly');

    if (panelState === false) {
      updatePanelListeners(feed.content[x], 'collapse');
    }
    else {
      updatePanelListeners(feed.content[x], 'expand');
    }
  }

  feed.base.setAttribute('data-sort', strings.states[!currentSort]);
  sort.button.title = strings.labels[!currentSort];
  sort.button.setAttribute('aria-label', strings.labels[!currentSort]);
  sort.button.classList.remove('animated');

  setTimeout(function () {
    sort.button.classList.add('animated');
    sort.icon.classList.remove(strings.icons[currentSort]);
    sort.icon.classList.add(strings.icons[!currentSort]);
  }, 1);

  if (strings.states[currentSort] == 'default') {
    for (i = 0; i < feed.count; i++) {
      clonePanel(0);
    }
    for (let i = panelCache.length; i > 0; i--) {
      addPanel(i - 1);
    }
  }
  else {
    for (i = feed.count; i > 0; i--) {
      clonePanel(i - 1);
    }
    for (let i = 0; i < panelCache.length; i++) {
      addPanel(i);
    }
  }
}
// Toggles SHiFT Code Panels
function togglePanel (type, event) {
  let panel = (function () {
    // Return Panel Element based on Event Listener Triggered
    if (type == 'open') {
      return event.currentTarget;
    }
    else if (type == 'close') {
      return event.currentTarget.parentNode.parentNode.parentNode;
    }
  })();
  let e = {}; // Panel Elements
    (function () {
      e.collapse = panel.getElementsByClassName('header')[0].getElementsByClassName('top')[0].getElementsByClassName('collapse')[0];
      e.body = panel.getElementsByClassName('body')[0];
        e.body.link = e.body.getElementsByClassName('src')[0].getElementsByClassName('content')[0].getElementsByTagName('a')[0];
        e.body.copyPC = e.body.getElementsByClassName('pc')[0].getElementsByClassName('content')[0].getElementsByClassName('copy')[0];
        e.body.copyXbox = e.body.getElementsByClassName('xbox')[0].getElementsByClassName('content')[0].getElementsByClassName('copy')[0];
        e.body.copyPS = e.body.getElementsByClassName('ps')[0].getElementsByClassName('content')[0].getElementsByClassName('copy')[0];
      })();
  let state = panel.getAttribute('data-expanded') == 'true';

  if (type == 'open') {
    panel.removeAttribute('title');
    panel.removeAttribute('aria-label');
    panel.removeAttribute('role');
    panel.removeAttribute('tabindex');
  }
  else if (type == 'close') {
    panel.title = 'SHiFT Code for ' + shiftData.name;
    panel.setAttribute('aria-label', 'SHiFT Code for ' + shiftData.name);
    panel.role = 'button';
    panel.tabIndex = '0';
  }

  disenable(e.body.link, state, true);
  disenable(e.body.copyPC, state);
  disenable(e.body.copyXbox, state);
  disenable(e.body.copyPS, state);
  disenable(e.collapse, state);
  vishidden(e.collapse, state);
  panel.setAttribute('data-expanded', !state);
  panel.setAttribute('aria-expanded', !state);

  if (type == 'open') {
    updatePanelListeners(panel, 'expand');
    e.collapse.focus();
  }
  else if (type == 'close') {
    updatePanelListeners(panel, 'collapse');
    panel.focus();
  }
}
// Copies the SHiFT Code to Clipboard
function copyCode (event) {
  event.parentNode.getElementsByClassName('value')[0].select();
  document.execCommand('copy');
  event.classList.remove('animated');

  setTimeout(function () {
    event.classList.add('animated');
  }, 1);
}
// Update Panel Animation Timing
function updatePanelTiming (panel, id) {
  panel.style.animationDelay = ((id - 1) * 0.2) + 's';
}
// Updates SHiFT Code Panel Event Listeners
function updatePanelListeners (panel, type) {
  let collapse = panel.getElementsByClassName('collapse')[0];
  let copy = panel.getElementsByClassName('copy');

  function panelListeners (action) {
    if (action == 'add') {
      panel.addEventListener('click', shiftEventListenerTogglePanelOpen);
      panel.addEventListener('keydown', shiftEventListenerTogglePanelOpenKey);
    }
    else if (action == 'remove') {
      panel.removeEventListener('click', shiftEventListenerTogglePanelOpen);
      panel.removeEventListener('keydown', shiftEventListenerTogglePanelOpenKey);
    }
  }
  function collapseListeners (action) {
    if (action == 'add') {
      collapse.addEventListener('click', shiftEventListenerTogglePanelClose);
    }
    else if (action == 'remove') {
      collapse.removeEventListener('click', shiftEventListenerTogglePanelClose);
    }
  }
  function copyListeners () {
    for (i = 0; i < copy.length; i++) {
      copy[i].addEventListener('click', function (e) { copyCode(this); });
    }
  }

  if (type == 'setup') {
    panelListeners('add');
    copyListeners();
  }
  else if (type == 'setup-copyOnly') {
    copyListeners();
  }
  else if (type == 'expand') {
    panelListeners('remove');

    setTimeout (function () {
      collapseListeners('add');
    }, 1);
  }
  else if (type == 'collapse') {
    collapseListeners('remove');

    setTimeout (function () {
      panelListeners('add');
    }, 1);
  }
}

// *** Event Listener Reference Functions ***
function shiftEventListenerTogglePanelOpen (event) {
  togglePanel('open', event);
}
function shiftEventListenerTogglePanelOpenKey (event) {
  if (event.key == 'Enter') {
    event.preventDefault();
    togglePanel('open', event);
  }
}
function shiftEventListenerTogglePanelClose (event) {
  togglePanel('close', event);
}

// *** Immediate Functions ***
// Updates Page Content
(function () {
  let id = shiftData.id;
  let name = shiftData.name;
  let template = {};
    (function () {
      template.base = document.getElementById('panel_template');
      template.panel = template.base.content.children[0];
      template.pcTitle = template.panel.getElementsByClassName('pc')[0].getElementsByClassName('title')[0];
      template.xboxTitle = template.panel.getElementsByClassName('xbox')[0].getElementsByClassName('title')[0];
      template.psTitle = template.panel.getElementsByClassName('ps')[0].getElementsByClassName('title')[0];
    })();
  let platformStrings = {
    1: {
      'pc': 'PC / Mac:',
      'xbox': 'Xbox 360 / Xbox One:',
      'ps': 'PS3 / PS4 / PS Vita:'
    },
    2: {
      'pc': 'PC / Mac / Linux:',
      'xbox': 'Xbox 360 / Xbox One:',
      'ps': 'PS3 / PS4:'
    }
  };

    template.panel.title = ('SHiFT Code for ') + name;
    template.panel.setAttribute('aria-label', name);
    template.pcTitle.innerHTML = platformStrings[id]['pc'];
    template.xboxTitle.innerHTML = platformStrings[id]['xbox'];
    template.psTitle.innerHTML = platformStrings[id]['ps'];
})();
// Handles Page Construction
(function () {
  let header = document.getElementById('shift_header');
  let feed = document.getElementById('panel_feed');
  let count = {
    'retrieved': 0,
    'total': 0,
    'new': 0,
    'exp': 0
  };

  // Update Counters and their respective elements
  function updateCounter (name) {
    count[name]++;

    let title = (function () {
      let plural = 's';

      if (count[name] == 1) { plural = ''; }

      return ('SHiFT Code') + plural;
    })();
    let elm = document.getElementById(('shift_header_count_') + name);
    let labels = {
      'total': title + (' Available'),
      'new': ('New ') + title,
      'exp': ('Expiring ') + title
    };
    let label = count[name] + (' ') + labels[name];

    elm.title = label;
    elm.setAttribute('aria-label', label);
    elm.getElementsByClassName('count')[0].innerHTML = count[name];

    if (count[name] == 1) { elm.classList.remove('inactive'); }
  }
  // Construct the SHiFT Code Panel and add it to the feed
  function constructPanel (codeObject) {
    let panel = {}; // SHiFT Code Panel Elements
      (function () {
        panel.base = document.getElementById('panel_template').content.children[0].cloneNode(true);
          panel.flags = {};
            panel.flags.new = panel.base.getElementsByClassName('flag new')[0];
            panel.flags.exp = panel.base.getElementsByClassName('flag exp')[0];
          panel.header = panel.base.getElementsByClassName('header')[0];
            panel.title = panel.header.getElementsByClassName('top')[0].getElementsByClassName('title')[0];
              panel.reward = panel.title.getElementsByClassName('reward')[0];
              panel.description = panel.title.getElementsByClassName('description')[0];
            panel.progress = panel.header.getElementsByClassName('bottom')[0].getElementsByClassName('progress-bar')[0];
              panel.progressBar = panel.progress.getElementsByClassName('progress')[0];
          panel.body = panel.base.getElementsByClassName('body')[0];
            panel.relDate = panel.body.getElementsByClassName('rel')[0].getElementsByClassName('content')[0];
            panel.expDate = panel.body.getElementsByClassName('exp')[0].getElementsByClassName('content')[0];
            panel.source = panel.body.getElementsByClassName('src')[0].getElementsByClassName('content')[0].firstChild;
            panel.codePC = {};
                  panel.codePC.base = panel.body.getElementsByClassName('pc')[0].getElementsByClassName('content')[0];
                  panel.codePC.display = panel.codePC.base.getElementsByClassName('display')[0];
                  panel.codePC.value = panel.codePC.base.getElementsByClassName('value')[0];
                  panel.codePC.copy = panel.codePC.base.getElementsByClassName('copy')[0];
            panel.codeXbox = {};
                  panel.codeXbox.base = panel.body.getElementsByClassName('xbox')[0].getElementsByClassName('content')[0];
                  panel.codeXbox.display = panel.codeXbox.base.getElementsByClassName('display')[0];
                  panel.codeXbox.value = panel.codeXbox.base.getElementsByClassName('value')[0];
                  panel.codeXbox.copy = panel.codeXbox.base.getElementsByClassName('copy')[0];
            panel.codePS = {};
                  panel.codePS.base = panel.body.getElementsByClassName('ps')[0].getElementsByClassName('content')[0];
                  panel.codePS.display = panel.codePS.base.getElementsByClassName('display')[0];
                  panel.codePS.value = panel.codePS.base.getElementsByClassName('value')[0];
                  panel.codePS.copy = panel.codePS.base.getElementsByClassName('copy')[0];
      })();
    let currentDate = getDate('yyyy-mm-dd', '-');

    // Handle Panel Properties
    (function () {
      // Panel ID
      panel.base.id = ('panel_code_') + codeObject.codeID;
    })();
    // Handle Header Properties
    (function () {
      // Reward
      (function () {
        let reward = codeObject.reward;

        panel.reward.innerHTML = reward;

        if (reward.length > 20) {
          panel.description.classList.add('long');
        }
        if (reward != '5 Golden Keys') {
          panel.description.innerHTML = 'Rare SHiFT Code';
        }
      })();
      // Flags & Dates
      (function () {
        function convertDate (date) {
          let y = date.substring(0, 4);
          let md = date.substring(5);

          return md + ('-') + y;
        }

        let relDate = convertDate(codeObject.relDate);
        let expDate = (function () {
          let exp = codeObject.expDate;

          if (exp === null) {
            panel.expDate.classList.add('inactive');
            return 'N/A';
          }
          else {
            return convertDate(exp);
          }
        })();

        panel.relDate.innerHTML = relDate;
        panel.expDate.innerHTML = expDate;

        if (codeObject.relDate == currentDate)  { panel.base.classList.add('new'); }
        else                                    { panel.flags.new.remove(); }
        if (codeObject.expDate == currentDate)  { panel.base.classList.add('exp'); }
        else                                    { panel.flags.exp.remove(); }
      })();
      // Progress Bar
      (function () {
        let rel = codeObject.relDate;
        let exp = codeObject.expDate;

        function getDifference (start, end) {
          function parse (date) {
            let str = date.replace(/-/g, ', ');
            let obj = new Date(str);

            obj.setMonth(obj.getMonth() - 1);
            obj.setHours(0, 0, 0);

            return obj.getTime();
          }

          let base = (24 * 60 * 60 * 1000);
          let startDate = parse(start);
          let endDate = parse(end);

          return Math.round(Math.abs((startDate - endDate) / base));
        }

        if (exp !== null) {
          let width = (function () {
            let base = (getDifference(currentDate, rel) / getDifference(exp, rel) * 100).toString();
            let result;

            if (base.indexOf('.') != -1)  { return base.match(/\d{1,2}(?=\.)/)[0]; }
            else                          { return base; }
          })();
          let left = (function () {
            let time = getDifference(currentDate, exp);
            let string = (function () {
              let plural = '';

              if (time != 1) { plural = 's'; }

              return (' Day') + plural + (' Left');
            })();

            return time + string;
          })();

          panel.progress.title = left;
          panel.progress.setAttribute('aria-label', left);
          panel.progress.setAttribute('aria-valuenow', width);
          panel.progressBar.style.width = width + ('%');
        }
        else { panel.progressBar.parentNode.parentNode.remove(); }
      })();
      // Source
      (function () {
        let source = codeObject.source;
        let label = (function () {
          let str = 'Source (';

          if (source.indexOf('facebook') != -1)     { str += 'Facebook'; }
          else if (source.indexOf('twitter') != -1) { str += 'Twitter'; }

          return str += ')';
        })();

        panel.source.href = source;
        panel.source.title = label;
        panel.source.setAttribute('aria-label', label);
        panel.source.getElementsByClassName('text')[0].innerHTML = source;
      })();
    })();
    // Handle Body Properties
    (function () {
      let fields = ['codePC', 'codeXbox', 'codePS'];

      for (i = 0; i < fields.length; i++) {
        let elm = panel[fields[i]];
        let entry = codeObject[fields[i]];

        elm.display.innerHTML = entry;
        elm.value.value = entry;
      }
    })();

    // Update Panel Event Listeners
    updatePanelListeners(panel.base, 'setup');

    // Add panel to feed
    (function () {
      let overlay = document.getElementById('shift_overlay');

      feed.appendChild(panel.base);
      updateCounter('total');
      updatePanelTiming(panel.base, count.total);

      if (codeObject.relDate == currentDate)      { updateCounter('new'); }
      else if (codeObject.expDate == currentDate) { updateCounter('exp'); }
      if (count.total == 1) { vishidden(overlay, true); }
      if (count.total == count.retrieved) {
        disenable(document.getElementById('shift_header_sort'), false);
        overlay.remove();
        document.getElementById('panel_template').remove();
      }
    })();
  }

  // Retrieve SHiFT Codes and add them to the page
  (function () {
    // Wait for dependencies
    function executeWhenReady() {
      if (typeof newAjaxRequest == 'function' && typeof getDate == 'function') {
        // Fetch SHiFT Codes
        newAjaxRequest('GET', ('assets/php/scripts/shift/retrieveCodes.php?gameID=') + shiftData.id, function (response) {
          let retrievedCodes = JSON.parse(response).response;
              count.retrieved = retrievedCodes.length;

          // Start processing
          if (count.retrieved > 0) {
            for (let i = 0; i < count.retrieved; i++) {
              // Construct the panel for the SHiFT Code
              constructPanel(retrievedCodes[i]);
            }
          }
          // Show error message
          else {
            let overlay = document.getElementById('shift_overlay');

            vishidden(overlay.getElementsByClassName('spinner')[0], true);
            vishidden(overlay.getElementsByClassName('error')[0], false);
          }
        });
      }
      else { setTimeout(function () { executeWhenReady(); }, 100); }
    }

    executeWhenReady();
  })();
})();

// *** Event Listeners ***
document.getElementById('shift_header_sort').addEventListener('click', toggleSort);
