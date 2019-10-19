/*********************************
  Index Page Scripts
*********************************/

// *** Variables ***
var indexIsHover = false;
var indexStringScrollInterval;
var indexStringScrollIntervalDelay = 5000;
var addLandingFlagsRetry;

// *** Functions ***
function indexGetPrimaryLinks (active = true) {
  let links = getTags(getClass(getClass(document, 'main'), 'action'), 'a');
  let activeLinks = [];

  for (let i = 0; i < links.length; i++) {
    let link = links[i];

    if (active === true && link.getAttribute('disabled') === null || active === false) {
      activeLinks.push(link);
    }
  }

  return activeLinks;
}
function indexPrimaryStringScroll () {
  let selected = getClass(getClass(getClass(getClass(document, 'main'), 'action'), 'string'), 'selected');
  let order = (function () {
    let links = indexGetPrimaryLinks();
    let array = [];

    for (let i = 0; i < links.length; i++) {
      let link = links[i];
      let object = {
        'id': '',
        'string': ''
      };

      object.id = link.className.replace('button', '').replace(' ', '');
      object.string = link.getAttribute('data-string');

      array.push(object);
    }

    return array;
  })();
  if (indexIsHover === false) {
    let regex = new RegExp('selected|chosen|\\s', 'g');
    let currentClass = selected.className.replace(regex, '');

    for (let i = 0; i < order.length; i++) {
      if (order[i].id == currentClass) {
        function updateSelected (arrayPos) {
          addClass(selected, 'chosen');
          addClass(selected, order[arrayPos].id);
          selected.innerHTML = order[arrayPos].string;
        }

        selected.className = 'selected';
        setTimeout(function () {
          if (i != (order.length - 1)) { updateSelected(i + 1); }
          else                         { updateSelected(0); }
        }, 50);
        return;
      }
    }
  }
}
function indexLinkHoverEvent (event) {
  let selected = getClass(getClass(getClass(getClass(document, 'main'), 'action'), 'string'), 'selected');
  let id = this.className.replace('button', '').replace(' ', '');
  let string = this.getAttribute('data-string');

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
function indexLinkNoHoverEvent (event) {
  let regex = new RegExp('button|\\s', 'g');
  let id = this.className.replace(regex, '');

  if (indexIsHover == id) {
    indexIsHover = false;
    indexStringScrollInterval = setInterval(indexPrimaryStringScroll, indexStringScrollIntervalDelay);
  }
}

// Immediate Functions & Event Listeners
function execLocalScripts () {
  if (typeof globalFunctionsReady == 'boolean') {
    // Update titles
    (function () {
      let links = indexGetPrimaryLinks(false);

      for (let i = 0; i < links.length; i++) {
        let link = links[i];
        let longString = link.getAttribute('data-long-string');
        let strToUse;

        if (link.title == '') {
          if (longString !== null) { strToUse = longString; }
          else                     { strToUse = link.getAttribute('data-string'); }

          updateLabel(link, 'SHiFT Codes for ' + strToUse);
        }
      }
    })();
    // Start string scroll
    indexStringScrollInterval = setInterval(indexPrimaryStringScroll, indexStringScrollIntervalDelay);
    // Create title sections
    (function () {
      let main = getTag(document, 'main');
      let faq = getClass(main, 'faq');
      let links = indexGetPrimaryLinks(false);

      for (let i = 0; i < links.length; i++) {
        let link = links[i];
        let regex = new RegExp('button|\\s', 'g');
        let id = link.className.replace(regex, '');
        let shortStr = link.getAttribute('data-string');
        let longStr = (function () {
          let str = link.getAttribute('data-long-string');

          if (str !== null) { return str; }
          else              { return shortStr; }
        })();
        let newButton = (function () {
          let clone = link.cloneNode(true);
          let span = document.createElement('span');

          span.innerHTML = shortStr;
          clone.innerHTML = '';
          clone.appendChild(span);

          return clone;
        })();
        let panel = {};
          (function () {
            panel.base = getTemplate('secondary_section_template');
            panel.bg = JSON.parse(panel.base.getAttribute('data-webp'));
            panel.title = getClass(panel.base, 'title');
            panel.quote = getClass(panel.base, 'quote');
            panel.button = getClass(panel.base, 'button');
          })();

        // Section
        addClass(panel.base, id);
        panel.bg.path += id;
        panel.base.setAttribute('data-webp', JSON.stringify(panel.bg));
        // Title
        panel.title.innerHTML = shortStr;
        // Quote
        panel.quote.innerHTML = link.getAttribute('data-quote');
        // Button
        // Strip scripting attributes from links
        (function () {
          let attributes = ['data-string', 'data-long-string', 'data-quote'];

          for (let x = 0; x < attributes.length; x++) {
            let attr = attributes[x];

            if (hasAttr(newButton, attr)) {
              newButton.removeAttribute(attr);
            }
          }
        })();
        panel.button.parentNode.replaceChild(newButton, panel.button);

        main.insertBefore(panel.base, faq);
      }

      function tryWebpParse () {
        if (typeof parseWebpImages != 'undefined') {
          parseWebpImages(main);
        }
        else {
          setTimeout(tryWebpParse, 250);
        }
      }
      tryWebpParse();
    })();
    // Link event listeners
    (function () {
      let links = indexGetPrimaryLinks();

      for (let i = 0; i < links.length; i++) {
        let link = links[i];

        link.addEventListener('mouseover', indexLinkHoverEvent);
        link.addEventListener('mouseout', indexLinkNoHoverEvent);
      }
    })();
    // Add landing flags
    tryToRun({
      attempts: false,
      delay: 250,
      function: function () {
        if (shiftStats) {
          let flags = {
            'template': document.getElementById('flag_template')
          };
          let buttons = getClasses(getTag(document, 'main'), 'button');

          for (i = 0; i < buttons.length; i++) {
            let button = buttons[i];
            let regex = new RegExp('button|\\s', 'g');
            let name = button.className.replace(regex, '');
            let n = shiftStats.new[name];
            let e = shiftStats.expiring[name];

            if (n > 0 || e > 0) {
                (function () {
                  flags.root = flags.template.content.children[0].cloneNode(true);
                  flags.new = flags.root.getElementsByClassName('flag new')[0];
                  flags.exp = flags.root.getElementsByClassName('flag exp')[0];
                })();

              if (n == 0) { flags.new.remove(); }
              if (e == 0) { flags.exp.remove(); }

              button.appendChild(flags.root);
            }
          }

          return true;
        }
        else {
          return false;
        }
      }
    });
  }
  else {
    setTimeout(execLocalScripts, 250);
  }
}
execLocalScripts();
