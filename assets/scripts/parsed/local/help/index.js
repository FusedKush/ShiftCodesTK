/*********************************
  Help Center Page Scripts
*********************************/
// *** Variables ***
var helpCenterArticles = [{
  'title': 'FAQ',
  'description': 'Answers to some frequently asked questions',
  'link': 'faq'
}, {
  'title': 'Clearing your System Cache',
  'description': 'How to clear your system cache on PC, Xbox, and Playstation',
  'link': 'clearing-your-system-cache'
}]; // *** Immediate Functions ***
// Constructs Article Links

(function () {
  var defaultIcon = 'fas fa-file-alt';
  var template = document.getElementById('article_template');
  var container = document.getElementById('article_container');

  var _loop = function _loop() {
    var link = {};

    (function () {
      link.root = template.content.children[0].cloneNode(true);
      link.icon = link.root.getElementsByClassName('icon')[0].getElementsByTagName('span')[0];
      link.title = link.root.getElementsByClassName('title')[0];
      link.description = link.root.getElementsByClassName('description')[0];
    })();

    var current = helpCenterArticles[i];

    if (typeof current.icon == 'undefined') {
      link.icon.className = defaultIcon;
    } else {
      link.icon.className = current.icon;
    }

    link.root.href = '/help/' + current.link;
    link.title.innerHTML = current.title;
    link.description.innerHTML = current.description;
    container.appendChild(link.root);
  };

  for (i = 0; i < helpCenterArticles.length; i++) {
    _loop();
  }

  template.parentNode.removeChild(template);
})();