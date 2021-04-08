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
    let id = multiView.id !== undefined && multiView.id !== '' 
             ? multiView.id 
             : 'multiview_' + randomNum(100, 1000);
    let views = dom.find.children(multiView, 'class', 'view');
    let toggles = dom.find.children(document.body, 'class', 'view-toggle');
    let type = dom.get(multiView, 'attr', 'data-view-type');
    let controls;
    let container = (function () {
      let container = document.createElement('div');
    
      edit.class(container, 'add', 'views');
      edit.attr(container, 'add', 'id', `${id}_views`);
  
      if (type == 'tabs') {
        controls = document.createElement('div');
    
        edit.class(controls, 'add', 'controls h-menu');
        edit.attr(controls, 'add', 'id', `${id}_controls`);
      }

      return container;
    })();

    edit.class(multiView, 'add', 'multi-view-setup');

    for (let i = views.length - 1; i >= 0; i--) {
      let view = views[i];

      if (multiView == dom.find.parent(view, 'class', 'multi-view')) {
        let name = dom.get(view, 'attr', 'data-view');        
        let idName = name ? name.toLowerCase().replace(' ', '_') : false;
        let ids = {
          view: (function () {
            if (view.id) {
              return view.id;
            }
            else if (idName) {
              return `${id}_view_${idName}`;
            }
            else {
              return `${id}_view_${i}`;
            }
          })()
        };

        isHidden(view, true);
        edit.attr(view, 'update', 'id', ids.view);

        if (type == 'tabs') {
          let name = dom.get(view, 'attr', 'data-view');        
          let idName = name ? name.toLowerCase().replace(' ', '_') : false;
          let controller = document.createElement('button');
          
          ids.controller = idName ? `${id}_controller_${idName}` : `${id}_controller_${i}`;
          edit.class(controller, 'add', 'view-controller');
          edit.attr(controller, 'add', 'aria-controls', ids.view);
          edit.attr(controller, 'add', 'id', ids.controller);
          controller.innerHTML = `<span>${name}</span>`;
          controls.insertAdjacentElement('afterbegin', controller);
  
          edit.attr(view, 'add', 'aria-owns', ids.controller);
        }
        else if (type == 'toggle') {
          for (let i = toggles.length - 1; i >= 0; i--) {
            let toggle = toggles[i];
            let ids = {};
      
            if (toggle && !dom.has(toggle, 'tag', 'button')) {
              toggle = dom.find.child(toggle, 'tag', 'button');
            }
      
            let target = dom.find.id(dom.get(toggle, 'attr', 'data-view'));
                ids.toggle = toggle.id !== undefined && toggle.id !== '' ? toggle.id : `${id}_view_toggle_${i}`;
      
            edit.attr(toggle, 'update', 'id', ids.toggle);
            
            if (target) {
              ids.target = target.id !== undefined && target.id !== '' ? target.id : `${id}_view_target_${i}`;

              edit.attr(target, 'update', 'id', ids.target);
      
              (function () {
                const currentControls = dom.get(toggle, 'attr', 'aria-controls');
      
                if (!currentControls || currentControls.indexOf(ids.target) == -1) {
                  edit.attr(toggle, 'list-add', 'aria-controls', ids.target);
                }
              })();
              (function () {
                const currentChildren = dom.get(target, 'attr', 'aria-owns');
      
                if (!currentChildren || currentChildren.indexOf(ids.toggle) == -1) {
                  edit.attr(target, 'list-add', 'aria-owns', ids.toggle);
                }
              })();
            }
            if (toggle) {
            }
          }
          // let toggle = dom.find.child(view, 'class', 'view-toggle');          
          // let ids = {
          //   view: view.id
          // };

          // if (toggle && !dom.has(toggle, 'tag', 'button')) {
          //   toggle = dom.find.child(toggle, 'tag', 'button');
          // }

          // console.log(toggle);
          // if (toggle) {
          //   let target = dom.find.id(dom.get(toggle, 'attr', 'data-view'));
          //       ids.toggle = `${id}_toggle_${i}`;

          //   edit.attr(toggle, 'add', 'id', ids.toggle);
          //   console.log(target);
          //   if (target) {
          //     ids.target = target.id;

          //     edit.attr(toggle, 'list', 'aria-controls', ids.target);
          //     edit.attr(target, 'list', 'aria-owns', ids.toggle);
          //   }
          // }

          // isHidden(view, true);
        }

        // container.insertAdjacentElement('afterbegin', view);
      }
    }

    for (let i = views.length - 1; i >= 0; i--) {
      let view = views[i];

      if (dom.find.parent(view, 'class', 'multi-view') == multiView) {
        views[i] = container.insertAdjacentElement('afterbegin', view);
      }
    }

    if (type == 'tabs') {
      controls = multiView.appendChild(controls);
    }
    
    container = multiView.appendChild(container);
    multiView_update(dom.find.child(container, 'class', 'view'), true);
    return true;
  }
  catch (e) {
    console.error(`multiView Setup failed: ${e}`);
    return false;
  }
}
/**
 * Configure all candidate elements for use as a *Multi View*
 * 
 * @param {Element} parent The parent element who's contents are to be parsed.
 * @return {(array|false)} Returns an `array` made up of the *Multi View ID's* of all configured elements on success. If an error occurrs, returns **false**.
 */ 
function multiView_setup_children (parent) {
  let setupViews = [];
  let multiViews = dom.find.children(parent, 'class', 'multi-view');

  for (let multiView of multiViews) {
    if (!dom.has(multiView, 'class', 'multi-view-setup')) {
      if (multiView_setup(multiView)) {
        setupViews.push(multiView.id);
      }
    }
  }

  if (setupViews) {
    return setupViews;
  }
  
  return false;
}
/**
 * Update and set a new active view
 * 
 * @param {Element} newView The new view element to switch to
 * @param {boolean} skip_transitions Indicates if the *View Transitions* should be skipped for the operation.
 * @return {boolean} Returns True on success and False on failure.
 */
function multiView_update (newView, skip_transitions = false) {
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
    }, !skip_transitions ? 50 : 0);
    
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
    if (typeof globalFunctionsReady != 'undefined') {
      clearInterval(interval);

      // Configure Multi Views
      multiView_setup_children(document.body);
      // Listen for tab events
      window.addEventListener('click', function (e) {
        let target = e.target;
        
        if (dom.has(target, 'class', 'view-controller') || dom.has(target, 'class', 'view-toggle')) {
          let newView = (function () {
            const view = dom.get(target, 'attr', 'aria-controls');

            if (view) {
              const newView = dom.find.id(view);

              if (newView) {
                return newView;
              }
            }

            return false;
          })();

          if (newView) {
            let parent = dom.find.parent(newView, 'class', 'multi-view');
  
            if (parent) {
              let currentView = dom.get(parent, 'attr', 'data-view');
  
              if (currentView != newView) {
                multiView_update(newView);
              }
            }
          }
        }
      });
    }
  }, 250);
})();
