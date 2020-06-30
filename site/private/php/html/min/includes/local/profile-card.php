<?php 
    /**
     * Returns a customized Profile Card
     * @param array $options An array of customization options
       * @var string $id The User ID of the User. $user returns the Current User's User ID.
       * @var boolean $usePicture True if the User's Profile Picture is to be displayed. Otherwise, False.
       * @var boolean $showBorder True if a border is to be displayed around the card. Otherwise, False.
       * @var boolean $showRoles True if assigned Roles are to be displayed on the card. Otherwise, False.
          * This setting does not affect the Role colors 
       * @var boolean $showInfoTerms True if the Primary Profile Info <dt> terms are to be displayed. Otherwise, False.
       * @var boolean $showStats True if the User's stats can be displayed. Otherwise, False.
     * @return string Returns the customized Profile Card
     */
    function getProfileCard($options = []) { 
  ?><?php
    GLOBAL $_mysqli;

    $defaults = [
      'id'              => '$user',
      'usePicture'      => false,
      'showBorder'      => true,
      'showInfoTerms'   => false,
      'showRoles'       => true,
      'showStats'       => false,
      'showActions'     => false,
      'allowEdit'       => false
    ];
    $settings = array_replace($defaults, $options);
    $profileData = [];
    $profileCardClasses = "profile-card multi-view force-size";
    $profileCardID = "profile_card_" . rand(100, 1000);

    if ($settings['id'] == '$user')   {
      $profileData['id'] = auth_user_id();
    }
    else {
      $profileData['id'] = $settings['id'];
    }

    $profileData['isCurrentUser'] = $profileData['id'] == auth_user_id();
    // Get Username & Roles
    (function () use (&$_mysqli, &$profileData) {
      if ($profileData['isCurrentUser']) {
        $profileData['name'] = auth_user_name();
        $profileData['roles'] = auth_user_roles();
      }
      else {  
        $query = "SELECT 
                    auth_users.username as 'username',
                    auth_records.user_roles as 'roles'
                  FROM auth_users
                  INNER JOIN auth_records 
                    ON auth_users.user_id=auth_records.user_id
                  WHERE auth_users.user_id='{$profileData['id']}'
                  LIMIT 1";
        $sql = $_mysqli->query($query, [ 'collapseAll' => true ]);

        $profileData['name'] = $sql['username'] 
                               ?? "User {$profileData['id']}";
        $profileData['roles'] = $sql['roles'] 
                                ? json_decode($sql['roles'], true) 
                                : array_fill_keys(AUTH_ROLES['roles'], false);
      }
    })();

    // Profile Card classes
    (function () use (&$profileCardClasses, $profileData, $settings) {
      // Card Border
      if ($settings['showBorder']) {
        $profileCardClasses .= ' show-border';
      }
      if ($settings['showInfoTerms']) {
        $profileCardClasses .= ' show-info-terms';
      }
      // Roles
      if (array_search(true, $profileData['roles']) !== false) {
        foreach ($profileData['roles'] as $role => $hasRole) {
          if ($hasRole) {
            $profileCardClasses .= " $role";
          }
        }
      }
      // Edit view
      if (!$settings['showActions'] || !$settings['allowEdit']) {
        $profileCardClasses .= " setup";
      }
    })();
  ?><div class="<?= $profileCardClasses; ?>"id="<?= $profileCardID; ?>"data-view-type=toggle><div class=view id="<?= "{$profileCardID}_view_view"; ?>"><div class="section user"><?php
          $title = $profileData['isCurrentUser'] 
                   ? 'Your'
                   : "{$profileData['name']}'s";
        ?><div class=profile-picture aria-hidden=true><?php if ($settings['usePicture']) : ?><img alt="<?= "$title Profile Picture"; ?>"src="/assets/img/<?= $profileData['id']; ?>"><?php else : ?><span class="fas box-icon fa-user placeholder"><?php endif; ?></span></div><dl class=info><div class="definition user-name"title="<?= "$title Username"; ?>"aria-label="<?= "$title Username"; ?>"><dt>Username<dd><?= clean_all_html($profileData['name']); ?></div><div class="defintition user-id"title="<?= "$title User ID"; ?>"aria-label="<?= "$title User ID"; ?>"><dt>User ID<dd><?= $profileData['id']; ?></div></dl></div><?php if ($settings['showRoles'] && array_search(true, $profileData['roles']) !== false) : ?><div class="section roles"><?php foreach ($profileData['roles'] as $role => $hasRole) : ?><?php if ($hasRole) : ?><?php 
                $label = AUTH_ROLES['props'][$role]['label'];
                $label = $profileData['isCurrentUser'] 
                        ? str_replace('This user is', 'You are', $label) 
                        : str_replace('This user', $profileData['name'], $label);
              ?><span class="<?= "role $role"; ?>"aria-label="<?= $label; ?>"title="<?= $label; ?>"><span><?= ucfirst($role); ?></span></span><?php endif; ?><?php endforeach; ?></div><?php endif; ?><?php if ($settings['showStats']) : ?><?php
          (function () use ($_mysqli, &$profileData) {
            $query = "SELECT 
                        `shift_codes_submitted` as 'codes_submitted',
                        `creation_date` as 'join_date', 
                        `last_public_activity` as 'last_seen_date'
                      FROM
                        `auth_records`
                      WHERE `user_id`='{$profileData['id']}'
                      LIMIT 1";
            $sql = $_mysqli->query($query, [ 'collapseAll' => true ]);

            // if (!$sql) {
            //   return 0;
            // }

            foreach ($sql as $key => $value) {
              $profileData[$key] = $value;
            }
          })();
          $stats = [
            'codes_submitted' => [
              'name'  => 'SHiFT Codes Submitted',
              'title' => $profileData['isCurrentUser'] 
                         ? "The total number of SHiFT Codes that you have submitted"
                         : "The total number of SHiFT Codes that {$profileData['name']} has submitted",
              'icon'  => 'fas fa-key'
            ],
            'join_date' => [
              'name'  => 'Joined',
              'title' => $profileData['isCurrentUser'] 
                         ? "How long ago you joined ShiftCodesTK"
                         : "How long ago {$profileData['name']} joined ShiftCodesTK",
              'icon'  => 'fas fa-calendar-day'
            ],
            'last_seen_date' => [
              'name'  => 'Last Seen',
              'title' => $profileData['isCurrentUser'] 
                         ? "The last time you were active on ShiftCodesTK"
                         : "The last time {$profileData['name']} was active on ShiftCodesTK",
              'icon'  => 'fas fa-star'
            ]
          ];
        ?><dl class="section stats"><?php foreach ($stats as $name => $stat) : ?><?php
              if (strpos($name, 'date') !== false) {
                $date = new DateTime($profileData[$name]);
                // $date->setTime(0, 0);
              }
            ?><div class="<?= "definition $name"; ?>"title="<?= $stat['title']; ?>"><span class="<?= "box-icon icon {$stat['icon']}"; ?>"aria-hidden=true></span> <div class=stat><dt><?= $stat['name']; ?></dt><?php if (strpos($name, 'date') !== false) : ?><dd data-ts="<?= $date->format('c'); ?>"><?= $date->format('F d, Y'); ?></dd><?php else : ?><dd><?= $profileData[$name]; ?></dd><?php endif; ?></div></div><?php endforeach; ?></dl><?php endif; ?><?php if ($settings['showActions']) : ?><div class="section actions"><?php if (!$profileData['isCurrentUser']) : ?><button aria-label="<?= "Report {$profileData['name']}"; ?>"class="color styled report warning"title="<?= "Report {$profileData['name']}"; ?>"disabled><span class="fas fa-flag"aria-hidden=true></span></button><?php endif; ?><?php if ($settings['allowEdit'] && $profileData['isCurrentUser']) : ?><button aria-label="Edit your Profile"class="color styled light view-toggle"title="Edit your Profile"data-view="<?= "{$profileCardID}_view_edit"; ?>">Edit</button><?php endif; ?></div><?php endif; ?></div><?php if ($settings['allowEdit'] && $settings['showActions'] && $profileData['isCurrentUser']) : ?><div class=view id="<?= "{$profileCardID}_view_edit"; ?>"><?php
          include_once(FORMS_PATH . 'account/update-profile.php');

          $form_updateProfile->updateProperty('formFooter->actions->reset->attributes', [
            [
              'name'  => 'data-view',
              'value' => "{$profileCardID}_view_view"
            ]
          ]);
          $form_updateProfile->insertForm();
        ?></div><?php endif; ?></div><?php } ?>