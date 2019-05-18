/*********************************
  Updates Page Scripts
*********************************/

// *** Immediate Functions ***
// Handles Changelogs' Construction
(function () {
  let count = {
    'retrieved': 0,
    'total': 0
  };

  // Construct the Changelog Panel & add it to the page
  function constructPanel (updateObject) {
    let panel = {}; // Changelog Panel Elements
      (function () {
        panel.template = document.getElementById('panel_template');
        panel.base = panel.template.content.children[0].cloneNode(true);
        panel.header = panel.base.getElementsByClassName('header')[0];
          panel.icon = panel.header.getElementsByClassName('icon')[0].getElementsByClassName('fas')[0];
          panel.version = panel.header.getElementsByClassName('version')[0];
          panel.date = panel.header.getElementsByClassName('info')[0].getElementsByClassName('date')[0];
          panel.type = panel.header.getElementsByClassName('info')[0].getElementsByClassName('type')[0];
        panel.body = panel.base.getElementsByClassName('body')[0];
      })();

    // Handle Panel Properties
    (function () {
      let ver = updateObject.version;
      // Panel ID
      panel.base.id = ('version_') + ver;
      // Panel animation timing
      panel.base.style.animationDelay = (count.total * 0.2) + 's';
    })();
    // Handle Header Properties
    (function () {
      let type = updateObject.type;
      let icons = {
        'Major': 'fa-broadcast-tower',
        'Minor': 'fa-cogs',
        'Patch': 'fa-tools'
      };
      let typeString = (function () {
        if (type != 'Patch') { return type + (' Update'); }
        else                 { return type; }
      })();

      panel.icon.classList.add(icons[type]);
      updateLabel(panel.icon, typeString);
      panel.version.innerHTML = ('Version ') + updateObject.version;
      panel.date.innerHTML = (function () {
        let date = updateObject.date;
        let y = date.substring(0, 4);
        let md = date.substring(5);

        return md + ('-') + y;
      })();
      panel.type.innerHTML = typeString;
    })();
    // Handle Body Properties
    (function () {
      panel.body.innerHTML = (function () {
        let notes = updateObject.notes;

        function updateChanges (match) { return match.replace(/-\s{1}/g, '<li>') + '</li>'; }

        // Replace Markdown with HTML Markup
        // Format Sections
        notes = notes.replace(/(#{3}\s{1})(?=\w)/g, '</ul><h3>');
        notes = notes.replace(/\s{1}#{3}/g, '</h3><ul class="styled">');
        // Format Lists
        notes = notes.replace(/-.*/g, updateChanges);
        // Format Bolded Content
        notes = notes.replace(/\*{2}(?=[\w.])/g, '<strong>');
        notes = notes.replace(/\*{2}(?![\w.])/g, '</strong>');
        // Format Emphasized Content
        notes = notes.replace(/_{1}(?=[\w.])/g, '<em>');
        notes = notes.replace(/_{1}(?![\w.])/g, '</em>');
        // Format Code Content
        notes = notes.replace(/`{1}(?=[\w.])/g, '<code>');
        notes = notes.replace(/`{1}(?![\w.])/g, '</code>');
        // Correct the final output
        notes = notes.replace(/<\/ul>/, ''); // Remove First closing List tag
        notes = notes + ('</ul>');           // Add Final closing List tag

        return notes;
      })();
    })();
    // Handle Panel Event Listeners
    (function () {
      dropdownPanelSetup(panel.base);
    })();
    // Add panel to page
    (function () {
      let main = document.getElementsByTagName('main')[0];

      main.appendChild(panel.base);
      count.total++;

      // Handle Header Updates
      (function () {
        let id = panel.base.id;
        let title = (function () {
          let start = id.slice(0, 1).toUpperCase();
          let parsed = id.replace('_', ' ');

          return start + parsed.slice(1);
        })();
        let current = document.getElementById('updates_header_current');
        let dropdown = document.getElementById('updates_header_jump_dropdown');
        let choiceList = dropdown.getElementsByClassName('choice-list')[0];

        // Update Latest Update field
        if (count.total == 1) {
          let section = current.parentNode.parentNode;

          current.href = ('#') + id;
          current.getElementsByTagName('strong')[0].innerHTML = title.replace('Version ', '');
          updateLabel(current, ('Jump to ') + title + (' Changelog'));

          vishidden(section, false);
          setTimeout(function () { section.removeAttribute('data-hidden'); }, 50);
        }

        // Add link to Jump Button
        (function () {
          let e = {};
            (function () {
              e.li = document.createElement('li');
              e.a = document.createElement('a');
              e.span = document.createElement('span');
            })();

          e.li.setAttribute('role', 'menuitem');
          e.a.className = 'choice internal';
          e.a.href = ('#') + id;
          e.a.setAttribute('data-value', title);
          e.span.innerHTML = title;
          updateLabel(e.a, ('Jump to ') + title + (' Changelog'))

          e.a.appendChild(e.span);
          e.li.appendChild(e.a);
          choiceList.appendChild(e.li);
          choiceList.lastChild.addEventListener('click', function () {
            toggleDropdownMenu(dropdown, true);
          });
        })();
      })();
      if (count.retrieved == count.total) {
        addFocusScrollListeners(main);
        disenable(document.getElementById('updates_header_jump'), false);
        panel.template.remove();
      }
    })();
  }

  // Retrieve updates and add them to the page
  (function () {
    // Wait for dependencies
    function waitForDep() {
      if (typeof newAjaxRequest == 'function') {
        // Fetch changelogs
        newAjaxRequest('GET', 'assets/php/scripts/getChangelogs.php', function (response) {
          let changelogs = JSON.parse(response).response;
              count.retrieved = changelogs.length;

          // Process changelogs
          for (let i = 0; i < count.retrieved; i++) {
            // Construct the panel
            constructPanel(changelogs[i]);
          }
        });
      }
      else { setTimeout(function () { waitForDep(); }, 100); }
    }

    waitForDep();
  })();
})();

// *** Event Listeners ***
