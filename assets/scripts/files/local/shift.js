/*********************************
  SHiFT Page Scripts
*********************************/

// *** Variables ***
let shiftData = {};
  (function () {
    shiftData.base = JSON.parse(document.body.getAttribute('data-shiftData'));
    shiftData.id = shiftData.base.id;
    shiftData.name = shiftData.base.name;
  })();

// *** Functions ***
// Toggle Sort Options Dropdown
function toggleSortDropdown() {
  let sort = document.getElementById('shift_header_sort');
  let dropdown = document.getElementById('shift_header_sort_dropdown');
  let options = dropdown.getElementsByTagName('button');
  let state = dropdown.getAttribute('data-expanded') == 'true';

  function toggleState () {
    dropdown.setAttribute('data-expanded', !state);
    dropdown.setAttribute('aria-expanded', !state);
  }

  sort.setAttribute('data-pressed', !state);
  sort.setAttribute('data-pressed', !state);
  for (i = 0; i < options.length; i++) {
    disenable(options[i], state);
  }

  if (state === false) {
    vishidden(dropdown, false);
    window.addEventListener('click', checkSortDropdownClick);
    window.addEventListener('keydown', checkSortDropdownKey);

    setTimeout(function () {
      let choices = dropdown.getElementsByTagName('button');

      toggleState();

      for (i = 0; i < choices.length; i++) {
        if (choices[i].getAttribute('data-pressed') == 'true') { choices[i].focus(); }
      }
    }, 50);
  }
  else {
    toggleState();
    window.removeEventListener('click', checkSortDropdownClick);
    window.removeEventListener('keydown', checkSortDropdownKey);

    setTimeout(function () {
      vishidden(dropdown, true);
      sort.focus();
    }, 250);
  }
}
// Update Feed Filter & Sort Settings
function updateFeedSettings(setting, type) {
  let feed = document.getElementById('shift_code_feed');
  let cache = document.getElementById('shift_code_cache');
  let panels = cache.getElementsByClassName('shift-code');
  codes = [];
  let panelsAdded = 0;
  let today = getDate('m-d-y', '/');

  function addPanel(code) {
    let panel;

    feed.appendChild(code);
    panelsAdded++;
    panel = feed.children[panelsAdded - 1];
    updatePanelTiming(panel, (panelsAdded));
    addDropdownPanelListener(panel)
    addPanelListeners(panel);
  }

  // Get Codes & Clear Feed
  (function () {
    let feedPanels = feed.getElementsByClassName('shift-code');

    for (i = 0; i < panels.length; i++) {
      let panel = panels[i]
      let currentStoredState = panel.getAttribute('data-expanded') == 'true';

      for (x = 0; x < feedPanels.length; x++) {
        let feedPanel = feedPanels[x];
        let currentState = feedPanel.getAttribute('data-expanded') == 'true';

        if (feedPanel.id == panel.id && currentState != currentStoredState) {
          console.warn("Current Stored State: " + currentStoredState + " | Updating to: " + !currentStoredState);
          updateDropdownPanelAttributes(panel, !currentStoredState);
        }
      }

      codes[i] = {};
      codes[i].panel = panel.cloneNode(true);
      codes[i].relDate = panel.getElementsByClassName('section rel')[0].getElementsByClassName('content')[0].innerHTML;
      codes[i].expDate = panel.getElementsByClassName('section exp')[0].getElementsByClassName('content')[0].innerHTML;
    }
    feed.innerHTML = '';
  })();

  // Filter Settings
  if (setting == 'filter') {
    let currentFilter = feed.getAttribute('data-filter');

    if (type != 'none') {
      function updateCode(code) {
        addPanel(code.panel);
        code.used = true;
      }

      for (let i = 0; i < codes.length; i++) {
        if (type == 'new' && codes[i].relDate == today)      { updateCode(codes[i]); }
        else if (type == 'exp' && codes[i].expDate == today) { updateCode(codes[i]); }
      }
    }
    else { updateFeedSettings('sort', feed.getAttribute('data-sort')); }

    feed.setAttribute('data-filter', type);

    // Update Filter Buttons
    (function () {
      let buttons = document.getElementById('shift_header').getElementsByClassName('counters')[0].getElementsByTagName('button');
      let labels = {
        true: '(Click to remove filter)',
        false: '(Click to filter)'
      };

      for (i = 0; i < buttons.length; i++) {
        let state = buttons[i].classList[1] == feed.getAttribute('data-filter');
        let currentLabel = buttons[i].title;
        let newLabel = currentLabel.replace(/\(.*\)/g, labels[state]);

        buttons[i].setAttribute('data-pressed', state);
        buttons[i].setAttribute('aria-pressed', state);
        updateLabel(buttons[i], newLabel);
      }
    })();
  }
  // Sort Settings
  if (setting == 'sort') {
    function sort(sortType) {
      codes = codes.sort(function(a, b) {
        let matches = {
          'new': {
            'primary': b,
            'secondary': a
          },
          'old': {
            'primary': a,
            'secondary': b
          }
        };

        if (a.relDate != b.relDate) { return matches[sortType].primary.relDate.localeCompare(matches[sortType].secondary.relDate); }
        else                        { return matches[sortType].primary.expDate.localeCompare(matches[sortType].secondary.expDate); }
      });
    }

    if (type == 'default') {
      function updateCode (x) {
        if (codes[x].used !== true) {
          addPanel(codes[x].panel);
          codes[x].used = true;
        }
      }

      sort ('new');

      // Add Expiring Codes
      for (let i = 0; i < codes.length; i++) {
        if (codes[i].expDate == today)       { updateCode(i); }
      }
      // Add New Codes
      for (let i = 0; i < codes.length; i++) {
        if (codes[i].relDate == today)       { updateCode(i); }
      }
      // Add Remaining Codes w/ an Expiration Date
      for (let i = 0; i < codes.length; i++) {
        if (codes[i].expDate != 'N/A')       { updateCode(i); }
      }
      // Add Remaining Codes w/o an Expiration Date
      for (let i = 0; i < codes.length; i++) { updateCode(i); }
    }
    if (type == 'newest') {
      sort ('new');

      for (let i = 0; i < codes.length; i++) { addPanel(codes[i].panel); }
    }
    if (type == 'oldest') {
      sort ('old');

      for (let i = 0; i < codes.length; i++) { addPanel(codes[i].panel); }
    }

    // Update Dropdown Menu & Panel Feed Properties
    (function () {
      let options = document.getElementById('shift_header_sort_dropdown').getElementsByTagName('button');

      feed.setAttribute('data-sort', type);

      setTimeout(function () {
        for (i = 0; i < options.length; i++) {
          let state = options[i].getAttribute('data-value') == type;

          options[i].setAttribute('data-pressed', state);
          options[i].setAttribute('aria-pressed', state);
        }
      }, 250);
    })();
  }
}
// Check Dropdown Clicks
function checkSortDropdownClick (event) {
  let dropdown = document.getElementById('shift_header_sort_dropdown').getElementsByClassName('panel')[0];
  let targets = [event, event.target.parentNode, event.target.parentNode.parentNode, event.target.parentNode.parentNode.parentNode];
  let matched = false;

  for (i = 0; i < targets.length; i++) {
    if (targets[i] == dropdown) {
      matched = true;
      break;
    }
  }

  if (matched === false) { toggleSortDropdown(); }
}
// Check Dropdown KeyPresses
function checkSortDropdownKey (event) {
  let target = event.target;
  let options = document.getElementById('shift_header_sort_dropdown').getElementsByTagName('button');
  let firstOption = options[0];
  let lastOption = options[options.length - 1];

  if (event.shiftKey === true && event.key == 'Tab' && target == firstOption || event.shiftKey === false && event.key == 'Tab' && target == lastOption) {
    event.preventDefault();

    if (target == firstOption)     { lastOption.focus(); }
    else if (target == lastOption) { firstOption.focus(); }
  }
}
// Toggles SHiFT Code Panels
function togglePanel (event) {
  let panel = event.currentTarget.parentNode.parentNode.parentNode;
  let e = {}; // Panel Elements
    (function () {
      e.body = panel.getElementsByClassName('body')[0];
        e.body.link = e.body.getElementsByClassName('src')[0].getElementsByClassName('content')[0].getElementsByTagName('a')[0];
        e.body.copyPC = e.body.getElementsByClassName('pc')[0].getElementsByClassName('content')[0].getElementsByClassName('copy')[0];
        e.body.copyXbox = e.body.getElementsByClassName('xbox')[0].getElementsByClassName('content')[0].getElementsByClassName('copy')[0];
        e.body.copyPS = e.body.getElementsByClassName('ps')[0].getElementsByClassName('content')[0].getElementsByClassName('copy')[0];
      })();
  let state = panel.getAttribute('data-expanded') == 'true';
  let labels = {
    true: 'Collapse SHiFT Code',
    false: 'Expand SHiFT Code'
  };

  panel.setAttribute('data-expanded', !state);
  panel.setAttribute('aria-expanded', !state);
  updateLabel(event.currentTarget, labels[!state]);
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
// Adds SHiFT Code Panel Event Listeners
function addPanelListeners(panel) {
  let copy = panel.getElementsByClassName('copy');

  for (i = 0; i < copy.length; i++) {
    copy[i].addEventListener('click', function (e) { copyCode(this); });
  }
}

// *** Immediate Functions ***
// Handles Page Construction
(function () {
  let header = document.getElementById('shift_header');
  let feed = document.getElementById('shift_code_feed');
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
    let action = (function () {
      if (name == 'total') { return ''; }
      else                 { return ' (Click to filter)'; }
    })();

    updateLabel(elm, count[name] + (' ') + labels[name] + action);
    elm.getElementsByClassName('count')[0].innerHTML = count[name];

    if (count[name] == 1) {
      disenable(elm, false);
      elm.classList.remove('inactive');
    }
  }
  // Construct the SHiFT Code Panel and add it to the feed
  function constructPanel (codeObject) {
    let panel = {};
      (function () {
        function returnContent(className) {
          return panel.body.getElementsByClassName(className)[0].getElementsByClassName('content')[0];
        }
        function returnCode(codeName) {
          let result = {};
            (function () {
              result.title = panel.body.getElementsByClassName(codeName)[0].getElementsByClassName('title')[0];
              result.base = returnContent(codeName);
              result.value = result.base.getElementsByClassName('value')[0];
              result.display = result.base.getElementsByClassName('display')[0];
              result.copy = result.base.getElementsByClassName('copy')[0];
            })();
          return result;
        }

        panel.template = document.getElementById('shift_code_template');
        panel.base = panel.template.content.children[0].cloneNode(true);
        panel.header = panel.base.getElementsByClassName('header')[0];
          panel.title = panel.header.getElementsByClassName('title')[0].getElementsByClassName('string')[0];
            panel.reward = panel.title.getElementsByClassName('reward')[0];
            panel.labels = {};
              panel.labels.description = panel.title.getElementsByClassName('label description')[0];
              panel.labels.new = panel.base.getElementsByClassName('label new')[0];
              panel.labels.exp = panel.base.getElementsByClassName('label exp')[0];
          panel.progress = panel.header.getElementsByClassName('progress-bar')[0];
            panel.progressBar = panel.progress.getElementsByClassName('progress')[0];
        panel.body = panel.base.getElementsByClassName('body')[0];
          panel.relDate = returnContent('rel');
          panel.expDate = returnContent('exp');
          panel.source = returnContent('src').getElementsByTagName('a')[0];
          panel.notes = returnContent('notes').getElementsByTagName('ul')[0];
          panel.codePC = returnCode('pc');
          panel.codeXbox = returnCode('xbox');
          panel.codePS = returnCode('ps');
      })();
    let currentDate = getDate();

    // Handle Panel Properties
    (function () {
      // Panel ID
      panel.base.id = ('shift_code_') + codeObject.codeID;
    })();
    // Handle Header Properties
    (function () {
      // Reward
      (function () {
        let reward = codeObject.reward;
        let description = panel.labels.description;

        // if (reward.length > 20) { panel.description.classList.add('long'); }
        if (reward != '5 Golden Keys') {
          panel.reward.innerHTML = reward;
          updateLabel(description, 'Rare SHiFT Code');
          description.childNodes[0].innerHTML = 'Rare SHiFT Code';
        }
      })();
      // Handles all dates (Flags, Dates, Progress Bar)
      (function () {
        function convertDate (date) {
          let y = date.substring(0, 4);
          let md = date.substring(5);

          return (md + ('/') + y).replace(/-/g, '/');
        }

        let today   = getDate('m-d-y', '/');
        let relDate = convertDate(codeObject.relDate);
        let expDate = (function () {
          let exp = codeObject.expDate;

          if (exp === null) {
            panel.expDate.classList.add('inactive');
            return 'N/A';
          }
          else { return convertDate(exp); }
        })();

        // Flags & Dates
        (function () {
          panel.relDate.innerHTML = relDate;
          panel.expDate.innerHTML = expDate;

          if (today == relDate)  { panel.base.classList.add('new'); }
          else                   { panel.labels.new.remove(); }
          if (today == expDate)  { panel.base.classList.add('exp'); }
          else                   { panel.labels.exp.remove(); }
        })();
        // Progress Bar
        (function () {
          function getDifference (start, end) {
            let date = {
              'start': new Date(start),
              'end': new Date(end)
            };
            let difference = Math.abs(date.end.getTime() - date.start.getTime());

            return Math.ceil(difference / (1000 * 3600 * 24));
          }
          function updateProgress(timeLeft, currentWidth) {
            updateLabel(panel.progress, timeLeft);
            panel.progress.setAttribute('aria-valuenow', currentWidth);
            panel.progressBar.style.width = currentWidth + ('%');
          }

          if (expDate != 'N/A') {
            let width = (function () {
              if (relDate != expDate) {
                let origin = (getDifference(today, relDate) / getDifference(expDate, relDate) * 100).toString();

                if (origin.indexOf('.') != -1)  { return origin.match(/\d{1,2}(?=\.)/)[0]; }
                else                            { return origin; }
              }
              else { return 100; }
            })();
            let countdown = (function () {
              let time = getDifference(today, expDate);
              let string = (function () {
                let plural = '';

                if (time != 1) { plural = 's'; }

                return (' Day') + plural + (' Left');
              })();

              return time + string;
            })();

            updateProgress(countdown, width);
          }
          else {
            let width = 0;
            let countdown = 'No Expiration Date';

            updateProgress(countdown, width);
            panel.progress.classList.add('inactive');
          }
        })();
      })();
      // Source
      (function () {
        let source = codeObject.source;

        if (source !== null) {
          let label = (function () {
            let str = 'Source';

            if (source.indexOf('facebook') != -1)     { str += ' (Facebook)'; }
            else if (source.indexOf('twitter') != -1) { str += ' (Twitter)'; }

            return str;
          })();

          panel.source.href = source;
          panel.source.innerHTML += source;
          updateLabel(panel.source, label);
        }
        else {
          let e = document.createElement('span');
          let parent = panel.source.parentNode;

          e.innerHTML = 'N/A';
          updateLabel(e, 'No confirmed source available');
          parent.appendChild(e);
          parent.classList.add('inactive');
          panel.source.remove();
        }
      })();
      // Notes
      (function () {
        let notes = codeObject.notes;

        // Notes Attribute
        if (notes !== null) {
          panel.notes.innerHTML = (function () {
            if (notes.indexOf('-') == -1) {
              return ('<li><i>') + notes + ('</i></li>');
            }
            else {
              function updateNotes (match) { return match.replace(/-\s{1}/g, '<li><i>') + '</i></li>'; }

              return notes.replace(/-.*/g, updateNotes);
            }
          })();
        }
        else { panel.notes.parentNode.parentNode.remove(); }
      })();
    })();
    // Handle Body Properties
    (function () {
      let fields = ['PC', 'Xbox', 'PS'];

      for (i = 0; i < fields.length; i++) {
        let code = ('code') + fields[i];
        let elm = panel[code];
        let entry = codeObject[code];

        elm.title.innerHTML = codeObject[('platforms') + fields[i]] + (':');
        elm.display.innerHTML = entry;
        elm.value.value = entry;
      }
    })();

    // Configure Dropdown Panels
    dropdownPanelSetup(panel.base);
    // Update Copy Listeners
    addPanelListeners(panel.base);

    // Add panel to feed
    (function () {
      let overlay = document.getElementById('shift_overlay');

      updateCounter('total');
      updatePanelTiming(panel.base, count.total);
      feed.appendChild(panel.base);

      if (codeObject.relDate == currentDate)      { updateCounter('new'); }
      else if (codeObject.expDate == currentDate) { updateCounter('exp'); }
      if (count.total == 1) { vishidden(overlay, true); }
      if (count.total == count.retrieved) {
        addFocusScrollListeners(feed);
        disenable(document.getElementById('shift_header_sort'), false);
        overlay.remove();
        document.getElementById('shift_code_template').remove();

        // Copy Panels to Cache
        for (i = 0; i < feed.children.length; i++) {
          let panel = feed.children[i].cloneNode(true);

          document.getElementById('shift_code_cache').appendChild(panel);
        }
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
document.getElementById('shift_header_sort').addEventListener('click', toggleSortDropdown);
// Filter Button Listeners
(function () {
  let counters = document.getElementById('shift_header').getElementsByClassName('counters')[0].getElementsByTagName('button');

  for (i = 0; i < counters.length; i++) {
    counters[i].addEventListener('click', function (e) {
      let call = this.classList[1];

      if (call != document.getElementById('shift_code_feed').getAttribute('data-filter')) { updateFeedSettings('filter', call); }
      else                                                                                { updateFeedSettings('filter', 'none'); }
    });
  }
})();
// Sort Options Dropdown Listeners
(function () {
  let choices = document.getElementById('shift_header_sort_dropdown').getElementsByTagName('BUTTON');

  for (i = 0; i < choices.length; i++) {
    choices[i].addEventListener('click', function (e) {
      let call = this.getAttribute('data-value');

      if (call != document.getElementById('shift_code_feed').getAttribute('data-filter')) { updateFeedSettings('sort', call); }

      toggleSortDropdown();
    });
  }
})();
