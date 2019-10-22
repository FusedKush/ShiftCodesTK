var changelogProps = {
  limit: 10,
  offset: 0,
  hash: false,
  firstRun: true
};
var changelogIdPrefix = 'changelog_';

function getChangelogs () {
  let idPrefix = changelogIdPrefix;
  //  ^ Prefix for changelog IDs
  let count = {
    fetched: 0,
    added: 0
  };
  let query = (function () {
    let str = '';
    let propNames = Object.keys(changelogProps);

    for (let i = 0; i < propNames.length; i++) {
      let name = propNames[i];
      let val = changelogProps[name];

      str += `&${name}=${val}`;
    }

    return str.replace('&', '?');
  })();
  let jump = getElement('updates_header_jump');
  let list = document.getElementById('changelog_list');

  function toggleOverlay (overlayIsHidden) {
    let overlay = getElement('changelog_overlay');

    if (overlayIsHidden) {
      addClass(overlay, 'inactive');

      setTimeout(function () {
        isHidden(overlay, true);
      }, 250);
    }
    else {
      isHidden(overlay, false);

      setTimeout(function () {
        delClass(overlay, 'inactive');
      }, 50);
    }
  }
  function clearList () {
    list.innerHTML = '';
  }
  function addChangelog(cl) {
    let changelog = getTemplate('changelog_template');
    let e = {
      icon: getClass(changelog, 'icon').childNodes[0],
      version: getClass(changelog, 'version'),
      date: getClass(changelog, 'date'),
      type: getClass(changelog, 'type'),
      body: getClass(changelog, 'body')
    };
    let ver = cl.version;

    // Properties
    (function () {
      changelog.id = idPrefix + ver;
      changelog.style.animationDelay = `${(count.added * 0.2)}s`;
    })();
    // Header
    (function () {
      let icons = {
        major: 'fa-broadcast-tower',
        minor: 'fa-cogs',
        patch: 'fa-tools'
      };
      //  ^ Changelog Icons
      let type = cl.type.toLowerCase();
      let typeStr = (function () {
        if (type == 'patch') { return cl.type; }
        else                 { return `${cl.type} Update`; }
      })();

      addClass(e.icon, icons[type]);
      updateLabel(e.icon, typeStr);

      e.version.innerHTML = `Version ${ver}`;
      e.date.innerHTML = datetime('month-date-year', cl.date);
      e.type.innerHTML = typeStr;
    })();
    // Body
    (function () {
      let notes = (function () {
        let regexps = {
          // Format Sections
          '(#{3}\\s{1})(?=\\w)': '</ul><h3>',
          '\\s{1}#{3}': '</h3><ul class="styled">',
          // Format Lists
          '-.+': function (match) {
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
        let exps = Object.keys(regexps);
        let n = cl.notes;

        for (let i = 0; i < exps.length; i++) {
          let exp = exps[i];
          let regex = new RegExp(exp, 'g');
          let replacement = regexps[exp];

          n = n.replace(regex, replacement);
        }

        n = n.replace(/<\/ul>/, ''); // Remove First closing List tag
        n += '</ul>';                // Add Final closing List tag
        return n;
      })();

      e.body.innerHTML = notes;
    })();
    // Listeners
    dropdownPanelSetup(changelog);
    // Add to page
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
  function handleResponse (serverResponse) {
    let response = tryJSONParse(serverResponse);

    if (response && response.statusCode == 0) {
      let changelogs = response.payload.changelogs;
          count.fetched = changelogs.length;

      for (let changelog of changelogs) {
        addChangelog(changelog);
      }

      if (changelogProps.firstRun) {
        let vers = response.payload.versions;
        changelogProps.firstRun = false;

        // Setup pager
        (function () {
          let pager = getElement('changelog_pager');

          pager.setAttribute('data-max', Math.ceil((vers.length / changelogProps.limit)));
          pager = configurePager(pager);
          addPagerListeners(pager, function (e) {
            let val = tryParseInt(this.getAttribute('data-value'));

            if (val != changelogProps.offset) {
              changelogProps.offset = val;
              getChangelogs();
            }
          });
        })();
        // Setup Jump dropdown
        (function () {
          let jump, dropdown;
          let template = document.getElementById('changelog_jump_template');

          tryToRun({
            function: function () {
              jump = document.getElementById('updates_header_jump');
              dropdown = document.getElementById('updates_header_jump_dropdown');

              return true;
            }
          });

          for (let ver of vers) {
            let choice = getTemplate(template);
            let link = getTag(choice, 'a');

            link.href += idPrefix + ver;
            updateLabel(link, link.href.replace('[\\d\\.]+', ver));
            link.childNodes[0].innerHTML = `Version ${ver}`;

            getClass(dropdown, 'choice-list').appendChild(choice);
          }

          setupDropdownMenu(dropdown);
          isDisabled(jump, false);
          deleteElm(template);
        })();
      }
    }
    else {
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

  lpbUpdate(90, true, {start: 20});
  isDisabled(jump, true);
  newAjaxRequest({
    file: `assets/php/scripts/getChangelogs${query}`,
    callback: handleResponse
  });
}

(function () {
  let interval = setInterval(function () {
    if (globalFunctionsReady && pagers) {
      clearInterval(interval);

      let currentHashState = addHashListener(changelogIdPrefix, function (hash) {
        changelogProps.offset = 0;
        changelogProps.hash = hash.replace(`#${changelogIdPrefix}`, '');
        getChangelogs();
        changelogProps.hash = false;
      });

      if (!currentHashState) {
        getChangelogs();
      }
    }
  }, 250);
})();
