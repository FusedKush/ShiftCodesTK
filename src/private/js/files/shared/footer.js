(function () {
  let interval = setInterval(() => {
    if (typeof globalFunctionsReady !== 'undefined') {
      clearInterval(interval);
      
      // Remove focus when returning to top
      dom.find.id('footer_return').addEventListener('click', function () {
        this.blur();
      });
      // Build Information Panel
      (function () {
        let commit_hash = dom.find.id('footer_ver_commit_hash');
        let build_info_panel = dom.find.id('footer_ver_commit_details');
      
        commit_hash.addEventListener('contextmenu', (event) => {
          if (event.ctrlKey) {
            event.preventDefault();
            ShiftCodesTK.layers.toggleLayer(build_info_panel, true);
          }
        });
      })();
    }
  }, 500);
})();
