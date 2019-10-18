var shiftProps = {};

function updateShiftPager () {
  let id = shiftProps.gameInfo.id;
  let pager = document.getElementById('shift_code_pager');
  let limit = shiftProps.limit;
  let total = getClasses(document.getElementById('shift_code_feed'), 'shift-code').length;
  let props = {
    now: (shiftProps.offset / limit) + 1,
    max: (function () {
      let count = 0;
      let filter = shiftProps.filter;

      if (filter.length == 0) {
        count = shiftStats.total[id];
      }
      else {
        for (let f of filter) {
          count += shiftStats[f][id];
        }
      }

      return Math.ceil(count / limit);
    })(),
    offset: limit,
    onclick: 'shift_header_sort'
  };
  let propNames = Object.keys(props);

  for (let i = 0; i < propNames.length; i++) {
    let prop = propNames[i];

    pager.setAttribute(`data-${prop}`, props[prop]);
  }

  delClass(pager, 'configured');
  pager = configurePager(pager);

  for (let button of getTags(pager, 'button')) {
    button.addEventListener('click', function (e) {
      let val = tryParseInt(this.getAttribute('data-value'));

      if (val != shiftProps.offset) {
        shiftProps.offset = val;
        getCodes();
      }
    });
  }
}
function getCodes () {
  let count = {
    fetched: 0,
    added: 0
  };
  // Elements
  let header = document.getElementById('shift_header');
  let badges = {
    total: getClass(header, 'badge total'),
    new: getClass(header, 'badge new'),
    exp: getClass(header, 'badge exp')
  };
  let list = document.getElementById('shift_code_feed');

  function errorToast (body) {
    return newToast({
      settings: {
        template: 'exception'
      },
      content: {
        title: 'An error has occurred',
        body: body
      }
    });
  }

  function changeOverlay (settings) {
    let comps = {};
        comps.overlay = document.getElementById('shift_overlay');
        comps.spinner = getClass(comps.overlay, 'spinner');
        comps.error = getClass(comps.overlay, 'error');
    let keys = Object.keys(settings);

    for (let i = 0; i < keys.length; i++) {
      let key = keys[i];

      vishidden(comps[key], settings[key]);
    }
  }
  function toggleControls (isDisabled) {
    let controls = [
      badges.new,
      badges.exp,
      document.getElementById('shift_header_sort')
    ];

    for (let i = 0; i < controls.length; i++) {
      let c = controls[i];

      if (!hasClass(c, 'inactive')) {
        disenable(c, isDisabled);
      }
    }
  }

  function clearList () {
    let codes = getClasses(list, 'shift-code');

    for (let i = codes.length - 1; i >= 0; i--) {
      list.removeChild(codes[i]);
    }
  }
  function addCode (code) {
    let panel = getTemplate('shift_code_template');
    let e = {};
        e.header = getClass(panel, 'header');
        e.labels = getClass(e.header, 'labels');
        e.body = getClass(panel, 'body');

    function getField(name, parent = e.body) {
      return getClass(getClass(parent, name), 'content');
    }

    // Properties
    (function () {
      panel.id = `shift_code_${code.id}`;
      panel.style.animationDelay = `${count.added * 0.2}s`;
    })();
    // Details
    (function () {
      // Reward
      (function () {
        let rew = code.reward;
        let des = getClass(e.labels, 'basic');

        getClass(e.header, 'reward').innerHTML = rew;

        if (rew.search('Golden Key') == -1) {
          des.childNodes[0].innerHTML = 'Rare SHiFT Code';
          updateLabel(des, 'Rare SHiFT Code with an uncommon reward');
        }
      })();
      // Labels, Dates, Progress Bar
      (function () {
        function getFDate(date = 'now') {
          return getDate('m-d-y', '/', date);
        }

        let expField = getField('exp');
        // Dates
        let today = getFDate();
        let rel = getFDate(code.rel_date);
        let exp = (function () {
          let ex = code.exp_date;

          if (ex === null) {
            addClass(expField, 'inactive');
            return 'N/A';
          }
          else {
            return getFDate(ex);
          }
        })();

        // Labels
        if (today == rel) { addClass(panel, 'new'); }
        else              { e.labels.removeChild(getClass(e.labels, 'new')); }
        if (today == exp) { addClass(panel, 'exp'); }
        else              { e.labels.removeChild(getClass(e.labels, 'exp')); }
        // Date Fields
        getField('rel').innerHTML = rel;
        expField.innerHTML = exp;
        // Progress Bar
        (function () {
          let pb = getClass(e.header, 'progress-bar');

          function getDif (start, end) {
            let date = {
              start: new Date(start),
              end: new Date(end)
            };
            let dif = Math.abs(date.end.getTime() - date.start.getTime());

            return Math.ceil(dif / (1000 * 3600 * 24));
          }
          function update (percent, label) {
            updateProgressBar(pb, percent, { useWidth: true });
            updateLabel(pb, label);
          }

          if (exp != 'N/A') {
            let percent = (function () {
              if (rel != exp) {
                return Math.round((getDif(today, rel) / getDif(exp, rel)) * 100);
              }
              else {
                return 100;
              }
            })();
            let label = (function () {
              let days = getDif(today, exp);
                let plural = (function () {
                  if (days != 1) { return 's'; }
                  else           { return ''; }
                })();

              return `${days} Day${plural} Left`;
            })();

            update(percent, label)
          }
          else {
            update(0, 'No Expiration Date');
            addClass(pb, 'inactive');
          }
        })();
      })();
      // Source
      (function () {
        let s = code.source;
        let field = getField('src');
        let link = getClass(field, 'link');
        let noLink = getClass(field, 'no-link');

        if (s !== null) {
          link.href = s;
          link.innerHTML += s;
          field.removeChild(noLink);
        }
        else {
          addClass(field, 'inactive');
          field.removeChild(link);
        }
      })();
      // Notes
      (function () {
        let n = code.notes;

        if (n !== null) {
          getTag(getField('notes'), 'ul').innerHTML = (function () {
            if (n.indexOf('-') == -1) {
              return (`<li><i>${n}</i></li>`);
            }
            else {
              function updateNotes (match) {
                let mRegex = new RegExp('-\\s{1}', 'g');

                return `${match.replace(mRegex, '<li><i>')}</i></li>`;
              }

              let regex = new RegExp('-.*', 'g');

              return n.replace(regex, updateNotes);
            }
          })();
        }
        else {
          e.body.removeChild(getClass(e.body, 'notes'));
        }
      })();
    })();
    // Codes
    (function () {
      let platforms = ['pc', 'xbox', 'ps'];

      for (let i = 0; i < platforms.length; i++) {
        let platform = platforms[i];
        let field = getClass(e.body, platform);
        let codeVal = code[`code_${platform}`];

        getClass(field, 'title').innerHTML = code[`platforms_${platform}`];
        getClass(field, 'display').innerHTML = codeVal;
        getClass(field, 'value').value = codeVal;
      }
    })();
    // Config
    (function () {
      dropdownPanelSetup(panel);

      // Copy to Clipboard Listeners
      (function () {
        let copy = getClasses(e.body, 'copy');

        for (i = 0; i < copy.length; i++) {
          copy[i].addEventListener('click', copyToClipboard);
        }
      })();
    })();
    // Add to List
    (function () {
      count.added++;

      if (count.added == 1) {
        clearList();
        changeOverlay({
          overlay: true,
          spinner: true,
          error: true
        });
      }
      if (count.added == count.fetched) {
        setTimeout(function () {
          toggleControls(false);
        }, 600);
      }

      list.insertBefore(panel, document.getElementById('shift_code_pager'));
    })();
  }

  function fetchCodes (serverResponse) {
    let response = tryJSONParse(serverResponse);

    if (response && response.statusCode == 0) {
      let codes = response.payload;
          count.fetched = response.payload.length;

      if (count.fetched > 0) {
        for (let i = 0; i < count.fetched; i++) {
          addCode(codes[i]);
        }
      }
      else {
        clearList();
        changeOverlay({
          overlay: false,
          spinner: true,
          error: false
        });
      }
    }
    else {
      clearList();
      changeOverlay({
        overlay: false,
        spinner: true,
        error: false
      });
      errorToast('We could not retrieve any SHiFT Codes due to an error. Please refresh the page and try again.');
    }

    lpbUpdate(100);
  }

  // Fetch SHiFT Codes
  toggleControls(true);
  lpbUpdate(90, true, { start: 20 });
  newAjaxRequest({
    file: `/assets/php/scripts/shift/getCodes
           ?gameID=${shiftProps.gameInfo.id}
           &order=${shiftProps.order}
           &filter=${shiftProps.filter.join(', ')}
           &limit=${shiftProps.limit}
           &offset=${shiftProps.offset}
           &hash=${shiftProps.hash}`,
    callback: fetchCodes
  });
}

