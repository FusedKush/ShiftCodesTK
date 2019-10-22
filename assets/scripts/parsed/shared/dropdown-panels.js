var defaultDropdownPanelLabels = {
  "false": 'Expand Panel',
  "true": 'Collapse Panel'
}; // Update Dropdown Panel Attributes

function updateDropdownPanelAttributes(panel, state) {
  var toggler = panel.getElementsByClassName('header')[0];

  var labels = function () {
    var customLabels = toggler.getAttribute('data-custom-labels');

    if (customLabels === null) {
      return defaultDropdownPanelLabels;
    } else {
      return JSON.parse(customLabels);
    }
  }();

  panel.setAttribute('data-expanded', state);
  toggler.setAttribute('aria-expanded', state);
  toggler.setAttribute('aria-pressed', state);
  toggler.title = labels[state];
  toggler.setAttribute('aria-label', labels[state]);
} // Add Dropdown Panel Listener


function addDropdownPanelListener(panel) {}
/*
panel.getElementsByClassName('header')[0].addEventListener('click', function (e) { toggleDropdownPanel(this); });
*/
// Set up Dropdown Panel


function dropdownPanelSetup(panel) {
  var hashTargetOverlay = document.createElement('span'); // Requires constructor

  if (hasClass(panel, 'c')) {
    var parent = panel.parentNode;
    var template = {};

    (function () {
      template.base = getTemplate('dropdown_panel_template');
      template.title = getClass(template.base, 'title');
      template.icon = getClass(template.title, 'icon');
      template.primary = getClass(template.title, 'primary');
      template.secondary = getClass(template.title, 'secondary');
      template.body = getClass(template.base, 'body');
    })();

    var props = ['icon', 'primary', 'secondary', 'body'];

    if (panel.id != '') {
      template.base.id = panel.id;
    }

    for (var i = 0; i < props.length; i++) {
      var prop = props[i];
      var val = getClass(panel, prop);

      if (val !== undefined) {
        template[prop].innerHTML = val.innerHTML;
      } else {
        template[prop].parentNode.removeChild(template[prop]);
      }
    }

    delClass(panel, 'c');
    parent.replaceChild(template.base, panel);
    panel = template.base;
  }

  updateDropdownPanelAttributes(panel, false);
  addDropdownPanelListener(panel);
  hashTargetOverlay.className = 'overlay-hashtarget';
  panel.insertBefore(hashTargetOverlay, panel.childNodes[0]);
} // Toggle Dropdown Panel


function toggleDropdownPanel(toggler) {
  var panel = toggler.parentNode;
  var state = panel.getAttribute('data-expanded') == 'true';
  updateDropdownPanelAttributes(panel, !state);
}

(function () {
  var interval = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(interval); // Configure present panels

      (function () {
        var panels = document.getElementsByClassName('dropdown-panel');
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = panels[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var panel = _step.value;

            if (!hasClass(panel, 'no-auto-config')) {
              dropdownPanelSetup(panel);
            }
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
      })(); // Listen for panel clicks


      window.addEventListener('click', function (e) {
        var classname = 'dropdown-panel-toggle';
        var target = e.target;
        var parent = findClass(target, 'up', classname);

        function toggle(id) {
          toggleDropdownMenu(document.getElementById(id));
        }

        if (hasClass(target, classname)) {
          toggleDropdownPanel(target);
        } else if (parent) {
          toggleDropdownPanel(parent);
        }
      });
    }
  }, 250);
})();