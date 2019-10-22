var changelogProps = {
  limit: 10,
  offset: 0,
  hash: false,
  firstRun: true
};
var changelogIdPrefix = 'changelog_';

function getChangelogs() {
  var idPrefix = changelogIdPrefix;
  var count = {
    fetched: 0,
    added: 0
  };

  var query = function () {
    var str = '';
    var propNames = Object.keys(changelogProps);

    for (var i = 0; i < propNames.length; i++) {
      var name = propNames[i];
      var val = changelogProps[name];
      str += "&".concat(name, "=").concat(val);
    }

    return str.replace('&', '?');
  }();

  var jump = getElement('updates_header_jump');
  var list = document.getElementById('changelog_list');

  function toggleOverlay(overlayIsHidden) {
    var overlay = getElement('changelog_overlay');

    if (overlayIsHidden) {
      addClass(overlay, 'inactive');
      setTimeout(function () {
        isHidden(overlay, true);
      }, 250);
    } else {
      isHidden(overlay, false);
      setTimeout(function () {
        delClass(overlay, 'inactive');
      }, 50);
    }
  }

  function clearList() {
    list.innerHTML = '';
  }

  function addChangelog(cl) {
    var changelog = getTemplate('changelog_template');
    var e = {
      icon: getClass(changelog, 'icon').childNodes[0],
      version: getClass(changelog, 'version'),
      date: getClass(changelog, 'date'),
      type: getClass(changelog, 'type'),
      body: getClass(changelog, 'body')
    };
    var ver = cl.version; // Properties

    (function () {
      changelog.id = idPrefix + ver;
      changelog.style.animationDelay = "".concat(count.added * 0.2, "s");
    })(); // Header


    (function () {
      var icons = {
        major: 'fa-broadcast-tower',
        minor: 'fa-cogs',
        patch: 'fa-tools'
      }; //  ^ Changelog Icons

      var type = cl.type.toLowerCase();

      var typeStr = function () {
        if (type == 'patch') {
          return cl.type;
        } else {
          return "".concat(cl.type, " Update");
        }
      }();

      addClass(e.icon, icons[type]);
      updateLabel(e.icon, typeStr);
      e.version.innerHTML = "Version ".concat(ver);
      e.date.innerHTML = datetime('month-date-year', cl.date);
      e.type.innerHTML = typeStr;
    })(); // Body


    (function () {
      var notes = function () {
        var regexps = {
          // Format Sections
          '(#{3}\\s{1})(?=\\w)': '</ul><h3>',
          '\\s{1}#{3}': '</h3><ul class="styled">',
          // Format Lists
          '-.+': function _(match) {
            return match.replace(/-\s{1}/g, '<li>') + '</li>';
          },
          // Format Bolded Content
          '\\*{2}(?=[\\w.])': '<strong>',
          '\\*{2}(?![\\w.])': '</strong>',
          // Format Emphasized Content
          '_{1}(?=[\\w.])': '<em>',
          '_{1}(?![\\w.])': '</em>',
          // Format Code Content
          '`{1}(?=[\\w.])': '<code>',
          '`{1}(?![\\w.])': '</code>'
        };
        var exps = Object.keys(regexps);
        var n = cl.notes;

        for (var i = 0; i < exps.length; i++) {
          var exp = exps[i];
          var regex = new RegExp(exp, 'g');
          var replacement = regexps[exp];
          n = n.replace(regex, replacement);
        }

        n = n.replace(/<\/ul>/, ''); // Remove First closing List tag

        n += '</ul>'; // Add Final closing List tag

        return n;
      }();

      e.body.innerHTML = notes;
    })(); // Listeners


    dropdownPanelSetup(changelog); // Add to page

    (function () {
      if (count.added == 0) {
        clearList();
      }

      list.appendChild(changelog);
      count.added++;

      if (count.added == 1) {
        toggleOverlay(true);
      }

      if (count.added == count.fetched) {
        addFocusScrollListeners(list);
        hashUpdate();

        if (!changelogProps.firstRun) {
          isDisabled(jump, false);
        }
      }
    })();
  }

  function handleResponse(serverResponse) {
    var response = tryJSONParse(serverResponse);

    if (response && response.statusCode == 0) {
      var changelogs = response.payload.changelogs;
      count.fetched = changelogs.length;
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (var _iterator = changelogs[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          var changelog = _step.value;
          addChangelog(changelog);
        }
      } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion && _iterator["return"] != null) {
            _iterator["return"]();
          }
        } finally {
          if (_didIteratorError) {
            throw _iteratorError;
          }
        }
      }

      if (changelogProps.firstRun) {
        var vers = response.payload.versions;
        changelogProps.firstRun = false; // Setup pager

        (function () {
          var pager = getElement('changelog_pager');
          pager.setAttribute('data-max', Math.ceil(vers.length / changelogProps.limit));
          pager = configurePager(pager);
          addPagerListeners(pager, function (e) {
            var val = tryParseInt(this.getAttribute('data-value'));

            if (val != changelogProps.offset) {
              changelogProps.offset = val;
              getChangelogs();
            }
          });
        })(); // Setup Jump dropdown


        (function () {
          var jump, dropdown;
          var template = document.getElementById('changelog_jump_template');
          tryToRun({
            "function": function _function() {
              jump = document.getElementById('updates_header_jump');
              dropdown = document.getElementById('updates_header_jump_dropdown');
              return true;
            }
          });
          var _iteratorNormalCompletion2 = true;
          var _didIteratorError2 = false;
          var _iteratorError2 = undefined;

          try {
            for (var _iterator2 = vers[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
              var ver = _step2.value;
              var choice = getTemplate(template);
              var link = getTag(choice, 'a');
              link.href += idPrefix + ver;
              updateLabel(link, link.href.replace('[\\d\\.]+', ver));
              link.childNodes[0].innerHTML = "Version ".concat(ver);
              getClass(dropdown, 'choice-list').appendChild(choice);
            }
          } catch (err) {
            _didIteratorError2 = true;
            _iteratorError2 = err;
          } finally {
            try {
              if (!_iteratorNormalCompletion2 && _iterator2["return"] != null) {
                _iterator2["return"]();
              }
            } finally {
              if (_didIteratorError2) {
                throw _iteratorError2;
              }
            }
          }

          setupDropdownMenu(dropdown);
          isDisabled(jump, false);
          deleteElm(template);
        })();
      }
    } else {
      clearList();
      toggleOverlay(false);
      newToast({
        settings: {
          template: 'exception'
        },
        content: {
          body: 'We could not retrieve the changelogs from the server at this time. Please try again later.'
        }
      });
    }

    lpbUpdate(100);
  }

  lpbUpdate(90, true, {
    start: 20
  });
  isDisabled(jump, true);
  newAjaxRequest({
    file: "assets/php/scripts/getChangelogs".concat(query),
    callback: handleResponse
  });
}

(function () {
  var interval = setInterval(function () {
    if (globalFunctionsReady && pagers) {
      clearInterval(interval);
      var currentHashState = addHashListener(changelogIdPrefix, function (hash) {
        changelogProps.offset = 0;
        changelogProps.hash = hash.replace("#".concat(changelogIdPrefix), '');
        getChangelogs();
        changelogProps.hash = false;
      });

      if (!currentHashState) {
        getChangelogs();
      }
    }
  }, 250);
})();