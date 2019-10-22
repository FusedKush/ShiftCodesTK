var shiftProps = {};

function updateShiftPager () {
  let id = shiftProps.gameID;
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

  addPagerListeners(pager, function (e) {
    let val = tryParseInt(this.getAttribute('data-value'));

    if (val != shiftProps.offset) {
      shiftProps.offset = val;
      getCodes();
    }
  });
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

        if (rew.search('\\d+ Golden Key') != 0) {
          des.childNodes[0].innerHTML = 'Rare SHiFT Code';
          updateLabel(des, 'Rare SHiFT Code with an uncommon reward');
        }
      })();
      // Labels, Dates, Progress Bar
      (function () {
        let dateFormat = 'monthN date, year';

        function getFDate(date = false) {
          return datetime(dateFormat, date);
        }

        let today = getFDate();
        let names = ['rel', 'exp'];
        let events = {
          rel: 'new',
          exp: 'exp'
        };
        let dates = {
          today: getFDate()
        };
          (function () {
            for (let n of names) {
              dates[n] = {};
              dates[n].origin = code[`${n}_date`];
              dates[n].form = getFDate(dates[n].origin);
              dates[n].str = (function () {
                let o = dates[n].origin;

                if (o) {
                  if (o.search('00:00:00') != -1) {
                    return dates[n].form;
                  }
                  else {
                    return datetime(`${dateFormat} @ hour12:minute ampm`, o);
                  }
                }
                else {
                  return 'N/A';
                }
              })();
            }
          })();

        // Panel Class, Labels, & Fields
        for (let n of names) {
          let d = dates[n];
          let field = getField(n);

          function set (label, str = d.str) {
            field.innerHTML += str;
            updateLabel(field, label);
          }

          if (d.origin && dates.today == d.form) {
            addClass(panel, events[n]);
          }
          else {
            e.labels.removeChild(getClass(e.labels, events[n]));
          }

          if (d.origin) {
            let relative = dateRel(d.origin);

            function getLabel (string) {
              return string.replace(datetime('monthN', d.form), datetime('monthL', d.form));
            }

            if (relative) {
              let day = copyElm(field);
              let str = `${relative}, ${d.str}`;

              addClass(day, 'day');
              day.innerHTML = `<span>${d.str.replace(datetime(dateFormat, d.origin), relative)}</span>`;
              field.appendChild(day);
              set(getLabel(str), str);
            }
            else {
              set(getLabel(d.str));
            }
          }
          else {
            addClass(field, 'inactive');
            set('No Expiration Date');
          }
        }
        // Progress Bar
        (function () {
          let pb = getClass(e.header, 'progress-bar');
          let exp = dates.exp.origin;

          function update (val, label) {
            updateProgressBar(pb, val, { useWidth: true });
            updateLabel(pb, label);
          }

          if (exp) {
            let val = (function () {
              let rel = dates.rel.origin;
              let exp = dates.exp.origin;

              if (dates.rel.form != dates.exp.form) {
                return Math.round((dateDif(rel) / dateDif(rel, exp)) * 100);
              }
              else {
                return 100;
              }
            })();
            let label = (function () {
              let dif = dateDif(exp);

              return `${dif} Day${checkPlural(dif)} left`;
            })();

            update(val, label);
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
        hashUpdate();

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
           ?gameID=${shiftProps.gameID}
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
    let hashState;

    shiftProps = {
      gameID: window.location.pathname.slice(1),
      order: 'default',
      filter: [],
      limit: 10,
      offset: 0,
      hash: false
    };
    hashState = addHashListener('shift_code_', function (hash) {
      shiftProps.hash = hash.replace('#shift_code_', '');
      getCodes();
      shiftProps.hash = false;
    });

    // Initial code listing
    if (!hashState) {
      getCodes();
    }
    // Setup badges & pager
    (function () {
      tryToRun({
        attempts: false,
        delay: 500,
        function: function () {
          if (shiftStats) {
            let id = shiftProps.gameID;

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
