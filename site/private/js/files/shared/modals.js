/**
 * Configure an element for use as a Modal
 * 
 * @param {Element} modal The element to be configured.
 * @returns {boolean} Returns **true** success, or **false** if an error occurred.
 */
function configureModal (modal) {
  /** The configured modal element */
  let configuredModal = edit.copy(dom.find.id('modal_template'));
  /** The possible pieces of the modal */
  let pieces = {
    classes: dom.get(modal, 'class').remove('modal'),
    id: modal.id,
    title: dom.find.child(modal, 'class', 'title'),
    body: dom.find.child(modal, 'class', 'body')
  };

  // Classes
  if (pieces.classes) {
    edit.class(configuredModal, 'add', pieces.classes.toString);
  }
  // ID
  (function () {
    if (pieces.id) {
      configuredModal.id = pieces.id;
    }
    else {
      console.error("configureModal Error: A modal cannot be configured without explicitly passing defining an ID.");
      return false;
    }
  })();
  // Title
  (function () {
    let title = dom.find.child(configuredModal, 'class', 'title');

    if (pieces.title) {  
      title.innerHTML = pieces.title.innerHTML
    }
    else {
      deleteElement(title);
    }
  })();
  // Body
  if (pieces.body) {
    dom.find.child(dom.find.child(configuredModal, 'class', 'body'), 'class', 'content-container').innerHTML = pieces.body.innerHTML;
  }

  modal.parentNode.replaceChild(configuredModal, modal);
  return true;
}
/**
 * Toggle the active state of a given modal
 * 
 * @param {Element} modal The modal to be toggled.
 * @param {boolean|"toggle"} state Indicates the desired *active state* of the modal.
 * - Passing **true** indicates that the modal is to be *enabled* and *visible*.
 * - Passing **false** indicates that the modal is to be *disabled* and *hidden*.
 * - Passing **"toggle"** indicates that the modal is to be toggled between the two *active states*.
 * @returns {boolean} Returns **true** on success, or **false** on failure.
 */
function toggleModal (modal, state = 'toggle') {
  if (!dom.has(modal, 'class', 'modal') || !dom.has(modal, 'class', 'configured')) {
    console.error("toggleModal Error: Provided modal is not a valid modal.");
    return false;
  }
  if (state !== true && state !== false && state != 'toggle') {
    console.error(`toggleModal Error: "${state}" is not a valid value for the "state" argument.`);
    return false;
  }
  
  if (state == 'toggle') {
    state = dom.has(modal, 'class', 'inactive');
  }
  if (state) {
    let allModals = dom.find.children(document.body, 'class', 'modal');

    for (let currentModal of allModals) {
      if (!dom.has(currentModal, 'class', 'inactive') && dom.has(currentModal, 'class', 'configured') && currentModal != modal) {
        toggleModal(currentModal, false);
        break;
      }
    }

    isHidden(modal, false);
    toggleBodyScroll(false);
    dom.find.child(modal, 'class', 'body').scrollTop = 0;
    
    setTimeout(function () {
      edit.class(modal, 'remove', 'inactive');
      focusLock.set(dom.find.child(modal, 'class', 'panel'), function () {
        toggleModal(modal, false);
      });
    }, 50);
  }
  else {
    toggleBodyScroll(true);
    focusLock.clear();
    edit.class(modal, 'add', 'inactive');
    
    setTimeout(function () {
      isHidden(modal, true);
    }, 850);
  }

  return true;
}

// Startup functions
(function () {
  let interval = setInterval(function () {
    if (typeof globalFunctionsReady != 'undefined') {
      clearInterval(interval);

      // Configure present modals
      (function () {
        let modals = dom.find.children(document.body, 'class', 'modal');

        for (let modal of modals) {
          if (!dom.has(modal, 'class', 'configured')) {
            configureModal(modal);
          }
        }
      })();
      // Event Listeners
      window.addEventListener('click', function (event) {
        let target = event.target;

        function checkElement (element) {
          let attrModal = dom.get(element, 'attr', 'data-modal');
          
          if (attrModal) {
            let attrState = dom.get(element, 'attr', 'data-modal-state');
            let modal = dom.find.id(attrModal);
            
            if (modal) {
              return toggleModal(modal, attrState ? attrState == 'true' : 'toggle');
            }
            else {
              console.error('modalClickListener Error: ', element, `points to a missing modal: "${attrModal}"`);
              return false;
            }
          }
          else {
            let parentModal = dom.find.parent(element, 'class', 'modal');

            if (parentModal) {
              return toggleModal(parentModal, false);
            }
          }

          console.error('modalClickListener Error: ', element, 'does not point to and is not a child of a modal.');
          return false;
        }

        if (dom.has(target, 'class', 'modal-toggle')) {
          checkElement(target);
        }
        else {
          let parentToggle = dom.find.parent(target, 'class', 'modal-toggle');

          if (parentToggle) {
            checkElement(parentToggle);
          }
        }
      });
    }
  }, 250);
})();