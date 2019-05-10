/*********************************
  FAQ Page Scripts
*********************************/

// *** Immediate Functions ***
// Constructs Dropdown IDs and Table of Contents
(function () {
  let toc = document.getElementById('table_of_contents');
  let groups = document.getElementsByClassName('group');

  function convertToId(name) {
    return name.toLowerCase().replace(/\s/g, '_');
  }
  function updateProperties(element, id, label, name) {
    element.href = '#' + id;
    element.title = label;
    element.setAttribute('aria-label', label);
    element.innerHTML = name;
  }

  for(i = 0; i < groups.length; i++) {
    let group = groups[i];
    let groupName = group.getElementsByTagName('h2')[0].innerHTML;
    let groupID = convertToId(groupName);
    let panels = group.getElementsByClassName('dropdown-panel');
    let tocEntry = {};
      (function () {
        tocEntry.template = document.getElementById('toc_entry_template');
        tocEntry.listitemTemplate = document.getElementById('toc_entry_listitem_template');
        tocEntry.root = tocEntry.template.content.children[0].cloneNode(true);
        tocEntry.title = tocEntry.root.getElementsByTagName('h3')[0].getElementsByTagName('a')[0];
        tocEntry.list = tocEntry.root.getElementsByTagName('ul')[0];
      })();

    group.id = groupID;
    updateProperties(tocEntry.title, groupID, 'Jump to section: "' + groupName + '"', groupName);

    for (x = 0; x < panels.length; x++) {
      let panel = panels[x];
      let panelName = panel.getElementsByTagName('h3')[0].innerHTML;
      let panelID = convertToId(panelName);
      let listitem = {};
        (function () {
          listitem.root = tocEntry.listitemTemplate.content.children[0].cloneNode(true);
          listitem.link = listitem.root.getElementsByTagName('a')[0];
        })();

      panel.id = panelID;
      updateProperties(listitem.link, panelID, 'Jump to question: "' + panelName + '"', panelName);
      tocEntry.list.appendChild(listitem.root);
    }

    toc.appendChild(tocEntry.root);
  }
  tocEntry.template.remove();
  tocEntry.listitemTemplate.remove();
})();
