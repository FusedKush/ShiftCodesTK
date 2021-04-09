<?php function confirmation_modal () { ?>
  <div class="modal small" id="confirmation_modal">
    <div class="title">Action Confirmation</div>
    <div class="body">
      <div class="inner-body">Are you sure you want to perform this action?</div>
    </div>
    <div class="footer">
      <div>
        <button class="modal-action modal-toggle prevent-onclose layer-target styled has-spinner" id="confirmation_modal_approve" data-action="approve">
          <span>Confirm</span>
          <?php include('local/spinner.php'); ?>
        </button>
        <div class="layer tooltip" id="confirmation_modal_approve_tooltip" data-layer-delay="medium">
          <span>Approve this action</span>
        </div>
      </div>
      <div>
        <button class="modal-action modal-toggle layer-target styled button-effect text has-spinner" id="confirmation_modal_deny" data-action="deny">
          <span>Cancel</span>
          <?php include('local/spinner.php'); ?>
        </button>
        <div class="layer tooltip" id="confirmation_modal_deny_tooltip" data-layer-delay="medium">
          <span>Deny this action</span>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<!-- End of Confirmation Modal Function -->

<div class="modals" id="modals">
  <!-- Global Modals -->
  <?php confirmation_modal(); ?>
  <!-- Profile Card Modal -->
  <div class="modal" id="profile_card_modal">
    <div class="body">
    </div>
  </div>
  <!-- Global Function Stats Modal --> 
  <?php if (isset($_GET['show_global_function_stats'])) : ?>
    <div class="modal" id="global_function_stats_modal">
      <div class="title">Global Function Stats</div>
      <div class="body">
        <div class="intro">Statistics related to the&nbsp;<code>dom</code>&nbsp;and&nbsp;<code>edit</code>&nbsp;global helper functions.</div>
        <div class="section totals">
          <dl class="total">
            <dt>Total</dt>
            <div class="subtitle">The total number of function calls</div>
            <dd>0 (0%) (0 / sec)</dd>
          </dl>
          <dl class="success">
            <dt>Success</dt>
            <div class="subtitle">The number of successful function calls</div>
            <dd>0 (0%) (0 / sec)</dd>
          </dl>
          <dl class="errors">
            <dt>Errored</dt>
            <div class="subtitle">The number of errored function calls</div>
            <dd>0 (0%) (0 / sec)</dd>
          </dl>
        </div>
        <div class="section calls">
          <div class="content-container">
            <div class="title">Function Calls</div>
            <div class="placeholder">No function calls have been recorded yet</div>
          </div>
        </div>
        <div class="section returns">
          <div class="content-container">
            <div class="title">Return Values</div>
            <div class="placeholder">No return values have been recorded yet</div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <!-- Local Modals -->
</div>