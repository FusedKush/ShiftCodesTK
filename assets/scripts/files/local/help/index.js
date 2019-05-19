/*********************************
  Help Center Page Scripts
*********************************/
// *** Variables ***
var helpCenterArticles = [
  {
    'title': 'FAQ',
    'description': 'Answers to some frequently asked questions',
    'link': 'faq'
  },
  {
    'title': 'Clearing your System Cache',
    'description': 'How to clear your system cache on PC, Xbox, and Playstation',
    'link': 'clearing-your-system-cache'
  },
  /*
  {
    'title': 'Supported Devices & Browsers',
    'description': 'Devices and Browsers supported by ShiftCodesTK',
    'link': 'supported-devices-and-browsers'
  }
  */
];

// *** Immediate Functions ***
// Constructs Article Links
(function () {
  let defaultIcon = 'fas fa-file-alt';
  let template = document.getElementById('article_template');
  let container = document.getElementById('article_container');

  for(i = 0; i < helpCenterArticles.length; i++) {
    let link = {};
      (function () {
        link.root = template.content.children[0].cloneNode(true);
        link.icon = link.root.getElementsByClassName('icon')[0].getElementsByTagName('span')[0];
        link.title = link.root.getElementsByClassName('title')[0];
        link.description = link.root.getElementsByClassName('description')[0];
      })();
    let current = helpCenterArticles[i];

    if (typeof current.icon == 'undefined') { link.icon.className = defaultIcon; }
    else                                    { link.icon.className = current.icon; }

    link.root.href = ('/help/') + current.link;
    link.title.innerHTML = current.title;
    link.description.innerHTML = current.description;
    container.appendChild(link.root);
  }

  template.remove();
})();
