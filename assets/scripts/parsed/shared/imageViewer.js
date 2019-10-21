function imgViewClick(e) {
  var target = e.target;

  if (target.tagName == 'IMG' && hasClass(target, 'fullscreen')) {
    var viewer = document.getElementById('image_viewer');
    var v = {};

    (function () {
      for (var _i = 0, _arr = ['header', 'title', 'close']; _i < _arr.length; _i++) {
        var _e = _arr[_i];
        v[_e] = getClass(viewer, _e);
      }
    })();

    var title = target.getAttribute('data-fullscreen');
    var copy = copyElm(target);
    delClass(copy, 'fullscreen');
    addClass(copy, 'img');
    viewer.insertBefore(copy, v.header);

    if (title) {
      v.title.innerHTML = title;
    }

    vishidden(viewer, false);
    setTimeout(function () {
      delClass(viewer, 'inactive');
      v.close.focus();
      focusLock.set([v.header, copy], imgViewClose);
    }, 50);
  }
}

function imgViewClose() {
  var viewer = document.getElementById('image_viewer');
  var v = {};

  (function () {
    for (var _i2 = 0, _arr2 = ['img', 'title']; _i2 < _arr2.length; _i2++) {
      var e = _arr2[_i2];
      v[e] = getClass(viewer, e);
    }
  })();

  addClass(viewer, 'inactive');
  focusLock.clear();
  setTimeout(function () {
    vishidden(viewer, true);
    viewer.removeChild(v.img);
    v.title.innerHTML = '';
  }, 450);
}

(function () {
  var t = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(t);
      window.addEventListener('click', imgViewClick);
      getClass(document.getElementById('image_viewer'), 'close').addEventListener('click', imgViewClose);
    }
  }, 250);
})();