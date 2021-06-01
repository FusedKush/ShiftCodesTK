<?php
  use ShiftCodesTK\Strings;
?>

<div id="templates" hidden>
  <!-- Global Templates -->
  <!-- Dropdown Panels -->
  <template id="dropdown_panel_template">
    <div class="dropdown-panel">
      <button class="header dropdown-panel-toggle">
        <div class="wrapper">
          <div class="title">
            <div class="icon"></div>
            <div class="string">
              <h3 class="primary"></h3>
              <span class="secondary"></span>
            </div>
          </div>
          <div class="indicator">
            <span class="fas fa-chevron-right"></span>
          </div>
        </div>
      </button>
      <div class="body content-container"></div>
    </div>
  </template>
  <!-- Pager -->
  <template id="pager_template">
    <div class="pager configured">
      <div class="content-wrapper">
        <button class="pager-button styled previous layer-target" aria-label="Previous Page">
          <span class="fas fa-chevron-left box-icon"></span>
        </button>
        <div class="layer tooltip">Previous Page</div>
        <div class="jumps">
          <div class="content-container">
            <button class="pager-button styled jump layer-target" id="pager_jump_1" aria-label="Jump to Page 1">
              <b class="box-icon">1</b>
            </button>
            <div class="layer tooltip" id="pager_jump_1_tooltip">Jump to Page 1</div>
          </div>
        </div>
        <button class="pager-button styled next layer-target" aria-label="Next Page">
          <span class="fas fa-chevron-right box-icon"></span>
        </button>
        <div class="layer tooltip">Next Page</div>
      </div>
    </div>
  </template>
  <!-- Form Alerts -->
  <template id="form_alert_template">
    <?= FormCore::ALERT_TEMPLATE; ?>
  </template>
  <!-- Form Character Counters -->
  <template id="form_character_counter_template">
    <?= FormField::CHARACTER_COUNTER_TEMPLATE; ?>
  </template>
  <!-- Profile Card -->
  <?php (function () { ?>
    <template id="profile_card_template">
      <?php
        $profile_card_data = [
          'template_id' => 'profile_card_template_card',
          'user_title'  => "\${username}'s"
        ];
        $profile_card_data['template_id'] = 'profile_card_template_card';
        $profile_card_data['user_title'] = "\${username}'s";

        include(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . '/account/update-profile.php');
      ?>

      <div
        class="profile-card multi-view"
        id="<?= $profile_card_data['template_id']; ?>"
        data-user-id="${user_id}"
        data-view-type="toggle">
        <!-- User Details -->
        <div class="section user">
          <div class="profile-picture">
            <img alt="<?= "{$profile_card_data['user_title']} Profile Picture"; ?>">
            <span class="placeholder box-icon" hidden></span>
          </div>
          <dl class="info">
            <div class="definition user-name">
              <dt>Username</dt>
              <dd class="layer-target">${username}</dd>
              <div class="layer tooltip" data-layer-delay="long"><?= "{$profile_card_data['user_title']} Username"; ?></div>
            </div>
            <div class="definition user-id">
              <dt>User ID</dt>
              <dd class="layer-target">${user_id}</dd>
              <div class="layer tooltip" data-layer-delay="long"><?= "{$profile_card_data['user_title']} User ID"; ?></div>
            </div>
          </dl>
        </div>
        <!-- Primary View -->
        <div class="view primary" id="<?= "{$profile_card_data['template_id']}_view_primary"; ?>">
          <!-- User Roles -->
          <div class="section roles">
            <?php 
              $profile_card_data['user_roles'] = array_reverse(ShiftCodesTK\Users\User::USER_ROLES, true);
            ?>

            <?php foreach ($profile_card_data['user_roles'] as $role => $role_data) : ?>
              <?php
                $role_title = str_replace('This user', '${username}', $role_data['label']);
              ?>

              <span class="<?= "role {$role} layer-target"; ?>" aria-label="<?= $role_title; ?>" data-role="<?= $role; ?>">
                <span><?= $role_data['name']; ?></span>
              </span>
              <div class="layer tooltip"><?= $role_title; ?></div>
            <?php endforeach; ?>
            <!-- End of Role Loop -->
          </div>
          <!-- Profile Stats -->
          <?php 
            $profile_card_data['profile_stats'] = [
              'last_public_activity'    => [
                'name'                     => 'Last Seen',
                'title'                    => 'The last time ${username} was publically active.',
                'icon'                     => 'fas fa-star',
                'is_date'                  => true
              ],
              'creation_date'           => [
                'name'                     => 'Joined',
                'title'                    => 'How long ago ${username} joined ShiftCodesTK.',
                'icon'                     => 'fas fa-calendar-day',
                'is_date'                  => true
              ],
              'shift_codes_submitted'   => [
                'name'                     => 'SHiFT Codes Submitted',
                'title'                    => 'The total number of SHiFT Codes submitted by ${username}.',
                'icon'                     => 'fas fa-key',
                'is_date'                  => false
              ]
            ];
          ?>

          <dl class="section stats">
            <!-- Privacy Preference Button -->
            <button
              class="stat-privacy link appear layer-target"
              aria-label="Profile Stats Privacy Settings"
              data-alias="<?= "{$profile_card_data['template_id']}_edit_profile_action"; ?>">
              <span class="box-icon fas fa-info-circle" aria-hidden="true"></span>
            </button>
            <div class="layer tooltip">
              <div class="multi-view" data-type="toggle">
                <?php
                  $profile_card_data['profile_stats_tooltips'] = [
                    'hidden'  => [
                      'icon'        => 'fa-eye-slash',
                      'description' => 'Only you are able to see them.'
                    ],
                    'private' => [
                      'icon'        => 'fa-lock',
                      'description' => 'Only other users who are currently logged-in are able to see them.'
                    ],
                    'public'  => [
                      'icon'        => 'fa-eye',
                      'description' => 'Everyone is able to see them.'
                    ]
                  ];
                ?>
    
                <?php foreach ($profile_card_data['profile_stats_tooltips'] as $preference => $tooltip) : ?>
                  <?php
                    $display_preference = Strings\transform($preference, Strings\TRANSFORM_CAPITALIZE_WORDS);
                    $full_tooltip = "Your Profile Statistics are currently  <span class=\"inline-box-icon fas {$tooltip['icon']}\" aria-hidden=\"true\"></span>  <em>{$display_preference}</em>. {$tooltip['description']}";
                  ?>
    
                  <span 
                    class="status view <?= " {$preference} "; ?>" 
                    hidden
                    aria-hidden="true">
                    <?= $full_tooltip; ?>
                  </span>
                <?php endforeach; ?>
                <!-- End of Privacy Preference Tooltip Loop -->
              </div>
              <br>
              <br>You can change your Privacy Preferences using the&nbsp;<code class="dark-bg"><span class="inline-box-icon fas fa-pen"></span>&nbsp;&nbsp;Edit Profile</code>&nbsp;button.
            </div>
            <!-- End of Privacy Preference Button Conditional -->
            <?php foreach ($profile_card_data['profile_stats'] as $stat_name => $stat_info) : ?>
              <?php 
                $stat_data = "\${{$stat_name}_value}";
              ?>

              <div class="<?= "definition {$stat_name}"; ?>">
                <span class="<?= "box-icon icon {$stat_info['icon']}"; ?>" aria-hidden="true"></span>
                &nbsp;<div class="stat">
                  <dt class="layer-target layer-hover-indicator"><?= $stat_info['name']; ?></dt>
                  <div class="layer tooltip"><?= $stat_info['title']; ?></div>
                  
                  <!-- Date Value -->
                  <?php if ($stat_info['is_date']) : ?>
                    <dd class="layer-target" data-relative-date="<?= "\${{$stat_name}_value}"; ?>">
                      <?= "\${{$stat_name}_value_relative}"; ?>
                    </dd>
                    <div class="layer tooltip"><?= "\${{$stat_name}_value_timestamp}"; ?></div>
                  <!-- Non-Date Value -->
                  <?php else : ?>
                    <dd><?= $stat_data; ?></dd>
                  <?php endif; ?>
                  <!-- End of Date Value Check -->
                </div>
              </div>

              <?php unset($profileStatDate); ?>
            <?php endforeach; ?>
            <!-- End of Profile Stats Loop -->
          </dl>
          <!-- Actions -->
          <div class="section actions button-group">
            <!-- Enforcement Button -->
            <button 
              class="styled warning button-effect outline enforcement layer-target" 
              id="<?= "{$profile_card_data['template_id']}_enforce_action" ?>"
              aria-label="Enforce ${username}">
              <span class="fas fa-gavel" aria-hidden="true"></span>
            </button>
            <div class="layer tooltip" data-layer-target="<?= "{$profile_card_data['template_id']}_enforce_action" ?>">Take an enforcement action against ${username}</div>
            <!-- Report Button -->
            <button 
              class="styled warning button-effect outline report layer-target" 
              id="<?= "{$profile_card_data['template_id']}_report_action" ?>"
              aria-label="Report ${username}">
              <span class="fas fa-flag" aria-hidden="true"></span>
            </button>
            <div class="layer tooltip" data-layer-target="<?= "{$profile_card_data['template_id']}_report_action" ?>">Report ${username}'s Profile</div>
            <!-- Edit Profile Button -->
            <button 
              class="styled light button-effect hover edit-profile layer-target" 
              id="<?= "{$profile_card_data['template_id']}_edit_profile_action" ?>"
              aria-label="Edit your Profile">
              <span>
                <span class="fas fa-pen" aria-hidden="true"></span>
                &nbsp;&nbsp;Edit Profile
              </span>
            </button>
            <div class="layer dropdown" data-layer-target="<?= "{$profile_card_data['template_id']}_edit_profile_action" ?>">
              <div class="title">Edit your Profile</div>
              <ul class="choice-list">
                <li>
                  <button class="choice styled button-effect text layer-target" data-value="change-profile-picture" disabled>
                    <span>
                      <span class="inline-box-icon"><span class="fas fa-camera" aria-hidden="true"></span></span>
                      Change Profile Picture
                    </span>
                  </button>
                  <div class="layer dropdown" data-layer-pos="left">
                    <div class="title">Change Profile Picture</div>
                    <ul class="choice-list">
                      <li>
                        <button class="choice styled button-effect text auto-toggle" data-value="upload" disabled>
                          <span class="inline-box-icon"><span class="fas fa-file-upload" aria-hidden="true"></span></span>
                          Upload Profile Picture
                        </button>
                      </li>
                      <li>
                        <button class="choice styled warning button-effect text auto-toggle" data-value="remove" disabled>
                          <span class="inline-box-icon"><span class="fas fa-trash-alt" aria-hidden="true"></span></span>
                          Remove Profile Picture
                        </button>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="multi-view" data-view-type="toggle">
                  <div class="view enabled">
                    <button
                      class="choice styled button-effect text auto-toggle view-toggle"
                      data-value="change-username"
                      data-view="<?= "{$profile_card_data['template_id']}_view_change_username" ?>">
                      <span>
                        <span class="inline-box-icon"><span class="fas fa-user"></span></span>
                        Change Username
                      </span>
                    </button>
                  </div>
                  <div class="view disabled" hidden>
                    <button
                      class="choice styled button-effect text layer-target allow-disabled-layers"
                      disabled>
                      <span>
                        <span class="inline-box-icon"><span class="fas fa-user"></span></span>
                        Change Username
                      </span>
                    </button>
                    <div class="layer tooltip" data-layer-pos="left">You can only change your username&nbsp;<em>twice</em>&nbsp;every&nbsp;<em>24 hours</em></div>
                  </div>
                </li>
                <li>
                  <button class="choice styled button-effect text auto-toggle view-toggle" data-value="role-details" data-view="<?= "{$profile_card_data['template_id']}_view_role_details"; ?>">
                    <span>
                      <span class="inline-box-icon"><span class="fas fa-star" aria-hidden="true"></span></span>
                      Role Details
                    </span>
                  </button>
                </li>
                <li>
                  <button class="choice styled button-effect text auto-toggle view-toggle" data-value="change-profile-stats-privacy" data-view="<?= "{$profile_card_data['template_id']}_view_stat_privacy"; ?>">
                    <span>  
                      <span class="inline-box-icon"><span class="fas fa-eye" aria-hidden="true"></span></span>
                      Profile Stats Privacy
                    </span>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <!-- Sub Views -->
        <div class="view edit change-username" id="<?= "{$profile_card_data['template_id']}_view_change_username"; ?>">
          <?php
            $form_changeUsername->insertForm();
          ?>
        </div>
        <div class="view edit role-details" id="<?= "{$profile_card_data['template_id']}_view_role_details"; ?>">
          <?php
            $form_roleDetails->insertForm();
          ?>
        </div>
        <div class="view edit stat-privacy" id="<?= "{$profile_card_data['template_id']}_view_stat_privacy"; ?>">
          <?php
            $form_statPrivacy->insertForm();
          ?>
        </div>
      </div>
    </template>
  <?php })(); ?>
  <!-- End of Profile Card -->

  <!-- Local Templates -->
</div>
