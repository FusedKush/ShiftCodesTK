/*********************************
  Footer Scripts
*********************************/

// *** Immediate Functions ***
// Update Breadcrumbs
(function () {
  let breadcrumbs = (function () {
    let meta = document.getElementById('breadcrumbs');

    if (meta !== null) { return JSON.parse(meta.content); }
    else               { return null; }
  })();
  let container = document.getElementById('breadcrumb_container');
  let separatorTemplate = document.getElementById('breadcrumb_separator_template');
  let crumbTemplate = document.getElementById('breadcrumb_crumb_template');

  if (breadcrumbs !== null) {
    // Root Page
    (function () {
      let crumb = crumbTemplate.content.children[0].cloneNode(true);

      crumb.href = '/';
      crumb.innerHTML = 'Home';
      updateLabel(crumb, 'Home');

      container.appendChild(crumb);
    })();

    for (i = 0; i < breadcrumbs.length; i++) {
      let current = breadcrumbs[i];
      let separator = separatorTemplate.content.children[0].cloneNode(true);
      let crumb;

      if ((i + 1) != breadcrumbs.length) {
        crumb = crumbTemplate.content.children[0].cloneNode(true);

        crumb.href = current.url;
        updateLabel(crumb, current.name);
        crumb.innerHTML = current.name;
      }
      else {
        crumb = document.createElement('b');

        crumb.className = 'crumb';
        crumb.innerHTML = current.name;
      }

      container.appendChild(separator);
      container.appendChild(crumb);
    }
  }
  else {
    container.remove();
  }

  separatorTemplate.remove();
  crumbTemplate.remove();
})();

// *** Event Listeners ***
// Remove focus when returning to top
document.getElementById('footer_return').addEventListener('click', function (e) { this.blur(); });
