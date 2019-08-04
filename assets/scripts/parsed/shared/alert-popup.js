function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*********************************
  Alert Popup Scripts
*********************************/

/*** Variables ***/
var alertPopups = {};
var alertPopupTimeouts = {};
var alertPopupQueue = {};
/*** Functions ***/

function setAlertPopupTimeout(id) {
  var popup = document.getElementById('alert_popup_' + id);
  var timeouts = {
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
  var popup = function () {
    if (type == 'create') {
      return getTemplate('alert_popup_template');
    } else {
      return document.getElementById('alert_popup_' + id);
    }
  }();

  var feed = document.getElementById('alert_popup_feed');
  var alertPopupsActive = Object.keys(alertPopups).length;

  function updateState(newState) {
    vishidden(popup, !newState);
    popup.setAttribute('data-expanded', newState);
    popup.setAttribute('aria-expanded', newState);
  }

  if (type == 'create') {
    var uniqueID = fillObject.id;

    if (alertPopupsActive <= 2 && alertPopups[uniqueID] === undefined && loadEventFired === true) {
      var _id = alertPopupsActive + 1;

      var icon = getClass(popup, 'icon').getElementsByTagName('span')[0];
      var title = getClass(popup, 'title');
      var description = getClass(popup, 'description');
      var actions = getClass(popup, 'actions');
      var action = getClass(popup, 'action');
      var close = getClass(popup, 'close');
      alertPopups[uniqueID] = _id; // Construct popup

      (function () {
        popup.setAttribute('data-duration', fillObject.duration);
        popup.setAttribute('data-unique-id', uniqueID);
        popup.id = 'alert_popup_' + _id;
        popup.setAttribute('data-banner-id', _id);
        icon.className = fillObject.icon;
        title.innerHTML = fillObject.title;
        description.innerHTML = fillObject.description; // Action button

        if (_typeof(fillObject.action) == 'object') {
          action.href = fillObject.action.href;
          action.innerHTML = fillObject.action.text;
          updateLabel(action, fillObject.action.label);

          if (action.href.indexOf('shiftcodes.tk') == -1) {
            action.setAttribute('rel', 'external noopener');
          }
        } else {
          actions.removeChild(action);
        } // Close button


        if (_typeof(fillObject.close) == 'object') {
          close.innerHTML = fillObject.close.text;
          updateLabel(close, fillObject.close.label);
        }

        popup.getElementsByClassName('close')[0].addEventListener('click', function () {
          updateAlertPopup('destroy', null, _id);
        }); // Reset the timer on hover

        popup.addEventListener('mouseover', function (e) {
          if (this.getAttribute('data-expanded') == 'true' && this.getAttribute('data-duration') != 'inf') {
            clearTimeout(alertPopupTimeouts[_id]);
            popup.setAttribute('data-expiring', false);
          }
        }); // Restart the timer on mouse exit

        popup.addEventListener('mouseout', function (e) {
          if (this.getAttribute('data-expanded') == 'true' && this.getAttribute('data-duration') != 'inf') {
            setAlertPopupTimeout(_id);
          }
        });
      })(); // Add to feed


      (function () {
        updateState(true);
        feed.insertAdjacentElement('afterbegin', popup);
        setTimeout(function () {
          if (popup.getAttribute('data-duration') != 'inf') {
            setAlertPopupTimeout(_id);
          }
        }, 250);
      })();
    } else {
      var duplicate = false;

      if (alertPopupQueue[uniqueID] === undefined) {
        alertPopupQueue[uniqueID] = fillObject;
      }
    }
  } else {
    updateState(false);
    setTimeout(function () {
      var queueKeys = Object.keys(alertPopupQueue);
      delete alertPopups[popup.getAttribute('data-unique-id')];
      feed.removeChild(popup);

      if (queueKeys.length > 0) {
        var key = queueKeys[0];
        setTimeout(function () {
          updateAlertPopup('create', alertPopupQueue[key]);
          delete alertPopupQueue[key];
        }, 500);
      }
    }, 350);
  }
}