/**
 * Retrieves information about the markup of an element
 * 
 * @param {HTMLElement|Node} element The element to parse.
 * @param {object} markupOptions An object contains options to control the returned markup of nodes:
 * - **inlineNodes** `object`: A list of nodes that should avoid creating newlines.
 * - - **always** `array`: Nodes that will _always_ avoid creating newlines.
 * - - **withoutChildren** `array`: Nodes that will avoid creating newlines _if they don't contain any newlines_.
 * - **contentNodes** `object`: A list of nodes that only include their content and ommitt the tag itself.
 * - - **always** `array`: Nodes that will _always_ return only their content.
 * - - **withoutChildren** `array`: Nodes that will return only their content _if they don't contain any newlines_.
 * - **ignoredNodes** `object`: A list of nodes that will be be ignored and ommitted from the markup.
 * - - **always** `array`: Nodes that will _always_ be ignored.
 * - - **withoutChildren** `array`: Nodes that will be ignored _if they don't contain any newlines_.
 * 
 * @returns {array|false} Returns an `array` of _Node `Objects`_, or **false** if an error occurred.
 * - _Node `Objects`_ are made up of the following properties for each `subNode` of the parent `node`:
 * - - **node** `HTMLElement|Node`: The `node` that was provided.
 * - - **type** `opening|value|closing`: Indicates which type of `subNode` the object is.
 * - - - *opening*: The *opening tag* of the `subNode`.
 * - - - *value*: The *inner content* of the `subNode`.
 * - - - *closing*: The *closing tag* of the `subNode`.
 * - - **markup** `string`: The parsed markup string of the `subNode`. 
 * - - **originalMarkup** `string`: The original, _non-parsed_ markup string of the `subNode`.
 * - - **line** `number`: The line number of the `subNode` in the total markup.
 * - - **depth** `number`: Indicates the nesting depth of the `subNode` in relation to the parent `node`.
 * - - **hasNewline** `boolean`: Indicates if the `subNode` will create a newline.
 * - - **hasNewlineChild** `boolean`: Indicates if the `subNode` contains any children with newLines.
 * - - **parentNode** `HTMLElement`: The parent node of `node`.
 * - - **parentNodeTag** `string`: The _tagName_ or _nodeName_ of the `parentNode`.
 * - - **tag** `string`: The _tagName_ or _nodeName_ of the `subNode`. 
 * - - **class** `array|false`: The _classList_ of the `subNode` or **false** if none is available. 
 * - - **attributes** `array|false`: The _attributeList_ of the `subNode` or **false** if none is available. 
 */
