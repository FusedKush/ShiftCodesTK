/*********************************
  Alert Popup Scripts
*********************************/
/*** Variables ***/
var alertPopups = {};
var alertPopupTimeouts = {};
var alertPopupQueue = {};

/*** Functions ***/
function setAlertPopupTimeout (id) {
  let popup = document.getElementById('alert_popup_' + id);
  let timeouts = {
    'short': 2500,
    'medium': 5000,
    'long': 7500
  };

  alertPopupTimeouts[id] = setTimeout(function () {
    updateAlertPopup('destroy', null, id);
  }, timeouts[popup.getAttribute('data-duration')]);
  popup.setAttribute('data-expiring', true);
}
function updateAlertPopup(type, fillObject, id) {
  let popup = (function () {
    if (type == 'create') { return getTemplate('alert_popup_template'); }
    else                  { return document.getElementById('alert_popup_' + id); }
  })();
  let feed = document.getElementById('alert_popup_feed');
  let alertPopupsActive = Object.keys(alertPopups).length;

  function updateState (newState) {
    popup.setAttribute('data-expanded', newState);
    popup.setAttribute('aria-expanded', newState);
  }

  if (type == 'create') {
    let uniqueID = fillObject.id;

    if (alertPopupsActive <= 2 && alertPopups[uniqueID] === undefined) {
      let id = (alertPopupsActive + 1);
      let icon = getClass(popup, 'icon').getElementsByTagName('span')[0];
      let title = getClass(popup, 'title');
      let description = getClass(popup, 'description');
      let actions = getClass(popup, 'actions');
      let action = getClass(popup, 'action');
      let close = getClass(popup, 'close');

      alertPopups[uniqueID] = id;

      // Construct popup
      (function () {
        popup.setAttribute('data-duration', fillObject.duration);
        popup.setAttribute('data-unique-id', uniqueID);
        popup.id = 'alert_popup_' + id;
        popup.setAttribute('data-banner-id', id);
        icon.className = fillObject.icon;
        title.innerHTML = fillObject.title;
        description.innerHTML = fillObject.description;

        // Action button
        if (typeof fillObject.action == 'object') {
          action.href = fillObject.action.href;
          action.innerHTML = fillObject.action.text;
          updateLabel(action, fillObject.action.label);

          if (action.href.indexOf('shiftcodes.tk') == -1) {
            action.setAttribute('rel', 'external noopener');
          }
        }
        else {
          actions.removeChild(action);
        }
        // Close button
        if (typeof fillObject.close == 'object') {
          close.innerHTML = fillObject.close.text;
          updateLabel(close, fillObject.close.label);
        }
        popup.getElementsByClassName('close')[0].addEventListener('click', function () {
          updateAlertPopup('destroy', null, id);
        });

        // Reset the timer on hover
        popup.addEventListener('mouseover', function (e) {
          if (this.getAttribute('data-expanded') == 'true' && this.getAttribute('data-duration') != 'inf') {
            clearTimeout(alertPopupTimeouts[id]);
            popup.setAttribute('data-expiring', false);
          }
        });
        // Restart the timer on mouse exit
        popup.addEventListener('mouseout', function (e) {
          if (this.getAttribute('data-expanded') == 'true' && this.getAttribute('data-duration') != 'inf') {
            setAlertPopupTimeout(id);
          }
        });
      })();
      // Add to feed
      (function () {
        feed.appendChild(popup);

        setTimeout(function() {
          updateState(true);

          setTimeout(function () {
            vishidden(popup, false);
          }, 125);

          setTimeout(function() {
            if (popup.getAttribute('data-duration') != 'inf') {
              setAlertPopupTimeout(id);
            }
          }, 125);
        }, 50);
      })();
    }
    else {
      let duplicate = false;

      if (alertPopupQueue[uniqueID] === undefined) {
        alertPopupQueue[uniqueID] = fillObject;
      }
    }
  }
  else {
    updateState(false);

    setTimeout(function () {
      let queueKeys = Object.keys(alertPopupQueue);

      delete alertPopups[popup.getAttribute('data-unique-id')];
      feed.removeChild(popup);

      if (queueKeys.length > 0) {
        let key = queueKeys[0];

        setTimeout(function () {
          updateAlertPopup('create', alertPopupQueue[key]);
          delete alertPopupQueue[key];
        }, 500);
      }
    }, 350);
  }
}
