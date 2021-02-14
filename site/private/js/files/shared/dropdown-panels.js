var defaultDropdownPanelLabels = {
  false: 'Expand Panel',
  true: 'Collapse Panel'
};

// Update Dropdown Panel Attributes
function updateDropdownPanelAttributes (panel, state) {
  let toggler = panel.getElementsByClassName('header')[0];
  let labels = (function () {
    let customLabels = toggler.getAttribute('data-custom-labels');

    if (customLabels === null) { return defaultDropdownPanelLabels; }
    else                       { return JSON.parse(customLabels); }
  })();

  panel.setAttribute('data-expanded', state);
  toggler.setAttribute('aria-expanded', state);
  toggler.setAttribute('aria-pressed', state);
  // toggler.title = labels[state];
  // toggler.setAttribute('aria-label', labels[state]);
  updateLabel(toggler, labels[state], [ 'aria' ]);
}
// Set up Dropdown Panel
function dropdownPanelSetup (panel) {
  let hashTargetOverlay = document.createElement('span');

  // Requires constructor
  if (dom.has(panel, 'class', 'c')) {
    let parent = panel.parentNode;
    let template = {};
      (function () {
        template.base = edit.copy(dom.find.id('dropdown_panel_template'));
        template.title = dom.find.child(template.base, 'class', 'title');
          template.icon = dom.find.child(template.title, 'class', 'icon');
          template.primary = dom.find.child(template.title, 'class', 'primary');
          template.secondary = dom.find.child(template.title, 'class', 'secondary');
        template.body = dom.find.child(template.base, 'class', 'body');
      })();
      let props = [
        'icon',
        'primary',
        'secondary',
        'body'
      ];

    if (panel.id != '') {
      template.base.id = panel.id;
    }

    for (let i = 0; i < props.length; i++) {
      let prop = props[i];
      let val = dom.find.child(panel, 'class', prop);

      if (val && val.parentNode == panel) {
        template[prop].innerHTML = val.outerHTML;
      }
      else {
        template[prop].parentNode.removeChild(template[prop]);
      }
    }

    // Update template
    (function () {
      let newBase = document.createElement(dom.get(panel, 'tag'));
          edit.class(newBase, 'add', panel.className);
          edit.class(newBase, 'add', template.base.className);
          newBase.innerHTML = template.base.innerHTML;

      edit.class(newBase, 'remove', 'c');

      template.base = newBase;
      parent.replaceChild(template.base, panel);
      panel = template.base;
    })();
  }

  updateDropdownPanelAttributes(panel, false);
  hashTargetOverlay.className = 'overlay-hashtarget';
  panel = panel.insertBefore(hashTargetOverlay, panel.childNodes[0]);

  return panel;
}
// Toggle Dropdown Panel
function toggleDropdownPanel (toggler) {
  let panel = toggler.parentNode;
  let state = panel.getAttribute('data-expanded') == 'true';

  updateDropdownPanelAttributes(panel, !state);
}

(function () {
  let interval = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(interval);

      // Configure present panels
      (function () {
        let panels = document.getElementsByClassName('dropdown-panel');

        for (let panel of panels) {
          if (!dom.has(panel, 'class', 'no-auto-config')) {
            dropdownPanelSetup(panel);
          }
        }
      })();
      // Listen for panel clicks
      window.addEventListener('click', function (e) {
        let classname = 'dropdown-panel-toggle';
        let target = e.target;
        let parent = dom.find.parent(target, 'class', classname);

        function toggle (id) {
          toggleDropdownMenu(document.getElementById(id));
        }

        if (dom.has(target, 'class', classname)) { toggleDropdownPanel(target); }
        else if (parent)                 { toggleDropdownPanel(parent); }
      });
    }
  }, 250);
})();
