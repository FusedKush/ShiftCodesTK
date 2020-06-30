/*********************************
  FAQ Page Scripts
*********************************/

// Redeclaration of getClass as functions.js won't be loaded
function getClass(element, className) {
  return element.getElementsByClassName(className)[0];
}

// *** Immediate Functions ***
// Constructs Dropdown IDs and Table of Contents
(function () {
  let toc = document.getElementById('table_of_contents');
  let groups = document.getElementsByClassName('faq-group');
  let tocTemplate = document.getElementById('toc_entry_template');
  let listitemTemplate = document.getElementById('toc_entry_listitem_template');

  function convertToId(name) {
    let regex = new RegExp(' ', 'g');

    return name.toLowerCase().replace(regex, '_');
  }
  function updateProperties(element, id, label, name) {
    element.href = '#' + id;
    element.title = label;
    element.setAttribute('aria-label', label);
    element.innerHTML = name;
  }

  for(let i = 0; i < groups.length; i++) {
    let group = groups[i];
    let groupName = getClass(group, 'title').innerHTML;
    let groupID = convertToId(groupName);
    let panels = group.getElementsByClassName('dropdown-panel');
    let tocEntry = {};
      (function () {
        tocEntry.root = tocTemplate.content.children[0].cloneNode(true);
        tocEntry.title = tocEntry.root.getElementsByTagName('h3')[0].getElementsByTagName('a')[0];
        tocEntry.list = tocEntry.root.getElementsByTagName('ul')[0];
      })();

    group.id = groupID;
    updateProperties(tocEntry.title, groupID, 'Jump to section: "' + groupName + '"', groupName);

    for (let x = 0; x < panels.length; x++) {
      let panel = panels[x];
      let panelName = getClass(panel, 'primary').innerHTML;
      let panelID = convertToId(panelName);
      let listitem = {};
        (function () {
          listitem.root = listitemTemplate.content.children[0].cloneNode(true);
          listitem.link = listitem.root.getElementsByTagName('a')[0];
        })();

      panel.id = panelID;
      updateProperties(listitem.link, panelID, 'Jump to question: "' + panelName + '"', panelName);
      tocEntry.list.appendChild(listitem.root);
    }

    toc.appendChild(tocEntry.root);
  }
  tocTemplate.remove();
  listitemTemplate.remove();
})();
