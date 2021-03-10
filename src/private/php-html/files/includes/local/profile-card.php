<?php
  use ShiftCodesTK\Users,
      ShiftCodesTK\Strings;

  /** Generate a *Profile Card*.
   * 
   * @param array $card_options An `array` of options to customize the Profile Card:
   * 
   * | Property | Type | Default Value | Description |
   * | --- | --- | --- | --- |
   * | *user_id* | `string` | `$user` | The `user_id` of the *User*. The keyword **$user** uses the *Current User's ID*. |
   * | *show_picture* | `bool` | `true` | Indicates if the User's *Profile Picture* is to be displayed. |
   * | *show_border* | `bool` | `true` | Indicates if a border is to be rendered around the Profile Card. |
   * | *show_info_terms* | `bool` | `false` | Indicates if the *Primary Profile Info Terms* are to be rendered for the Profile Card. | 
   * | *show_roles* | `bool` | `false` | Indicates if the User's *User Roles* are to be displayed on the Profile Card. This does not effect the role colors of the username, border, etc... |
   * | *show_stats* | `bool` | `false` | Indicates if the User's *Profile Stats* are to be displayed on the Profile Card. Whether or not the stats are actually displayed depends on the User's *Profile Stats Privacy Preference*. |
   * | *show_actions* | `bool` | `false` | Indicates if *Profile Card Actions* are to be displayed on the Profile Card. Which actions are rendered is determined by which `user_id` is provided. |
   * | *allow_editing* | `bool` | `false` | Indicates if the User's Profile can be *edited*. |
   * 
   * @return string Returns the new *Profile Card* on success. 
   */
  function get_profile_card ($card_options = []) {
    $isCurrentUser = false;
    $isTemplate = ($card_options['user_id'] ?? null) === '$template';
    $options = (function () use ($card_options) {
      $defaultOptions = [
        'user_id'         => '$user',
        'show_picture'    => true,
        'show_border'     => true,
        'show_info_terms' => false,
        'show_roles'      => false,
        'show_stats'      => false,
        'show_actions'    => false,
        'allow_editing'   => false
      ];
      $options = array_merge($defaultOptions, $card_options);

      if ($options['user_id'] == '$user') {
        if (!Users\CurrentUser::is_logged_in()) {
          throw new \Error("A user is not currently logged in.");
        }

        $options['user_id'] = Users\CurrentUser::get_current_user()->user_id;
      }

      return $options;
    })();
    $userData = (function () use (&$isCurrentUser, $options, $isTemplate) {
      if (!$isTemplate) {
        $userData = new Users\UserRecord($options['user_id']);
        
        if ($userData->is_current_user()) {
          $getFullData = $options['show_stats'] || $options['allow_editing'];
  
          $isCurrentUser = true;
          return Users\CurrentUser::get_current_user($getFullData);
        }
        else {
          return $userData;
        }
      }
      else {
        return false;
      }
    })();
    $userID = !$isTemplate
              ? Strings\encode_html($userData->user_id)
              : '${user_id}';
    $username = !$isTemplate
                ? Strings\encode_html($userData->username)
                : '${username}';
    $profileCardClasses = (function () use ($options, $userData, $isTemplate) {
      $classes = [ 'profile-card', 'multi-view' ];

      if ($options['show_border']) {
        $classes[] = 'show-border';
      }
      if ($options['show_info_terms']) {
        $classes[] = 'show-info-terms';
      }
      if (!$options['show_actions'] && !$options['allow_editing']) {
        $classes[] = 'setup';
      }
      // User Roles
      if (!$isTemplate) {
        $userRoles = $userData->get_roles();

        if ($userRoles) {
          foreach ($userRoles as $role) {
            $classes[] = "role-{$role}";
          }
        }
      }

      return implode(' ', $classes);
    })();
    $profileCardID = !$isTemplate
                     ? \ShiftCodesTK\Auth\random_unique_id(12, 'profile_card_')
                     : 'profile_card_template_card';
  ?>
    <div 
      class="<?= $profileCardClasses; ?>" 
      id="<?= $profileCardID; ?>" 
      data-user-id="<?= $userID; ?>" 
      data-view-type="toggle">
      <!-- User Details -->
      <div class="section user">
        <?php
          $userTitle = (
                        $isCurrentUser
                        ? 'Your'
                        : "{$username}'s"
                      );
        ?>

        <!-- Profile Picture -->
        <div class="profile-picture">
          <!-- Show Profile Picture -->
          <?php if ($options['show_picture'] && ($isTemplate || $userData->profile_picture ?? false)) : ?>
            <img src="<?= "/assets/img/users/profiles/{$userID}?_request_token={$_SESSION['token']}"; ?>" alt="<?= "{$userTitle} Profile Picture"; ?>">
          <!-- Hide Profile Picture -->
          <?php else : ?>
            <?php
              $placeholderLetters = (function () use ($username) {
                $usernameObj = new Strings\StringObj($username);
                $letters = $usernameObj->preg_match('/[A-Z]/', Strings\PREG_GLOBAL_SEARCH|Strings\PREG_RETURN_FULL_MATCH);
    
                if ($letters) {
                  $letters = array_slice($letters, 0, 2, true);
                  $letters = implode('', $letters);
                }
                else {
                  $letters = $usernameObj->substr(0, 1);
                  $letters = Strings\transform($letters, Strings\TRANSFORM_UPPERCASE);
                }

                return $letters;
              })();
            ?>
            <!-- <span class="placeholder box-icon fas fa-user" aria-hidden="true"></span> -->
            <span class="placeholder box-icon" aria-hidden="true"><?= $placeholderLetters ?></span>
          <?php endif; ?>
          <!-- End of Profile Picture Conditional -->
        </div>
        <!-- Basic User Info -->
        <dl class="info">
          <!-- Username -->
          <div class="definition user-name">
            <dt>Username</dt>
            <dd class="layer-target"><?= $username; ?></dd>
            <div class="layer tooltip" data-layer-delay="long"><?= "{$userTitle} Username"; ?></div>
          </div>
          <!-- User ID -->
          <div class="definition user-id">
            <dt>User ID</dt>
            <dd class="layer-target"><?= $userID; ?></dd>
            <div class="layer tooltip" data-layer-delay="long"><?= "{$userTitle} User ID"; ?></div>
          </div>
        </dl>
      </div>
      <!-- Primary View -->
      <div class="view primary" id="<?= "{$profileCardID}_view_primary"; ?>">
        <!-- User Roles -->
        <?php if ($options['show_roles'] && ($isTemplate || $userData->get_roles(Users\User::USER_ROLES_GET_INT))) : ?>
          <div class="section roles">
            <?php 
              $roleList = !$isTemplate
                          ? $userData->get_roles(Users\User::USER_ROLES_GET_FULL_ARRAY)
                          : array_reverse(Users\User::USER_ROLES, true);
            ?>

            <?php foreach ($roleList as $role => $roleData) : ?>
              <?php
                $roleTitle = $isCurrentUser   
                            ? str_replace('This user is', 'You are', $roleData['label'])
                            : str_replace('This user', $username, $roleData['label']);
              ?>

              <span class="<?= "role {$role} layer-target"; ?>" aria-label="<?= $roleTitle; ?>" data-role="<?= $role; ?>">
                <span><?= $roleData['name']; ?></span>
              </span>
              <div class="layer tooltip"><?= $roleTitle; ?></div>
            <?php endforeach; ?>
            <!-- End of Role Loop -->
          </div>
        <?php endif; ?>
        <!-- End of User Roles Condition -->
        <!-- Profile Stats -->
        <?php if ($options['show_stats'] && ($isTemplate || $userData->current_profile_stats_visibility())) : ?>
          <?php 
            $profileStats = [
              'last_public_activity'    => [
                'name'                     => 'Last Seen',
                'title'                    => $isCurrentUser
                                              ? 'The last time you were publically active.'
                                              : "The last time {$username} was publically active.",
                'icon'                     => 'fas fa-star',
                'is_date'                  => true
              ],
              'creation_date'           => [
                'name'                     => 'Joined',
                'title'                    => $isCurrentUser
                                              ? 'How long ago you joined ShiftCodesTK.'
                                              : "How long ago {$username} joined ShiftCodesTK.",
                'icon'                     => 'fas fa-calendar-day',
                'is_date'                  => true
              ],
              'shift_codes_submitted'   => [
                'name'                     => 'SHiFT Codes Submitted',
                'title'                    => $isCurrentUser
                                              ? 'The total number of SHiFT Codes you have submitted.'
                                              : "The total number of SHiFT Codes submitted by {$username}.",
                'icon'                     => 'fas fa-key',
                'is_date'                  => false
              ]
            ];
          ?>

          <dl class="section stats">
            <!-- Privacy Preference Button -->
            <?php if ($options['allow_editing'] && $options['show_actions'] && ($isTemplate || $isCurrentUser)) : ?>
              <button
                class="stat-privacy styled light button-effect text view-toggle layer-target"
                aria-label="Profile Stats Privacy Settings"
                data-alias="<?= "{$profileCardID}_edit_profile_button"; ?>"
                data-view="">
                <?php
                  $privacyPreferenceIcons = [
                    'hidden'  => 'fa-eye-slash',
                    'private' => 'fa-lock',
                    'public'  => 'fa-eye'
                  ];
                ?>

                <?php foreach ($privacyPreferenceIcons as $preference => $icon) : ?>
                  <span 
                    class="<?= "icon fas box-icon {$preference} {$icon}"; ?>"
                    aria-hidden="true"
                    <?= !$isTemplate && $userData->profile_stats_preference == $preference ? '' : ' hidden' ?>>
                  </span>
                <?php endforeach; ?>
                <!-- End of Privacy Preference Icons Loop -->
              </button>
              <div class="layer tooltip">
                <?php
                  $privacyPreferenceTooltips = [
                    'hidden'  => 'Your Profile Statistics are currently&nbsp;<em>Hidden</em>. Only you are able to see them.',
                    'private' => 'Your Profile Statistics are currently&nbsp;<em>Private</em>. Only other users who are currently logged-in are able to see them.',
                    'public'  => 'Your Profile Statistics are currently&nbsp;<em>Public</em>. Everyone is able to see them.'
                  ];
                ?>

                <?php foreach ($privacyPreferenceTooltips as $preference => $tooltip) : ?>
                  <span 
                    class="status <?= " {$preference} "; ?>" 
                    aria-hidden="<?= $userData->profile_stats_preference == $preference; ?>" 
                    <?= !$isTemplate && $userData->profile_stats_preference == $preference ? '' : ' hidden'; ?>>
                    <?= $tooltip; ?>
                  </span>
                <?php endforeach; ?>
                <!-- End of Privacy Preference Tooltip Loop -->
                <br>
                <br>You can change your Privacy Preferences using the&nbsp;<code>Edit Profile</code>&nbsp;button.
              </div>
            <?php endif; ?>
            <!-- End of Privacy Preference Button Conditional -->
            <?php foreach ($profileStats as $stat_name => $stat_info) : ?>
              <?php 
                $stat_data = !$isTemplate
                             ? $userData->$stat_name
                             : "\${{$stat_name}_value}";

                if ($stat_info['is_date']) {
                  $profileStatDate = !$isTemplate
                                     ? new \DateTime($stat_data, new \DateTimeZone('UTC'))
                                     : $stat_data;
                }
              ?>

              <div class="<?= "definition {$stat_name}"; ?>">
                <span class="<?= "box-icon icon {$stat_info['icon']}"; ?>" aria-hidden="true"></span>
                &nbsp;<div class="stat">
                  <dt class="layer-target layer-hover-indicator"><?= $stat_info['name']; ?></dt>
                  <div class="layer tooltip"><?= $stat_info['title']; ?></div>
                  
                  <!-- Date Value -->
                  <?php if (isset($profileStatDate)) : ?>
                    <dd class="layer-target" data-relative-date="<?= !$isTemplate ? $profileStatDate->format('c') : "\${{$stat_name}_value}"; ?>">
                      <?= !$isTemplate ? $profileStatDate->format('F d, Y') : "\${{$stat_name}_value_relative}"; ?>
                    </dd>
                    <div class="layer tooltip"><?= !$isTemplate ? $profileStatDate->format('F d, Y h:i A \U\T\C') : "\${{$stat_name}_value_timestamp}"; ?></div>
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
        <?php endif; ?>
        <!-- End of Profile Stats Conditional -->
        <!-- Actions -->
        <?php if ($options['show_actions']) : ?>
          <div class="section actions button-group">
            <!-- Report/Enforcement Buttons -->
            <?php if (!$isCurrentUser) : ?>
              <?php
                $canEnforceUser = ($isTemplate 
                                  || (Users\CurrentUser::is_logged_in() 
                                  && Users\CurrentUser::get_current_user()->has_permission('MODERATE_USERS')
                                  && $userData->get_roles(Users\User::USER_ROLES_GET_INT) < Users\CurrentUser::get_current_user()->get_roles(Users\User::USER_ROLES_GET_INT)));
              ?>

              <!-- Enforcement Button -->
              <?php if ($canEnforceUser) : ?>
                <button 
                  class="styled warning button-effect outline enforcement layer-target" 
                  aria-label="<?= "Enforce {$username}"; ?>">
                  <span class="fas fa-gavel" aria-hidden="true"></span>
                </button>
                <div class="layer tooltip">Take an enforcement action against&nbsp;<strong><?= $username; ?></strong></div>
              <?php endif; ?>
              <!-- End of Enforcement Button Conditional -->
              <!-- Report Button -->
              <?php if ($isTemplate || !$userData->has_permission('MODERATE_USERS')) : ?>
                <button 
                  class="styled warning button-effect outline report layer-target" 
                  aria-label="<?= "Report {$username}"; ?>">
                  <span class="fas fa-flag" aria-hidden="true"></span>
                </button>
                <div class="layer tooltip">Report&nbsp;<strong><?= $username; ?></strong></div>
              <?php endif; ?>
              <!-- End of Report Button Conditional -->
            <?php endif; ?>
            <!-- End of Report/Enforcement Buttons -->
            <!-- Edit Buttons -->
            <?php if ($options['allow_editing'] && $options['show_actions'] && ($isTemplate || $isCurrentUser)) : ?>
              <button
                class="edit-profile styled button-effect hover light layer-target"
                id="<?= "{$profileCardID}_edit_profile_button"; ?>"
                aria-label="Edit your Profile">
                <span>Edit Profile</span>
              </button>
              <!-- <div class="layer tooltip" data-layer-delay="long">Change your Username, view your Roles, and change your Profile Stats Privacy Preference.</div> -->
              <div class="layer dropdown">
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
                  <li>
                    <?php
                      $canChangeUsername = !$isTemplate
                                           ? $userData->check_username_eligibility()
                                           : true;
                      $usernameButtonClasses = [
                        'choice',
                        'styled',
                        'button-effect',
                        'text',
                        'auto-toggle',
                        'view-toggle',
                        'allow-disabled-layers'
                      ];

                      if (!$canChangeUsername) {
                        $usernameButtonClasses[] = 'layer-target';
                      }
                    ?>

                    <button 
                      class="<?= implode(' ', $usernameButtonClasses); ?>" 
                      data-value="change-username"
                      data-view="<?= "{$profileCardID}_view_change_username"; ?>"
                      <?= !$canChangeUsername ? ' disabled' : ''; ?>>
                      <span>
                        <span class="inline-box-icon"><span class="fas fa-user" aria-hidden="true"></span></span>
                        Change Username
                      </span>
                    </button>
                    <?php if (!$canChangeUsername) : ?>
                      <div class="layer tooltip" data-layer-pos="left">You can only change your username&nbsp;<em>twice</em>&nbsp;every&nbsp;<em>24 hours</em></div>
                    <?php endif; ?>
                    <!-- End of Username Tooltip Conditional -->
                  </li>
                  <li>
                    <button class="choice styled button-effect text auto-toggle view-toggle" data-value="role-details" data-view="<?= "{$profileCardID}_view_role_details"; ?>">
                      <span>
                        <span class="inline-box-icon"><span class="fas fa-star" aria-hidden="true"></span></span>
                        Role Details
                      </span>
                    </button>
                  </li>
                  <li>
                    <button class="choice styled button-effect text auto-toggle view-toggle" data-value="change-profile-stats-privacy" data-view="<?= "{$profileCardID}_view_stat_privacy"; ?>">
                      <span>  
                        <span class="inline-box-icon"><span class="fas fa-eye" aria-hidden="true"></span></span>
                        Profile Stats Privacy
                      </span>
                    </button>
                  </li>
                </ul>
              </div>
            <?php endif; ?>
            <!-- End of Edit Buttons Conditional -->
          </div>
        <?php endif; ?>
        <!-- End of Actions Conditional -->
      </div>
      <!-- Editing Views -->
      <?php if ($options['allow_editing'] && $options['show_actions'] && ($isTemplate || $isCurrentUser)) : ?>
        <?php
          include(\ShiftCodesTK\PRIVATE_PATHS['forms'] . '/account/update-profile.php');
        ?>

        <!-- Change Username View -->
        <div class="view edit change-username" id="<?= "{$profileCardID}_view_change_username"; ?>">
          <?php
            $form_changeUsername->updateProperty('formFooter->actions->reset->object->properties->customHTML->attributes', [
              'data-view' => "{$profileCardID}_view_primary"
            ]);

            $form_changeUsername->insertForm();
          ?>
        </div>
        <!-- Role Details View -->
        <div class="view edit role-details" id="<?= "{$profileCardID}_view_role_details"; ?>">
          <?php
            $rolesField = $form_roleDetails->getChild('roles');

            $form_roleDetails->updateProperty('formFooter->actions->reset->object->properties->customHTML->attributes', [
              'data-view' => "{$profileCardID}_view_primary"
            ]);

            // Update Roles
            $rolesField->updateProperty('content->description', (function () use (&$rolesField, &$userData, $isTemplate) {
              $description = $rolesField->findReferencedProperty('content-description');

              foreach ($description as $role => $roleDescription) {
                if (!$isTemplate && !$userData->has_role($role)) {
                  unset($description[$role]);
                }
              }

              return $description;
            })());
            $rolesField->updateProperty('inputProperties->value', implode(', ', !$isTemplate ? $userData->get_roles() : []));
            $rolesField->updateProperty('inputProperties->options', (function () use (&$rolesField, &$userData, $isTemplate) {
              $options = $rolesField->findReferencedProperty('inputProperties->options');

              foreach ($options as $role => $roleName) {
                if (!$isTemplate && !$userData->has_role($role)) {
                  unset($options[$role]);
                }
              }

              return $options;
            })());

            $form_roleDetails->insertForm();
          ?>
        </div>
        <!-- Profile Stats Privacy Preference View -->
        <div class="view edit stat-privacy" id="<?= "{$profileCardID}_view_stat_privacy"; ?>">
          <?php
            $form_statPrivacy->updateProperty('formFooter->actions->reset->object->properties->customHTML->attributes', [
              'data-view' => "{$profileCardID}_view_primary"
            ]);
            $form_statPrivacy->getChild('privacy_preference')->updateProperty('inputProperties->value', !$isTemplate ? $userData->profile_stats_preference : '');

            $form_statPrivacy->insertForm();
          ?>
        </div>
      <?php endif; ?>
      <!-- End of Editing Views Conditional -->
    </div>
  <?php } ?>