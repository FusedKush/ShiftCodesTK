// Toast global registry
var toasts = {
  ready: false,
  active: {},
  queue: {}
};

function toastTimeout (toast, type) {
  let n = toast.id;

  if (type == 'set') {
    addClass(toast, 'expiring');
    toasts.active[n].timeout = setTimeout(function () {
      toastTimeout(toast, 'expired');
    }, toasts.active[n].duration);
  }
  else {
    clearTimeout(toasts.active[n].timeout);

    if (type == 'remove') {
      delClass(toast, 'expiring');
    }
    else if (type == 'expired') {
      removeToast(toast);
    }
  }
}
function newToast (properties) {
  let defaultProps = {
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
      action: false,
      close: false,
      name: 'Action',
      label: 'The Action button'
    },
    close: {
      use: true,
      type: 'button',
      link: '#',
      action: false,
      close: true,
      name: 'Dismiss',
      label: 'Dismiss and close the toast'
    }
  }
  let templates = {
    exception: {
      settings: {
        id: 'exception',
        duration: 'infinite'
      },
      content: {
        title: 'An error has occurred'
      },
      action: {
        use: true,
        type: 'link',
        link: ' ',
        name: 'Refresh',
        label: 'Refresh the page and try again'
      }
    }
  };
  let templateProps = (function () {
    let t = properties.settings.template;

    if (t && t !== 'none' && templates[t] !== undefined) {
      return templates[t];
    }
    else {
      return {};
    }
  })();
  let props = mergeObj(defaultProps, templateProps, properties);
  let toast = getTemplate('toast_template');
  let settings = {
    id: `toast_${props.settings.id}`,
    duration: (function () {
      let d = props.settings.duration;
      let vals = {
        short: 2500,
        medium: 5000,
        long: 7500
      };

      if (vals[d] !== undefined) { return vals[d]; }
      else                       { return d; }
    })()
  };
  let e = {
    progress: getClass(getClass(toast, 'progress-bar'), 'progress'),
    icon: getClass(toast, 'icon').childNodes[0],
    title: getClass(toast, 'title'),
    body: getClass(toast, 'body'),
    actions: getClass(toast, 'actions')
  };
  let list = document.getElementById('toast_list');
  let ids = {
    title: `${settings.id}_title`,
    body: `${settings.id}_body`
  };

  function addAction (type, actionProps) {
    let btn = (function () {
      if (actionProps.type == 'button') { return document.createElement('button'); }
      else                              { return document.createElement('a'); }
    })();
    let classes = {
      button: 'styled',
      link: 'button'
    };

    addClass(btn, type);
    addClass(btn, classes[actionProps.type])
    btn.innerHTML = actionProps.name;
    updateLabel(btn, actionProps.label);

    if (actionProps.type == 'link') {
      btn.href = actionProps.link;
    }
    if (actionProps.action) {
      btn.addEventListener('click', actionProps.action);
    }
    if (actionProps.close) {
      btn.setAttribute('aria-controls', settings.id);
      btn.addEventListener('click', function () {
        removeToast(toast);
      });
    }
    e.actions.appendChild(btn);
    return btn;
  }

  // Properties
  toast.id = settings.id;
  e.progress.style.animationDuration = `${settings.duration}ms`;
  // Content
  addClass(e.icon, props.content.icon);
  e.title.innerHTML = props.content.title;
  e.title.id = ids.title;
  toast.setAttribute('aria-labelledby', ids.title);
  e.body.innerHTML = props.content.body;
  e.body.id = ids.body;
  toast.setAttribute('aria-describedby', ids.body);
  // Actions
  if (props.action.use) { addAction('action', props.action); }
  if (props.close.use)  { addAction('close', props.close); }
  // Timeout listeners
  if (settings.duration != 'infinite') {
    toast.addEventListener('mouseover', toastEvent);
    toast.addEventListener('mouseout', toastEvent);
    toast.addEventListener('click', toastEvent);
  }
  // Add toast
  return (function () {
    let n = settings.id;

    if (toasts.active[n] === undefined &&
        Object.keys(toasts.active).length <= 3 &&
        toasts.ready) {
      addToast(toast, settings);
      return toast;
    }
    else if (toasts.queue[n] === undefined) {
      toasts.queue[n] = {
        toast: toast,
        settings: settings
      };

      return toast;
    }
    else {
      return false;
    }
  })();
}
function addToast (toast, settings) {
  let n = settings.id;

  toasts.active[n] = settings;
  document.getElementById('toast_list').appendChild(toast);
  vishidden(toast, false);

  setTimeout(function () {
    if (settings.duration != 'infinite') {
      toastTimeout(toast, 'set');
    }
  }, 200);
}
function removeToast (toast) {
  let list = document.getElementById('toast_list');
  let id = toast.id;
  let props = toasts.active[id];

  toastTimeout(toast, 'remove-timeout');
  vishidden(toast, true);
  setTimeout(function () {
    let qKeys = Object.keys(toasts.queue);

    list.removeChild(toast);
    delete toasts.active[id];

    if (qKeys.length > 0) {
      let key = qKeys[0];
      let obj = toasts.queue[key];

      setTimeout(function () {
        addToast(obj.toast, obj.settings);
        delete toasts.queue[qKeys[0]];
      }, 500);
    }
  }, 300);
}
function toastEvent (event) {
  let type = event.type;
  let toast = event.currentTarget;
  let state = hasClass(toast, 'expiring');

  if (type == 'mouseover' || type == 'click') {
    if (state === true) {
      toastTimeout(toast, 'remove');
    }
  }
  else if (state === false) {
    toastTimeout(toast, 'set');
  }
}

window.addEventListener('load', function () {
  let qKeys = Object.keys(toasts.queue);

  setTimeout(function () {
    toasts.ready = true;

    for (let i = 0; (i < qKeys.length && i <= 3); i++) {
      let key = qKeys[i];
      let item = toasts.queue[key];

      addToast(item.toast, item.settings);
      delete toasts.queue[key];
    }
  }, 2500);
});
