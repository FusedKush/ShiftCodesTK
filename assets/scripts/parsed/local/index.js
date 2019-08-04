function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*********************************
  Index Page Scripts
*********************************/
// *** Variables ***
var indexIsHover = false;
var indexStringScrollInterval;
var indexStringScrollIntervalDelay = 5000;
var addLandingFlagsRetry; // *** Functions ***

function indexGetPrimaryLinks() {
  var active = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  var links = getTags(getClass(getClass(document, 'main'), 'action'), 'a');
  var activeLinks = [];

  for (var _i = 0; _i < links.length; _i++) {
    var link = links[_i];

    if (active === true && link.getAttribute('disabled') === null || active === false) {
      activeLinks.push(link);
    }
  }

  return activeLinks;
}

function indexPrimaryStringScroll() {
  var selected = getClass(getClass(getClass(getClass(document, 'main'), 'action'), 'string'), 'selected');

  var order = function () {
    var links = indexGetPrimaryLinks();
    var array = [];

    for (var _i2 = 0; _i2 < links.length; _i2++) {
      var link = links[_i2];
      var object = {
        'id': '',
        'string': ''
      };
      object.id = link.className.replace('button', '').replace(' ', '');
      object.string = link.getAttribute('data-string');
      array.push(object);
    }

    return array;
  }();

  if (indexIsHover === false) {
    var regex = new RegExp('selected|chosen|\\s', 'g');
    var currentClass = selected.className.replace(regex, '');

    var _loop = function _loop(_i3) {
      if (order[_i3].id == currentClass) {
        var updateSelected = function updateSelected(arrayPos) {
          addClass(selected, 'chosen');
          addClass(selected, order[arrayPos].id);
          selected.innerHTML = order[arrayPos].string;
        };

        selected.className = 'selected';
        setTimeout(function () {
          if (_i3 != order.length - 1) {
            updateSelected(_i3 + 1);
          } else {
            updateSelected(0);
          }
        }, 50);
        return {
          v: void 0
        };
      }
    };

    for (var _i3 = 0; _i3 < order.length; _i3++) {
      var _ret = _loop(_i3);

      if (_typeof(_ret) === "object") return _ret.v;
    }
  }
}

function indexLinkHoverEvent(event) {
  var selected = getClass(getClass(getClass(getClass(document, 'main'), 'action'), 'string'), 'selected');
  var id = this.className.replace('button', '').replace(' ', '');
  var string = this.getAttribute('data-string');
  indexIsHover = id;
  clearInterval(indexStringScrollInterval);

  if (hasClass(selected, id) === false) {
    selected.className = 'selected';
    selected.innerHTML = string;
    setTimeout(function () {
      selected.className = 'selected chosen ' + id;
    }, 50);
  }
}

function indexLinkNoHoverEvent(event) {
  var regex = new RegExp('button|\\s', 'g');
  var id = this.className.replace(regex, '');

  if (indexIsHover == id) {
    indexIsHover = false;
    indexStringScrollInterval = setInterval(indexPrimaryStringScroll, indexStringScrollIntervalDelay);
  }
}

function addLandingFlags() {
  if (typeof shiftBadgeCount != 'undefined') {
    (function () {
      var flags = {
        'template': document.getElementById('flag_template')
      };
      var buttons = getClasses(getTag(document, 'main'), 'button');
      clearInterval(addLandingFlagsRetry);

      for (i = 0; i < buttons.length; i++) {
        var button = buttons[i];
        var regex = new RegExp('button|\\s', 'g');
        var buttonName = button.className.replace(regex, '');

        if (shiftBadgeCount["new"][buttonName] > 0 || shiftBadgeCount.expiring[buttonName] > 0) {
          (function () {
            flags.root = flags.template.content.children[0].cloneNode(true);
            flags["new"] = flags.root.getElementsByClassName('flag new')[0];
            flags.exp = flags.root.getElementsByClassName('flag exp')[0];
          })();

          if (shiftBadgeCount["new"][buttonName] == 0) {
            flags["new"].remove();
          }

          if (shiftBadgeCount.expiring[buttonName] == 0) {
            flags.exp.remove();
          }

          button.appendChild(flags.root);
        }
      }
    })();
  }
} // Immediate Functions & Event Listeners


function execLocalScripts() {
  if (typeof globalFunctionsReady == 'boolean') {
    // Update titles
    (function () {
      var links = indexGetPrimaryLinks(false);

      for (var _i4 = 0; _i4 < links.length; _i4++) {
        var link = links[_i4];
        var longString = link.getAttribute('data-long-string');
        var strToUse = void 0;

        if (link.title == '') {
          if (longString !== null) {
            strToUse = longString;
          } else {
            strToUse = link.getAttribute('data-string');
          }

          updateLabel(link, 'SHiFT Codes for ' + strToUse);
        }
      }
    })(); // Start string scroll


    indexStringScrollInterval = setInterval(indexPrimaryStringScroll, indexStringScrollIntervalDelay); // Create title sections

    (function () {
      var main = getTag(document, 'main');
      var faq = getClass(main, 'faq');
      var links = indexGetPrimaryLinks(false);

      var _loop2 = function _loop2(_i5) {
        var link = links[_i5];
        var regex = new RegExp('button|\\s', 'g');
        var id = link.className.replace(regex, '');
        var shortStr = link.getAttribute('data-string');

        var longStr = function () {
          var str = link.getAttribute('data-long-string');

          if (str !== null) {
            return str;
          } else {
            return shortStr;
          }
        }();

        var newButton = function () {
          var clone = link.cloneNode(true);
          var span = document.createElement('span');
          span.innerHTML = shortStr;
          clone.innerHTML = '';
          clone.appendChild(span);
          return clone;
        }();

        var panel = {};

        (function () {
          panel.base = getTemplate('secondary_section_template');
          panel.bg = JSON.parse(panel.base.getAttribute('data-webp'));
          panel.title = getClass(panel.base, 'title');
          panel.quote = getClass(panel.base, 'quote');
          panel.button = getClass(panel.base, 'button');
        })(); // Section


        addClass(panel.base, id);
        panel.bg.path += id;
        panel.base.setAttribute('data-webp', JSON.stringify(panel.bg)); // Title

        panel.title.innerHTML = shortStr; // Quote

        panel.quote.innerHTML = link.getAttribute('data-quote'); // Button
        // Strip scripting attributes from links

        (function () {
          var attributes = ['data-string', 'data-long-string', 'data-quote'];

          for (var x = 0; x < attributes.length; x++) {
            var attr = attributes[x];

            if (hasAttr(newButton, attr)) {
              newButton.removeAttribute(attr);
            }
          }
        })();

        panel.button.parentNode.replaceChild(newButton, panel.button);
        main.insertBefore(panel.base, faq);
      };

      for (var _i5 = 0; _i5 < links.length; _i5++) {
        _loop2(_i5);
      }

      function tryWebpParse() {
        if (typeof parseWebpImages != 'undefined') {
          parseWebpImages(main);
        } else {
          setTimeout(tryWebpParse, 250);
        }
      }

      tryWebpParse();
    })(); // Link event listeners


    (function () {
      var links = indexGetPrimaryLinks();

      for (var _i6 = 0; _i6 < links.length; _i6++) {
        var link = links[_i6];
        link.addEventListener('mouseover', indexLinkHoverEvent);
        link.addEventListener('mouseout', indexLinkNoHoverEvent);
      }
    })(); // Add landing flags


    addLandingFlagsRetry = setInterval(addLandingFlags, 250);
  } else {
    setTimeout(execLocalScripts, 250);
  }
}

execLocalScripts();