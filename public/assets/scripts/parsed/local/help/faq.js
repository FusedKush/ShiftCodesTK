/*********************************
  FAQ Page Scripts
*********************************/
// Redeclaration of getClass as functions.js won't be loaded
function getClass(element, className) {
  return element.getElementsByClassName(className)[0];
} // *** Immediate Functions ***
// Constructs Dropdown IDs and Table of Contents


(function () {
  var toc = document.getElementById('table_of_contents');
  var groups = document.getElementsByClassName('faq-group');
  var tocTemplate = document.getElementById('toc_entry_template');
  var listitemTemplate = document.getElementById('toc_entry_listitem_template');

  function convertToId(name) {
    var regex = new RegExp(' ', 'g');
    return name.toLowerCase().replace(regex, '_');
  }

  function updateProperties(element, id, label, name) {
    element.href = '#' + id;
    element.title = label;
    element.setAttribute('aria-label', label);
    element.innerHTML = name;
  }

  var _loop = function _loop(i) {
    var group = groups[i];
    var groupName = getClass(group, 'title').innerHTML;
    var groupID = convertToId(groupName);
    var panels = group.getElementsByClassName('dropdown-panel');
    var tocEntry = {};

    (function () {
      tocEntry.root = tocTemplate.content.children[0].cloneNode(true);
      tocEntry.title = tocEntry.root.getElementsByTagName('h3')[0].getElementsByTagName('a')[0];
      tocEntry.list = tocEntry.root.getElementsByTagName('ul')[0];
    })();

    group.id = groupID;
    updateProperties(tocEntry.title, groupID, 'Jump to section: "' + groupName + '"', groupName);

    var _loop2 = function _loop2(x) {
      var panel = panels[x];
      var panelName = getClass(panel, 'primary').innerHTML;
      var panelID = convertToId(panelName);
      var listitem = {};

      (function () {
        listitem.root = listitemTemplate.content.children[0].cloneNode(true);
        listitem.link = listitem.root.getElementsByTagName('a')[0];
      })();

      panel.id = panelID;
      updateProperties(listitem.link, panelID, 'Jump to question: "' + panelName + '"', panelName);
      tocEntry.list.appendChild(listitem.root);
    };

    for (var x = 0; x < panels.length; x++) {
      _loop2(x);
    }

    toc.appendChild(tocEntry.root);
  };

  for (var i = 0; i < groups.length; i++) {
    _loop(i);
  }

  tocTemplate.remove();
  listitemTemplate.remove();
})();