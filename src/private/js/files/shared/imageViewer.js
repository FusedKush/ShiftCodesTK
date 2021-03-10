function imgViewClick (e) {
  let target = e.target;

  if (target.tagName == 'IMG' && dom.has(target, 'class', 'fullscreen')) {
    let viewer = document.getElementById('image_viewer');
    let v = {};
      (function () {
        for (let e of ['header', 'title', 'close']) {
          v[e] = dom.find.child(viewer, 'class', e);
        }
      })();
    let title = target.getAttribute('data-fullscreen');
    let copy = edit.copy(target);

    edit.class(copy, 'remove', 'fullscreen');
    edit.class(copy, 'add', 'img');
    viewer.insertBefore(copy, v.header);

    if (title) {
      v.title.innerHTML = title;
    }

    isHidden(viewer, false);

    setTimeout(function () {
      edit.class(viewer, 'remove', 'inactive');
      v.close.focus();
      toggleBodyScroll(false);
      focusLock.set([v.header, copy], imgViewClose);
    }, 50);
  }
}
function imgViewClose () {
  let viewer = document.getElementById('image_viewer');
  let v = {};
    (function () {
      for (let e of ['img', 'title']) {
        v[e] = dom.find.child(viewer, 'class', e);
      }
    })();

    edit.class(viewer, 'add', 'inactive');
    toggleBodyScroll(true);
    focusLock.clear();

    setTimeout(function () {
      isHidden(viewer, true);
      viewer.removeChild(v.img);
      v.title.innerHTML = '';
    }, 450);
}

(function () {
  let t = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(t);
      window.addEventListener('click', imgViewClick);
      dom.find.child(document.getElementById('image_viewer'), 'class',  'close').addEventListener('click', imgViewClose);
    }
  }, 250);
})();
