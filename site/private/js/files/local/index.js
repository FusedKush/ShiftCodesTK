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
  let main = dom.find.child(document, 'class', 'main');
  let action = dom.find.child(main, 'class', 'action');
  let links = dom.find.children(action, 'tag', 'a');
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
  let main = dom.find.child(document, 'class', 'main');
  let selected = dom.find.child(main, 'class', 'selected');

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
          edit.class(selected, 'add', 'chosen');
          edit.class(selected, 'add', order[arrayPos].id);
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
  let main = dom.find.child(document, 'class', 'main');
  let selected = dom.find.child(main, 'class', 'selected');
  let id = this.className.replace('button', '').replace(' ', '');
  let string = this.getAttribute('data-string');

  indexIsHover = id;
  clearInterval(indexStringScrollInterval);

  if (dom.has(selected, 'class', id) === false) {
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
(function () {
  let interval = setInterval(function () {
    if (typeof globalFunctionsReady != 'undefined') {
      clearInterval(interval);

      // Start string scroll
      indexStringScrollInterval = setInterval(indexPrimaryStringScroll, indexStringScrollIntervalDelay);
      // Link event listeners
      (function () {
        let links = indexGetPrimaryLinks();

        for (let i = 0; i < links.length; i++) {
          let link = links[i];

          link.addEventListener('mouseover', indexLinkHoverEvent);
          link.addEventListener('mouseout', indexLinkNoHoverEvent);
        }
      })();
    }
  }, 250);
})();
