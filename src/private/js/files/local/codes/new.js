// Startup
(function () {
  let interval = setInterval(() => {
    if (typeof globalFunctionsReady != 'undefined' && typeof ShiftCodesTK.forms != 'undefined' && ShiftCodesTK.forms.isLoaded) {
      clearInterval(interval);

      // Setup Game Theme Color Hook
      (function () {
        const formsObject = ShiftCodesTK.forms;
        const form = dom.find.id('new_shift_code_form');
        const gameIDField = formsObject.getField(form, 'game_id');
        const formProps = formsObject.getProps(form);
        // const formData = formsObject.getFormData(form);

        // if (formData.game_id) {
        //   edit.attr(document.body, 'update', 'data-theme', formData.game_id);          
        // }
  
        // Game / Theme Color Hooks
        form.addEventListener('tkFormsFieldCommit', (event) => {
          const formEventData = event.formEventData;
          const gameID = formEventData.fieldValue;
          
          if (!event.customEventSource && formEventData.fieldProps.info.name == 'game_id') {
            changeSiteTheme(gameID);

            // Update Query Parameters
            updateQueryParameters({
              game: gameID
            });
          }
        });
        form.addEventListener('tkFormsFormAfterReset', (event) => {
          const formData = event.formEventData.formData;

          changeSiteTheme(formData.game_id ? formData.game_id : 'main');
        });
      })();


      ShiftCodesTK.local.isReady = true;
    }
  }, 250);
})();