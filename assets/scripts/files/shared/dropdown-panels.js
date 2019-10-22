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
  toggler.title = labels[state];
  toggler.setAttribute('aria-label', labels[state]);
}
// Set up Dropdown Panel
function dropdownPanelSetup (panel) {
  let hashTargetOverlay = document.createElement('span');

  // Requires constructor
  if (hasClass(panel, 'c')) {
    let parent = panel.parentNode;
    let template = {};
      (function () {
        template.base = getTemplate('dropdown_panel_template');
        template.title = getClass(template.base, 'title');
          template.icon = getClass(template.title, 'icon');
          template.primary = getClass(template.title, 'primary');
          template.secondary = getClass(template.title, 'secondary');
        template.body = getClass(template.base, 'body');
      })();
      let props = [
        'icon',
        'primary',
        'secondary',
        'body'
      ]

    if (panel.id != '') {
      template.base.id = panel.id;
    }

    for (let i = 0; i < props.length; i++) {
      let prop = props[i];
      let val = getClass(panel, prop);

      if (val !== undefined) {
        template[prop].innerHTML = val.innerHTML;
      }
      else {
        template[prop].parentNode.removeChild(template[prop]);
      }
    }

    delClass(panel, 'c');
    parent.replaceChild(template.base, panel);
    panel = template.base;
  }

  updateDropdownPanelAttributes(panel, false);
  hashTargetOverlay.className = 'overlay-hashtarget';
  panel.insertBefore(hashTargetOverlay, panel.childNodes[0]);
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
          if (!hasClass(panel, 'no-auto-config')) {
            dropdownPanelSetup(panel);
          }
        }
      })();
      // Listen for panel clicks
      window.addEventListener('click', function (e) {
        let classname = 'dropdown-panel-toggle';
        let target = e.target;
        let parent = findClass(target, 'up', classname);

        function toggle (id) {
          toggleDropdownMenu(document.getElementById(id));
        }

        if (hasClass(target, classname)) { toggleDropdownPanel(target); }
        else if (parent)                 { toggleDropdownPanel(parent); }
      });
    }
  }, 250);
})();
