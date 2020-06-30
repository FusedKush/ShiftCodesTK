// Update stat timestamps
(function () {
  let interval = setInterval(function () {
    if (typeof globalFunctionsReady != 'undefined' && typeof moment != 'undefined') {
      clearInterval(interval);

      let main = dom.find.child(document.body, 'tag', 'main');
      let profile = dom.find.child(main, 'class', 'profile-card');
      let stats = dom.find.children(dom.find.child(profile, 'class', 'stats'), 'class', 'definition');

      for (let i = 0; i < stats.length; i++) {
        let stat = stats[i];

        if (stat.className.indexOf('date') != -1) {
          let def = dom.find.child(stat, 'tag', 'dd');
          let date = moment.utc(dom.get(def, 'attr', 'data-ts'));
  
          updateLabel(def, date.format('MMMM DD, YYYY'));
          def.innerHTML = ucWords(date.fromNow(true)) + ' ago';
          edit.attr(def, 'remove', 'data-ts');
        }
      }
    }
  }, 250);
})();