function getNodeMarkup (node, markupOptions = {}) {
  try {
    /** The node markup array */
    let nodeMarkup = [];
    const options = (function () {
      const defaultOptions = {
        inlineNodes: {
          always: [
            'b',
            'code',
            'em',
            'i',
            'input',
            'label',
            'span',
            'strong',
            '#text'
          ],
          withoutChildren: [
            'a',
            'button',
            'li',
          ]
        },
        contentNodes: {
          always: [
            'em',
            'i',
            'strong',
            'b'
          ],
          withoutChildren: [
            'span',
          ]
        },
        ignoredNodes: {
          always: [],
          withoutChildren: []
        },
      };

      return mergeObj(defaultOptions, markupOptions);
    })();
  
    /**
     * Parse a given subNode and add it to the parent markup array
     * 
     * @param {HTMLElement|Node} subNode The element or node to parse.
     * @param {number} depth The depth of the `subNode` relative to the parent `node`.
     * @returns {boolean} Returns **true** if the node will create a newline in the markup, or **false** if it does not. 
     */
    function parseNode (subNode, depth = 0) {
      let subNodeMarkup = [];
      let hasNewlineChild = false;

      /**
       * Generate a new _Node `Object`_ for a piece of the `subNode`
       * 
       * @param {opening|value|closing} type The type of properties that are being added
       * @returns {object} Returns the new _Node `Object`_.
       */
      function addProps (type) {
        let props = {
          /** @property {HTMLElement|node}: The `node` that was provided. */
          node: subNode,
          /** @property {opening|value|closing}: Indicates which type of `subNode` the object is. */
          type: type,
          /** @property {string}: The parsed markup string of the `subNode`. */
          markup: '',
          /** @property {string}: The original, _non-parsed_ markup string of the `subNode`. */
          originalMarkup: '',
          /** @property {number}: The line number of the `subNode` in the total markup. */
          line: 0,
          /** @property {boolean}: Indicates if the `subNode` should create a new line in the markup. */
          hasNewline: false,
          /** @property {boolean}: Indicates if the `subNode` contains any children with newLines. */
          hasNewlineChild: hasNewlineChild,
          /** @property {number}: Indicates the nesting depth of the `subNode` in relation to the parent `node`. */
          depth: depth,
          /** @property {HTMLElement}: The parent node of `node`. */
          parentNode: subNode.parentNode,
          /** @property {string}: The _tagName_ or _nodeName_ of the `parentNode`. */
          parentNodeTag: subNode.parentNode.nodeName.toLowerCase(),
          /** @property {string}: The _tagName_ or _nodeName_ of the `subNode`. */
          tag: subNode.nodeName.toLowerCase(),
          /** @property {array|false}: The _classList_ of the `subNode` or **false** if none is available. */
          classes: false,
          /** @property {array|false}: The _attributeList_ of the `subNode` or **false** if none is available. */
          attributes: false
        };
  
        // Line Number Props
        (function () {
          /** @property {object}: A list of tags that shouldn't create multiple newlines in generated markup. */
          const inlineElements = options.inlineNodes;
          const isBlockNode = (
                                // Subnode is not an inline node
                                inlineElements.always.indexOf(props.tag) == -1
                                && (
                                  // Subnode is not an inline mode without children
                                  inlineElements.withoutChildren.indexOf(props.tag) == -1
                                  // Subnode has children with newlines
                                  || hasNewlineChild
                                )
                              );
          const isParentBlockNode = (
                                      inlineElements.always.indexOf(props.parentNodeTag) == -1
                                      && (
                                        // Subnode is not an inline mode without children
                                        inlineElements.withoutChildren.indexOf(props.parentNodeTag) == -1
                                        // Subnode has children with newlines
                                        || isBlockNode
                                      )
          )

          props.hasNewline = type == 'opening'
                                 && (
                                   // Subnode is main parent node
                                   subNode == node
                                     // Parent is not an inline node
                                   || isParentBlockNode
                                 )
                             || type == 'closing'
                                 && isBlockNode;



          // if (subNode != node && !hasNewlineChild && hasNewline) {
          //   hasNewlineChild = true; 
          // }
        })();
        if (type != 'value') {
          props = mergeObj(props, {
            classes: dom.get(subNode, 'class'),
            attributes: dom.get(subNode, 'attr')
          });
        }
        // Parse markup
        (function () {
          const indentSpaces = 4;
          let subNodeMarkup = '';
  
          if (type != 'value') {
            const pieces = subNode.outerHTML.match(new RegExp('^(<[^<>]+>){1}((?:.|\\r|\\n)*)(<\\/\\w+>){1}$'));
            const pieceMap = {
             opening: 1,
             closing: 3
            };
            
            subNodeMarkup = pieces[pieceMap[type]];
          }
          else {
            subNodeMarkup = subNode.textContent;
          }
  
          props.originalMarkup = subNodeMarkup;

          // Add Indent
          if (props.hasNewline) {
            subNodeMarkup = `${" ".repeat(indentSpaces).repeat(props.depth)}${subNodeMarkup}`;
          }

          // Process HTML Entities
          subNodeMarkup = subNodeMarkup.replace(new RegExp('&nbsp;', 'g'), ' ');
          subNodeMarkup = subNodeMarkup.replace(new RegExp('[\u00A0-\u9999<>\&]', 'gim'), function (match) {
            return `&#${match.charCodeAt(0)};`;
          });
  
          props.markup = subNodeMarkup;
        })()
  
        // nodeMarkup.push(props);

        return props;
      }
  
      if (subNode.tagName) {
        if (subNode.childNodes) {
          for (let child of subNode.childNodes) {
            childNodes = parseNode(child, depth + 1);

            for (let childNode of childNodes) {
              subNodeMarkup.push(childNode);

              if (childNode.hasNewline && !hasNewlineChild) {
                hasNewlineChild = true;
              }
            }
          }
        }
    
        const isContentNode = options.contentNodes.always.indexOf(subNode.nodeName.toLowerCase()) != -1
                              || (
                                options.contentNodes.withoutChildren.indexOf(subNode.nodeName.toLowerCase()) != -1
                                && !hasNewlineChild
                              );
                              
        if (!isContentNode) {
          subNodeMarkup.unshift(addProps('opening'));
          subNodeMarkup.push(addProps('closing'));
        }
      }
      else {
        subNodeMarkup.push(addProps('value'));
      }

      return subNodeMarkup;
    }
  
    nodeMarkup = parseNode(node);
  
    // Update line numbers
    (function () {
      /** The current line number of the markup */
      let cursor = 0;
      
      for (let subNodeMarkup of nodeMarkup) {
        /** The maximum depth of the markup */
        if (subNodeMarkup.hasNewline) {
          subNodeMarkup.line = ++cursor;
        }
        else {
          subNodeMarkup.line = cursor;
        }
      }
    })();

    return nodeMarkup;
  }
  catch (error) {
    console.error(`getNodeMarkup Error: ${error}`);
    return false;
  }
}

