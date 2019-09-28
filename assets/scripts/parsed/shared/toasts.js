// Toast global registry
var toasts = {
  ready: false,
  active: {},
  queue: {}
};

function toastTimeout(toast, type) {
  var n = toast.id;

  if (type == 'set') {
    addClass(toast, 'expiring');
    toasts.active[n].timeout = setTimeout(function () {
      toastTimeout(toast, 'expired');
    }, toasts.active[n].duration);
  } else {
    clearTimeout(toasts.active[n].timeout);

    if (type == 'remove') {
      delClass(toast, 'expiring');
    } else if (type == 'expired') {
      removeToast(toast);
    }
  }
}

function newToast(properties) {
  var defaultProps = {
    settings: {
      id: 'toast',
      duration: 'medium',
      template: 'none'
    },
    content: {
      icon: 'fas fa-bullhorn',
      title: 'Toast',
      body: 'This is a toast notification.'
    },
    action: {
      use: false,
      type: 'link',
      link: '#',
      name: 'Action',
      label: 'The Action button'
    },
    close: {
      use: true,
      type: 'button',
      name: 'Close',
      label: 'Close the toast'
    }
  };
  var templates = {};

  var templateProps = function () {
    if (properties.template && properties.template !== 'none' && properties.templates[template] !== undefined) {
      return templates[template];
    } else {
      return {};
    }
  }();

  var props = mergeObj(defaultProps, templateProps, properties);
  var toast = getTemplate('toast_template');
  var settings = {
    id: props.settings.id,
    duration: function () {
      var d = props.settings.duration;
      var vals = {
        "short": 2500,
        medium: 5000,
        "long": 7500
      };

      if (vals[d] !== undefined) {
        return vals[d];
      } else {
        return d;
      }
    }()
  };
  var e = {
    progress: getClass(getClass(toast, 'progress-bar'), 'progress'),
    icon: getClass(toast, 'icon').childNodes[0],
    title: getClass(toast, 'title'),
    body: getClass(toast, 'body'),
    actions: getClass(toast, 'actions')
  };
  var list = document.getElementById('toast_list');
  var ids = {
    title: "".concat(settings.id, "_title"),
    body: "".concat(settings.id, "_body")
  };

  function addAction(type, actionProps) {
    var btn = function () {
      if (actionProps.type == 'button') {
        return document.createElement('button');
      } else {
        return document.createElement('a');
      }
    }();

    var classes = {
      button: 'styled',
      link: 'button'
    };
    addClass(btn, type);
    addClass(btn, classes[actionProps.type]);
    btn.innerHTML = actionProps.name;
    updateLabel(btn, actionProps.label);

    if (actionProps.type == 'link') {
      btn.href = actionProps.link;
    }

    e.actions.appendChild(btn);
    return btn;
  } // Properties


  toast.id = settings.id;
  e.progress.style.animationDuration = "".concat(settings.duration, "ms"); // Content

  addClass(e.icon, props.content.icon);
  e.title.innerHTML = props.content.title;
  e.title.id = ids.title;
  toast.setAttribute('aria-labelledby', ids.title);
  e.body.innerHTML = props.content.body;
  e.body.id = ids.body;
  toast.setAttribute('aria-describedby', ids.body); // Actions

  if (props.action.use) {
    addAction('action', props.action);
  }

  if (props.close.use) {
    var close = addAction('close', props.close);
    close.setAttribute('aria-controls', settings.id);
    close.addEventListener('click', function () {
      removeToast(toast);
    });
  } // Timeout listeners


  if (settings.duration != 'infinite') {
    toast.addEventListener('mouseover', toastEvent);
    toast.addEventListener('mouseout', toastEvent);
    toast.addEventListener('click', toastEvent);
  } // Add toast


  return function () {
    var n = settings.id;

    if (toasts.active[n] === undefined && Object.keys(toasts.active).length <= 3 && toasts.ready) {
      addToast(toast, settings);
      return toast;
    } else if (toasts.queue[n] === undefined) {
      toasts.queue[n] = {
        toast: toast,
        settings: settings
      };
      return toast;
    } else {
      return false;
    }
  }();
}

function addToast(toast, settings) {
  var n = settings.id;
  toasts.active[n] = settings;
  document.getElementById('toast_list').appendChild(toast);
  vishidden(toast, false);
  setTimeout(function () {
    if (settings.duration != 'infinite') {
      toastTimeout(toast, 'set');
    }
  }, 300);
}

function removeToast(toast) {
  var list = document.getElementById('toast_list');
  var id = toast.id;
  var props = toasts.active[id];
  toastTimeout(toast, 'remove-timeout');
  vishidden(toast, true);
  setTimeout(function () {
    var qKeys = Object.keys(toasts.queue);
    list.removeChild(toast);
    delete toasts.active[id];

    if (qKeys.length > 0) {
      var key = qKeys[0];
      var obj = toasts.queue[key];
      setTimeout(function () {
        addToast(obj.toast, obj.settings);
        delete toasts.queue[qKeys[0]];
      }, 500);
    }
  }, 300);
}

function toastEvent(event) {
  var type = event.type;
  var toast = event.currentTarget;
  var state = hasClass(toast, 'expiring');

  if (type == 'mouseover' || type == 'click') {
    if (state === true) {
      toastTimeout(toast, 'remove');
    }
  } else if (state === false) {
    toastTimeout(toast, 'set');
  }
}

window.addEventListener('load', function () {
  var qKeys = Object.keys(toasts.queue);
  setTimeout(function () {
    toasts.ready = true;

    for (var i = 0; i < qKeys.length && i <= 3; i++) {
      var key = qKeys[i];
      var item = toasts.queue[key];
      addToast(item.toast, item.settings);
      delete toasts.queue[key];
    }
  }, 2500);
});