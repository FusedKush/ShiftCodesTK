// Multi View Scripts
/*  
  Used to toggle between different *views* in the form of tabs or toggles
*/

/**
 * Configure a Multi View element
 * @param {Element} multiView The element to configure
 * @return {boolean} Returns True on success and False on failure.
 */
function multiView_setup (multiView) {
  try {
    let id = multiView.id ? multiView.id : 'multiview_' + randomNum(100, 1000);
    let views = dom.find.children(multiView, 'class', 'view');
    let type = dom.get(multiView, 'attr', 'data-view-type');
    let controls;
    let container = document.createElement('div');
  
    edit.class(container, 'add', 'views');
    edit.attr(container, 'add', 'id', `${id}_views`);

    if (type == 'tabs') {
      controls = document.createElement('div');
  
      edit.class(controls, 'add', 'controls h-menu');
      edit.attr(controls, 'add', 'id', `${id}_controls`);
    }

    for (let i = views.length - 1; i >= 0; i--) {
      let view = views[i];

      if (multiView == dom.find.parent(view, 'class', 'multi-view')) {
        if (type == 'tabs') {
          let name = dom.get(view, 'attr', 'data-view');        
          let idName = name ? name.toLowerCase().replace(' ', '_') : false;
          let ids = {
            view: idName ? `${id}_view_${idName}` : `${id}_view_${i}`,
            controller: idName ? `${id}_controller_${idName}` : `${id}_controller_${i}`
          };
          let controller = document.createElement('button');
      
          edit.class(controller, 'add', 'view-controller');
          edit.attr(controller, 'add', 'aria-controls', ids.view);
          edit.attr(controller, 'add', 'id', ids.controller);
          controller.innerHTML = `<span>${name}</span>`;
          controls.insertAdjacentElement('afterbegin', controller);
  
          edit.attr(view, 'add', 'aria-owns', ids.controller);
          edit.attr(view, 'add', 'id', ids.view);
          isHidden(view, true);
        }
        else if (type == 'toggle') {
          let toggle = dom.find.child(view, 'class', 'view-toggle');          
          let ids = {
            view: view.id
          };

          if (toggle && !dom.has(toggle, 'tag', 'button')) {
            toggle = dom.find.child(toggle, 'tag', 'button');
          }

          if (toggle) {
            let target = dom.find.id(dom.get(toggle, 'attr', 'data-view'));
                ids.toggle = `${id}_toggle_${i}`;

            edit.attr(toggle, 'add', 'id', ids.toggle);
            
            if (target) {
              ids.target = target.id;

              edit.attr(toggle, 'add', 'aria-controls', ids.target);
              edit.attr(target, 'add', 'aria-owns', ids.toggle);
            }
          }

          isHidden(view, true);
        }

        // container.insertAdjacentElement('afterbegin', view);
      }
    }

    for (let i = views.length - 1; i >= 0; i--) {
      let view = views[i];

      if (dom.find.parent(view, 'class', 'multi-view') == multiView) {
        container.insertAdjacentElement('afterbegin', view);
      }
    }

    if (type == 'tabs') {
      multiView.appendChild(controls);
    }
    
    multiView.appendChild(container);
    multiView_update(dom.find.child(container, 'class', 'view'));
    edit.class(multiView, 'add', 'multi-view-setup');
    return true;
  }
  catch (e) {
    console.error(`multiView Setup failed: ${e}`);
    return false;
  }
}
/**
 * Update and set a new active view
 * 
 * @param {Element} newView The new view element to switch to
 * @return {boolean} Returns True on success and False on failure.
 */
function multiView_update (newView) {
  try {
    let multiView = dom.find.parent(newView, 'class', 'multi-view');
    let type = dom.get(multiView, 'attr', 'data-view-type');
    let oldView = dom.find.id(dom.get(multiView, 'attr', 'data-view'));
        // oldView = oldView ? oldView : false;
  
    if (type == 'tabs') {
      let newController = dom.find.id(dom.get(newView, 'attr', 'aria-owns'));
  
      edit.attr(newController, 'add', 'aria-selected', true);

      if (oldView) {
        let oldController = dom.find.id(dom.get(oldView, 'attr', 'aria-owns'));

        edit.attr(oldController, 'add', 'aria-selected', false);
      }
    }
    if (oldView) {
      isHidden(oldView, true);
      edit.class(oldView, 'remove', 'active');
    }
    isHidden(newView, false);
    
    setTimeout(function () {
      edit.class(newView, 'add', 'active');
      edit.attr(multiView, 'add', 'data-view', newView.id);
    }, 50);
    
    return true;
  }
  catch (e) {
    console.error(`multiView Update failed: ${e}`);
    return false;
  }
}

// Startup
(function () {
  let interval = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(interval);

      // Configure Multi Views
      (function () {
        let multiViews = dom.find.children(document.body, 'class', 'multi-view');

        for (let multiView of multiViews) {
          if (!dom.has(multiView, 'class', 'multi-view-setup')) {
            multiView_setup(multiView);
          }
        }
      })();
      // Listen for tab events
      window.addEventListener('click', function (e) {
        let target = e.target;

        if (dom.has(target, 'class', 'view-controller') || dom.has(target, 'class', 'view-toggle')) {
          let parent = dom.find.parent(target, 'class', 'multi-view');

          if (parent) {
            let currentView = dom.get(parent, 'attr', 'data-view');
            let newView = dom.get(target, 'attr', 'aria-controls');

            if (currentView != newView) {
              multiView_update(dom.find.id(newView));
            }
          }
        }
      });
    }
  }, 250);
})();