function displayNodeMarkup (nodeMarkup) {
  try {
    let codeBlock = (function () {
      let codeBlock = edit.copy(dom.find.id('code_block_template'));
      let totalNodeLines = nodeMarkup[nodeMarkup.length - 1].line;

      if (totalNodeLines >= 10) {
        if (totalNodeLines >= 100) {
          edit.class(codeBlock, 'add', 'long');
        }
        else {
          edit.class(codeBlock, 'add', 'medium');
        }
      } 

      return codeBlock;
    })();
    const presentation = dom.find.child(codeBlock, 'class', 'presentation');
    let copyButton = dom.find.child(codeBlock, 'class', 'copy-to-clipboard');
    let lineTemplate = dom.find.child(codeBlock, 'class', 'line');

    for (let subNode of nodeMarkup) {
      function addContent (contentbox) {
        let wrapper = document.createElement('span');
        let content = dom.find.child(contentbox, 'class', 'line-content');
  
        edit.class(wrapper, 'add', 'sub-node');
        wrapper.innerHTML = subNode.markup;

        if (content) {
          content.appendChild(wrapper);
          // content.innerHTML += subNode.markup;
        }
        else {
          contentbox.appendChild(wrapper);
          // contentbox.innerHTML += subNode.markup;
        }
      }

      if (subNode.hasNewline) {
        let line = edit.copy(lineTemplate);
        let pieces = (function () {
          let pieces = {};
              pieces.number = dom.find.child(line, 'class', 'line-number');
              pieces.lineNumber = dom.find.child(pieces.number, 'class', 'number');
              pieces.fold = dom.find.child(pieces.number, 'class', 'fold');
              pieces.content = dom.find.child(line, 'class', 'line-content');

          return pieces;
        })();

        edit.attr(line, 'update', 'data-line', subNode.line);
        edit.attr(line, 'update', 'data-depth', subNode.depth);
        pieces.lineNumber.innerHTML = subNode.line;
        updateLabel(pieces.lineNumber, `Copy Line ${subNode.line} to the clipboard`, [ 'aria', 'tooltip' ]);
        addContent(pieces.content);

        if (subNode.type == 'opening' && subNode.hasNewlineChild) {
          updateLabel(pieces.fold, `Fold Line`, [ 'aria', 'tooltip' ]);
          edit.class(codeBlock, 'remove', 'single-thread');
        }
        else {
          // deleteElement(pieces.fold);
          isHidden(pieces.fold, true);
          isDisabled(pieces.fold, true);
        }

        // node.appendChild(line);
        presentation.appendChild(line);
        // codeBlock.appendChild(line);

        // Update Copy to Clipboard Button
        if (copyButton.hidden && subNode.line > 1) {
          updateLabel(copyButton, 'Copy Code Block to the clipboard', [ 'aria', 'tooltip' ]);
          isDisabled(copyButton, false);
          isHidden(copyButton, false);
        }
      }
      else {
        addContent(presentation.lastElementChild);
      }

      const content = dom.find.child(codeBlock, 'class', 'copy-content');
      if (subNode.hasNewline) {
        content.innerHTML += '\r\n';
      }

      addContent(content);
    }

    deleteElement(lineTemplate);

    return codeBlock;
  }
  catch (error) {
    console.error(`displayNodeMarkup Error: ${error}`);
    return false;
  }
}

