function imgViewClick (e) {
  let target = e.target;

  if (target.tagName == 'IMG' && hasClass(target, 'fullscreen')) {
    let viewer = document.getElementById('image_viewer');
    let v = {};
      (function () {
        for (let e of ['header', 'title', 'close']) {
          v[e] = getClass(viewer, e);
        }
      })();
    let title = target.getAttribute('data-fullscreen');
    let copy = copyElm(target);

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
        v[e] = getClass(viewer, e);
      }
    })();

    addClass(viewer, 'inactive');
    toggleBodyScroll(true);
    focusLock.clear();

    setTimeout(function () {
      vishidden(viewer, true);
      viewer.removeChild(v.img);
      v.title.innerHTML = '';
    }, 450);
}

(function () {
  let t = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(t);
      window.addEventListener('click', imgViewClick);
      getClass(document.getElementById('image_viewer'), 'close').addEventListener('click', imgViewClose);
    }
  }, 250);
})();
