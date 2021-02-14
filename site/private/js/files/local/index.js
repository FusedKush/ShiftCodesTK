(function () {
  let interval = setInterval(function () {
    if (typeof globalFunctionsReady != 'undefined' && typeof ShiftCodesTK.shift != 'undefined') {
      clearInterval(interval);

      /**
       * Properties and methods related to the index page scripts.
       */
      ShiftCodesTK.local.index = {
        /** The actions box in the main strip. */
        actions: [],
        /** The current state of the button hover. The _ID_ of the button if active, and **false** if inactive. */
        hoverState: false,
        /** Properties related to the _Scroll Interval_ */
        interval: {
          /** The delay in ms between intervals. */
          delay: 5000,
          /** The _Interval ID_ of the interval. */
          id: 0,
          /** Start the scroll interval */
          start () {
            return this.id = setInterval(ShiftCodesTK.local.index.nextLink, this.delay);
          },
          /** Halt the scroll interval */
          stop () {
            return clearInterval(this.id);
          }
        },
        /**
         * The list of button links. Each link contains an `Object` of the following format: 
         * - **link**: The button link element.
         * - **id**: The **Game ID** of the button link
         * - **string**: The **Game String** of the button link.  */
        links: [],
        /**
         * Retrieves the Game ID of an action link or the selected string.
         * 
         * @param {HTMLLinkElement|HTMLSpanElement} element The link to check or the selected string.
         * @returns {string} Returns the _Game ID_ of the provided button or string.
         */
        getLinkID (element) {
          const gameList = Object.keys(ShiftCodesTK.shift.games);
          const gameSearch = new RegExp(gameList.join('|'));

          return element.className.match(gameSearch)[0];
        },
        /**
         * Set the new link to be selected.
         * 
         * @param {string} gameID The _Game ID_ of the link button to be selected.
         */
        setSelectedLink (gameID) {
          const local = ShiftCodesTK.local.index;
          const selected = dom.find.child(local.actions, 'class', 'selected');
          const link = (function () {
            for (let link of local.links) {
              if (link.id == gameID) {
                return link;
              }
            }
          })();

          selected.className = 'selected'; // Reset classes

          setTimeout(function () {
            edit.class(selected, 'add', link.id);
            edit.class(selected, 'add', 'chosen');
            selected.innerHTML = link.string;
          }, 50);
        },
        /**
         * Move the highlight to the next link in the list.
         * 
         * @returns {boolean} Returns **true** if the highlight was moved, or **false** if it was not.
         */
        nextLink () {
          const local = ShiftCodesTK.local.index;

          if (local.hoverState === false) {
            const selected = dom.find.child(local.actions, 'class', 'selected');
            const currentID = local.getLinkID(selected);

            for (let pos = 0; pos < local.links.length; pos++) {
              let link = local.links[pos];

              if (link.id == currentID) {
                const newPos = pos != (local.links.length - 1)
                            ? ++pos
                            : 0;
                const newLink = local.links[newPos];

                local.setSelectedLink(newLink.id);

                break;
              }
            }
          }

          return false;
        },
        /**
         * Handle a button link hover event
         * 
         * @param {Event} event The _mouseover_ or _mouseout_ event that occurred. 
         */
        linkEvent (event) {
          const local = ShiftCodesTK.local.index;

          if (dom.has(event.target, 'class', 'button') && !dom.has(event.target, 'attr', 'disabled') && dom.find.parent(event.target, 'class', 'actions-container') !== false) {
            const id = local.getLinkID(event.target);

            if (event.type == 'mouseover') {
              const selected = dom.find.child(local.actions, 'class', 'selected');
          
              local.hoverState = id;
              local.interval.stop();
          
              if (!dom.has(selected, 'class', id)) {
                local.setSelectedLink(id);
              }
            }
            else if (event.type == 'mouseout') {
              if (local.hoverState == id) {
                local.hoverState = false;
                local.interval.start();
              }
            }
          }
        }
      };

      // Startup
      (function () {
        const local = ShiftCodesTK.local.index;

        // Setup action & links properties
        local.actions = dom.find.child(document.body, 'class', 'actions-container');
        local.links = (function () {
          const links = dom.find.children(local.actions, 'tag', 'a');
          let validLinks = [];
      
          for (let link of links) {
            if (!dom.has(link, 'attr', 'disabled')) {
              validLinks.push({
                link: link,
                id: local.getLinkID(link),
                string: dom.get(link, 'attr', 'data-string')
              });
            }
          }
      
          return validLinks;
        })();
  
        // Begin highlight interval
        local.interval.start();
        // Event Listeners
        window.addEventListener('mouseover', local.linkEvent);
        window.addEventListener('mouseout', local.linkEvent);
      })();
    }
  }, 250);
})();