(function () {
  let interval = setInterval(function () {
    if (globalFunctionsReady && typeof ShiftCodesTK != 'undefined' && ShiftCodesTK.profile_card) {
      clearInterval(interval);

      (function () {
        const main = dom.find.child(document.body, 'tag', 'main');
        const groups = dom.find.children(main, 'class', 'group');

        for (let group of groups) {
          const elements = (function () {
            let elements = [];
            const group1 = dom.find.children(group, 'class', 'show-markup');
            const group2 = dom.find.children(group, 'class', 'show-children-markup');

            for (let element of group1) { elements.push(element); }
            for (let element of group2) { elements.push(element); }

            return elements;
          })();

          if (elements.length > 0) {
            // let pre = document.createElement('pre');

            for (let element of elements) {
              // let markup = getElementMarkup(element, 0, true);
              let markupObject = getNodeMarkup(element);
              let markupNode = displayNodeMarkup(markupObject);

              group.appendChild(markupNode);

              // pre.innerHTML += `${markup}\r\n`;
            }

            // group.appendChild(pre);
          }
        }
      })();
      // Event Listeners
      (function () {
        window.addEventListener('click', function (event) {
          // Line indicator
          (function () {
            let line = dom.has(event.target, 'class', 'line');
  
            if (line) {
              line = event.target;
            }
            else {
              let parent = dom.find.parent(event.target, 'class', 'line');
  
              if (parent) {
                line = parent;
              }
            }
  
            if (line) {
              const depth = dom.get(line, 'attr', 'data-depth');
              const codeBlock = dom.find.parent(line, 'class', 'code-block');
  
              if (depth > 0) {
                edit.attr(codeBlock, 'update', 'data-highlight-depth', depth);
              }
            }
          })();
          // CodeBlock Folding
          (function () {
            const fold = (function () {
              if (dom.has(event.target, 'class', 'fold')) {
                return event.target;
              }
              else {
                const parent = dom.find.parent(event.target, 'class', 'fold');

                if (parent) {
                  return parent;
                }
              }

              return false;
            })();

            function getLineDepth (line) {
              const attr = dom.get(line, 'attr', 'data-depth');

              if (attr) {
                return tryParseInt(attr, 'ignore');
              }
              else {
                return false;
              }
            }

            if (fold) {
              const foldLine = dom.find.parent(fold, 'class', 'line');

              if (foldLine) {
                const depth = getLineDepth(foldLine);
                const currentState = dom.has(foldLine, 'class', 'folded');
                const labels = {
                  true: 'Fold Lines',
                  false: 'Unfold Lines'
                };

                if (depth !== false) {
                  edit.class(foldLine, currentState ? 'remove' : 'add', 'folded');
                  updateLabel(fold, labels[currentState], [ 'aria', 'tooltip' ]);

                  // Fold Children 
                  (function () {
                    let line = foldLine.nextElementSibling;

                    while (true) {
                      const lineDepth = getLineDepth(line);
                      const currentLineState = (function () {
                        const attr = dom.get(line, 'attr', 'aria-expanded');
          
                        if (attr) {
                          return attr == 'true';
                        }
                        else {
                          return true;
                        }
                      })();
  
                      if (lineDepth === false || lineDepth <= depth || dom.has(line, 'class', 'folded')) {
                        break;
                      }

                      if (currentState != currentLineState) {
                        edit.attr(line, 'update', 'aria-expanded', currentState.toString());
                      }
  
                      if (line.nextElementSibling) {
                        line = line.nextElementSibling;
                      }
                      else {
                        break;
                      }
                    }
                  })();
                }
              }
            }
          })();
        });
      })();

      ShiftCodesTK.profile_card.get_card_modal('149357043452', (modal) => {
        console.info(modal);
      });
      // const foobar = ShiftCodesTK.profile_card.create_card('149357043452' /** {
      //   user_data: {
      //     id: '149357043452',
      //     username: 'FusedKush',
      //     roles: [
      //       // 'developer',
      //       // 'admin',
      //       'badass'
      //     ]
      //   },
      //   permissions: {
      //     can_report: true
      //   },
      //   profile_stats: {
      //     last_public_activity: '2021-02-01T13:00:00+00:00',
      //     creation_date: '2020-02-01T02:00:00+00:00',
      //     shift_codes_submitted: 288
      //   }
      // } **/,
      // (profile_card) => {
      //   console.log(profile_card);
      //   multiView_setup(dom.find.child(document.body, 'class', 'view profile-cards').appendChild(profile_card));
      // },
      //   // ShiftCodesTK.profile_card.HIDE_BORDER
      //   // |ShiftCodesTK.profile_card.HIDE_USERNAME
      //   // |ShiftCodesTK.profile_card.HIDE_USER_ID
      //   ShiftCodesTK.profile_card.CARD_SHOW_ROLES
      //   |ShiftCodesTK.profile_card.CARD_SHOW_STATS
      //   |ShiftCodesTK.profile_card.CARD_SHOW_ACTIONS
      //   |ShiftCodesTK.profile_card.CARD_ALLOW_EDITING
      // );
    }
  }, 250);
})();