// Initial Functions
shiftScriptsInit = setInterval(function () {
  if (globalFunctionsReady) {
    clearInterval(shiftScriptsInit);

    let header = document.getElementById('shift_header');

    shiftProps = {
      gameInfo: tryJSONParse(document.body.getAttribute('data-shift')),
      order: 'default',
      filter: [],
      limit: 10,
      offset: 0,
      hash: (function () {
        let h = window.location.hash;

        if (h.search('#shift_code_') == 0) {
          return h.replace('#shift_code_', '');
        }
        else {
          return false;
        }
      })()
    };
    hashRequests['shift_code_'] = function () {
      shiftProps.hash = window.location.hash.replace('#shift_code_', '');
      getCodes();
      hashUpdate();
      shiftProps.hash = false;
    };

    // Initial code listing
    getCodes();
    shiftProps.hash = false;
    // Setup badges & pager
    (function () {
      tryToRun({
        attempts: false,
        delay: 500,
        function: function () {
          if (shiftStats) {
            let id = shiftProps.gameInfo.id;

            // Setup badges
            (function () {
              let regex = new RegExp('\\d{1,2}');
              let badges = {
                total: getClass(header, 'badge total'),
                new: getClass(header, 'badge new'),
                expiring: getClass(header, 'badge exp')
              };
              let badgeNames = Object.keys(badges);

              for (let i = 0; i < badgeNames.length; i++) {
                let bn = badgeNames[i];
                let b = badges[bn];
                let c = shiftStats[bn][id];
                let label = (function () {
                  let str = b.title;

                  str = str.replace('No', c);

                  if (c == 1) {
                    str = str.replace('Codes', 'Code');
                  }
                  if (bn != 'total') {
                    str += ' (Click to Filter)';
                  }

                  return str;
                })();

                if (c > 0) {
                  getClass(b, 'count').innerHTML = c;
                  updateLabel(b, label);

                  if (bn != 'total') {
                    b.addEventListener('click', function (e) {
                      let attr = this.getAttribute('aria-pressed') == 'true';
                      let val = this.getAttribute('data-value');

                      if (!attr) {
                        updateLabel(this, this.title.replace('Filter', 'clear Filter'));
                        shiftProps.filter.push(val);
                      }
                      else {
                        let f = shiftProps.filter;

                        updateLabel(this, this.title.replace('clear Filter', 'Filter'));
                        f.splice(f.indexOf(val), 1);
                      }

                      shiftProps.offset = 0;
                      getCodes();
                      updateShiftPager();
                    });
                  };

                  disenable(b, false);
                  delClass(b, 'inactive');
                }
              }
            })();
            // Setup pager
            updateShiftPager();

            return true;
          }
          else {
            return false;
          }
        }
      });
    })();
    // Sort Listeners
    (function () {
      let dropdown = document.getElementById('shift_header_sort_dropdown');
      let options = getTags(dropdown, 'button');

      for (let i = 0; i < options.length; i++) {
        options[i].addEventListener('click', function (e) {
          let attr = this.getAttribute('aria-pressed');

          if (!attr || attr == 'false') {
            shiftProps.order = this.getAttribute('data-value');
            getCodes();
          }
        });
      }
    })();
  }
}, 250);
