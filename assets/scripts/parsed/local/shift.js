/*********************************
  SHiFT Page Scripts
*********************************/
// *** Variables ***
var shiftData = {};

(function () {
  shiftData.base = JSON.parse(document.body.getAttribute('data-shiftData'));
  shiftData.id = shiftData.base.id;
  shiftData.name = shiftData.base.name;
})(); // *** Functions ***
// Update Feed Filter & Sort Settings


function updateFeedSettings(setting, type) {
  var feed = document.getElementById('shift_code_feed');
  var cache = document.getElementById('shift_code_cache');
  var panels = cache.getElementsByClassName('shift-code');
  codes = [];
  var panelsAdded = 0;
  var today = getDate('m-d-y', '/');

  function addPanel(code) {
    var panel;
    feed.appendChild(code);
    panelsAdded++;
    panel = feed.children[panelsAdded - 1];
    updatePanelTiming(panel, panelsAdded);
    addDropdownPanelListener(panel);
    addPanelListeners(panel);
  } // Get Codes & Clear Feed


  (function () {
    var feedPanels = feed.getElementsByClassName('shift-code');

    for (i = 0; i < panels.length; i++) {
      var panel = panels[i];
      var currentStoredState = panel.getAttribute('data-expanded') == 'true';

      for (x = 0; x < feedPanels.length; x++) {
        var feedPanel = feedPanels[x];
        var currentState = feedPanel.getAttribute('data-expanded') == 'true';

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
  })(); // Filter Settings


  if (setting == 'filter') {
    var currentFilter = feed.getAttribute('data-filter');

    if (type != 'none') {
      var updateCode = function updateCode(code) {
        addPanel(code.panel);
        code.used = true;
      };

      for (var _i = 0; _i < codes.length; _i++) {
        if (type == 'new' && codes[_i].relDate == today) {
          updateCode(codes[_i]);
        } else if (type == 'exp' && codes[_i].expDate == today) {
          updateCode(codes[_i]);
        }
      }
    } else {
      updateFeedSettings('sort', feed.getAttribute('data-sort'));
    }

    feed.setAttribute('data-filter', type); // Update Filter Buttons

    (function () {
      var buttons = document.getElementById('shift_header').getElementsByClassName('counters')[0].getElementsByTagName('button');
      var labels = {
        "true": '(Click to remove filter)',
        "false": '(Click to filter)'
      };

      for (i = 0; i < buttons.length; i++) {
        var state = buttons[i].classList[1] == feed.getAttribute('data-filter');
        var currentLabel = buttons[i].title;
        var newLabel = currentLabel.replace(/\(.*\)/g, labels[state]);
        buttons[i].setAttribute('data-pressed', state);
        buttons[i].setAttribute('aria-pressed', state);
        updateLabel(buttons[i], newLabel);
      }
    })();
  } // Sort Settings


  if (setting == 'sort') {
    var sort = function sort(sortType) {
      codes = codes.sort(function (a, b) {
        var matches = {
          'new': {
            'primary': b,
            'secondary': a
          },
          'old': {
            'primary': a,
            'secondary': b
          }
        };

        if (a.relDate != b.relDate) {
          return matches[sortType].primary.relDate.localeCompare(matches[sortType].secondary.relDate);
        } else {
          return matches[sortType].primary.expDate.localeCompare(matches[sortType].secondary.expDate);
        }
      });
    };

    if (type == 'default') {
      var _updateCode = function _updateCode(x) {
        if (codes[x].used !== true) {
          addPanel(codes[x].panel);
          codes[x].used = true;
        }
      };

      sort('new'); // Add Expiring Codes

      for (var _i2 = 0; _i2 < codes.length; _i2++) {
        if (codes[_i2].expDate == today) {
          _updateCode(_i2);
        }
      } // Add New Codes


      for (var _i3 = 0; _i3 < codes.length; _i3++) {
        if (codes[_i3].relDate == today) {
          _updateCode(_i3);
        }
      } // Add Remaining Codes w/ an Expiration Date


      for (var _i4 = 0; _i4 < codes.length; _i4++) {
        if (codes[_i4].expDate != 'N/A') {
          _updateCode(_i4);
        }
      } // Add Remaining Codes w/o an Expiration Date


      for (var _i5 = 0; _i5 < codes.length; _i5++) {
        _updateCode(_i5);
      }
    }

    if (type == 'newest') {
      sort('new');

      for (var _i6 = 0; _i6 < codes.length; _i6++) {
        addPanel(codes[_i6].panel);
      }
    }

    if (type == 'oldest') {
      sort('old');

      for (var _i7 = 0; _i7 < codes.length; _i7++) {
        addPanel(codes[_i7].panel);
      }
    } // Update Dropdown Menu & Panel Feed Properties


    (function () {
      var options = document.getElementById('shift_header_sort_dropdown').getElementsByTagName('button');
      feed.setAttribute('data-sort', type);
      setTimeout(function () {
        for (i = 0; i < options.length; i++) {
          var state = options[i].getAttribute('data-value') == type;
          options[i].setAttribute('data-pressed', state);
          options[i].setAttribute('aria-pressed', state);
        }
      }, 250);
    })();
  }
}
/*
// Copies the SHiFT Code to Clipboard
function copyCode (event) {
  event.parentNode.getElementsByClassName('value')[0].select();
  document.execCommand('copy');
  event.classList.remove('animated');

  setTimeout(function () {
    event.classList.add('animated');
  }, 1);
}
*/
// Update Panel Animation Timing


function updatePanelTiming(panel, id) {
  panel.style.animationDelay = (id - 1) * 0.2 + 's';
} // Adds SHiFT Code Panel Event Listeners


function addPanelListeners(panel) {
  var copy = panel.getElementsByClassName('copy');

  for (i = 0; i < copy.length; i++) {
    copy[i].addEventListener('click', copyToClipboard);
  }
} // *** Immediate Functions ***
// Handles Page Construction


(function () {
  var header = document.getElementById('shift_header');
  var feed = document.getElementById('shift_code_feed');
  var count = {
    'retrieved': 0,
    'total': 0,
    'new': 0,
    'exp': 0
  }; // Update Counters and their respective elements

  function updateCounter(name) {
    count[name]++;

    var title = function () {
      var plural = 's';

      if (count[name] == 1) {
        plural = '';
      }

      return 'SHiFT Code' + plural;
    }();

    var elm = document.getElementById('shift_header_count_' + name);
    var labels = {
      'total': title + ' Available',
      'new': 'New ' + title,
      'exp': 'Expiring ' + title
    };

    var action = function () {
      if (name == 'total') {
        return '';
      } else {
        return ' (Click to filter)';
      }
    }();

    updateLabel(elm, count[name] + ' ' + labels[name] + action);
    elm.getElementsByClassName('count')[0].innerHTML = count[name];

    if (count[name] == 1) {
      disenable(elm, false);
      elm.classList.remove('inactive');
    }
  } // Construct the SHiFT Code Panel and add it to the feed


  function constructPanel(codeObject) {
    var panel = {};

    (function () {
      function returnContent(className) {
        return panel.body.getElementsByClassName(className)[0].getElementsByClassName('content')[0];
      }

      function returnCode(codeName) {
        var result = {};

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
      panel.labels["new"] = panel.base.getElementsByClassName('label new')[0];
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

    var currentDate = getDate(); // Handle Panel Properties

    (function () {
      // Panel ID
      panel.base.id = 'shift_code_' + codeObject.codeID;
    })(); // Handle Header Properties


    (function () {
      // Reward
      (function () {
        var reward = codeObject.reward;
        var description = panel.labels.description; // if (reward.length > 20) { panel.description.classList.add('long'); }

        if (reward != '5 Golden Keys') {
          panel.reward.innerHTML = reward;
          updateLabel(description, 'Rare SHiFT Code');
          description.childNodes[0].innerHTML = 'Rare SHiFT Code';
        }
      })(); // Handles all dates (Flags, Dates, Progress Bar)


      (function () {
        function convertDate(date) {
          var y = date.substring(0, 4);
          var md = date.substring(5);
          return (md + '/' + y).replace(/-/g, '/');
        }

        var today = getDate('m-d-y', '/');
        var relDate = convertDate(codeObject.relDate);

        var expDate = function () {
          var exp = codeObject.expDate;

          if (exp === null) {
            panel.expDate.classList.add('inactive');
            return 'N/A';
          } else {
            return convertDate(exp);
          }
        }(); // Flags & Dates


        (function () {
          panel.relDate.innerHTML = relDate;
          panel.expDate.innerHTML = expDate;

          if (today == relDate) {
            panel.base.classList.add('new');
          } else {
            panel.labels["new"].remove();
          }

          if (today == expDate) {
            panel.base.classList.add('exp');
          } else {
            panel.labels.exp.remove();
          }
        })(); // Progress Bar


        (function () {
          function getDifference(start, end) {
            var date = {
              'start': new Date(start),
              'end': new Date(end)
            };
            var difference = Math.abs(date.end.getTime() - date.start.getTime());
            return Math.ceil(difference / (1000 * 3600 * 24));
          }

          function updateProgress(timeLeft, currentWidth) {
            updateLabel(panel.progress, timeLeft);
            panel.progress.setAttribute('aria-valuenow', currentWidth);
            panel.progressBar.style.width = currentWidth + '%';
          }

          if (expDate != 'N/A') {
            var width = function () {
              if (relDate != expDate) {
                var origin = (getDifference(today, relDate) / getDifference(expDate, relDate) * 100).toString();

                if (origin.indexOf('.') != -1) {
                  return origin.match(/\d{1,2}(?=\.)/)[0];
                } else {
                  return origin;
                }
              } else {
                return 100;
              }
            }();

            var countdown = function () {
              var time = getDifference(today, expDate);

              var string = function () {
                var plural = '';

                if (time != 1) {
                  plural = 's';
                }

                return ' Day' + plural + ' Left';
              }();

              return time + string;
            }();

            updateProgress(countdown, width);
          } else {
            var _width = 0;
            var _countdown = 'No Expiration Date';
            updateProgress(_countdown, _width);
            panel.progress.classList.add('inactive');
          }
        })();
      })(); // Source


      (function () {
        var source = codeObject.source;

        if (source !== null) {
          var label = function () {
            var str = 'Source';

            if (source.indexOf('facebook') != -1) {
              str += ' (Facebook)';
            } else if (source.indexOf('twitter') != -1) {
              str += ' (Twitter)';
            }

            return str;
          }();

          panel.source.href = source;
          panel.source.innerHTML += source;
          updateLabel(panel.source, label);
        } else {
          var e = document.createElement('span');
          var parent = panel.source.parentNode;
          e.innerHTML = 'N/A';
          updateLabel(e, 'No confirmed source available');
          parent.appendChild(e);
          parent.classList.add('inactive');
          panel.source.remove();
        }
      })(); // Notes


      (function () {
        var notes = codeObject.notes; // Notes Attribute

        if (notes !== null) {
          panel.notes.innerHTML = function () {
            if (notes.indexOf('-') == -1) {
              return '<li><i>' + notes + '</i></li>';
            } else {
              var updateNotes = function updateNotes(match) {
                return match.replace(/-\s{1}/g, '<li><i>') + '</i></li>';
              };

              return notes.replace(/-.*/g, updateNotes);
            }
          }();
        } else {
          panel.notes.parentNode.parentNode.remove();
        }
      })();
    })(); // Handle Body Properties


    (function () {
      var fields = ['PC', 'Xbox', 'PS'];

      for (i = 0; i < fields.length; i++) {
        var code = 'code' + fields[i];
        var elm = panel[code];
        var entry = codeObject[code];
        elm.title.innerHTML = codeObject['platforms' + fields[i]] + ':';
        elm.display.innerHTML = entry;
        elm.value.value = entry;
      }
    })(); // Configure Dropdown Panels


    dropdownPanelSetup(panel.base); // Update Copy Listeners

    addPanelListeners(panel.base); // Add panel to feed

    (function () {
      var overlay = document.getElementById('shift_overlay');
      updateCounter('total');
      updatePanelTiming(panel.base, count.total);
      feed.appendChild(panel.base);

      if (codeObject.relDate == currentDate) {
        updateCounter('new');
      } else if (codeObject.expDate == currentDate) {
        updateCounter('exp');
      }

      if (count.total == 1) {
        vishidden(overlay, true);
      }

      if (count.total == count.retrieved) {
        addFocusScrollListeners(feed);
        disenable(document.getElementById('shift_header_sort'), false);
        overlay.remove();
        document.getElementById('shift_code_template').remove(); // Copy Panels to Cache

        for (i = 0; i < feed.children.length; i++) {
          var _panel = feed.children[i].cloneNode(true);

          document.getElementById('shift_code_cache').appendChild(_panel);
        }
      }
    })();
  } // Retrieve SHiFT Codes and add them to the page


  (function () {
    // Wait for dependencies
    function executeWhenReady() {
      if (typeof newAjaxRequest == 'function' && typeof getDate == 'function') {
        // Fetch SHiFT Codes
        newAjaxRequest('GET', '/assets/php/scripts/shift/retrieveCodes.php?gameID=' + shiftData.id, function (response) {
          var retrievedCodes = JSON.parse(response).response;
          count.retrieved = retrievedCodes.length; // Start processing

          if (count.retrieved > 0) {
            for (var _i8 = 0; _i8 < count.retrieved; _i8++) {
              // Construct the panel for the SHiFT Code
              constructPanel(retrievedCodes[_i8]);
            }
          } // Show error message
          else {
              var overlay = document.getElementById('shift_overlay');
              vishidden(overlay.getElementsByClassName('spinner')[0], true);
              vishidden(overlay.getElementsByClassName('error')[0], false);
            }
        });
      } else {
        setTimeout(function () {
          executeWhenReady();
        }, 100);
      }
    }

    executeWhenReady();
  })();
})(); // Filter Button Listeners


(function () {
  var counters = document.getElementById('shift_header').getElementsByClassName('counters')[0].getElementsByTagName('button');

  for (i = 0; i < counters.length; i++) {
    counters[i].addEventListener('click', function (e) {
      var call = this.classList[1];

      if (call != document.getElementById('shift_code_feed').getAttribute('data-filter')) {
        updateFeedSettings('filter', call);
      } else {
        updateFeedSettings('filter', 'none');
      }
    });
  }
})(); // Sort Options Dropdown Listeners


(function () {
  var dropdown = document.getElementById('shift_header_sort_dropdown');
  var choices = dropdown.getElementsByTagName('BUTTON');

  for (i = 0; i < choices.length; i++) {
    choices[i].addEventListener('click', function (e) {
      var call = this.getAttribute('data-value');

      if (call != document.getElementById('shift_code_feed').getAttribute('data-sort')) {
        updateFeedSettings('sort', call);
      }

      toggleDropdownMenu(dropdown);
    });
  }
})();