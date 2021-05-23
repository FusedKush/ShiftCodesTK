<?php
  require_once(dirname(__DIR__) . '/initialize.php');

  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('help/faq'))
    ->setTitle('FAQ')
    ->setGeneralInfo(
      'Answers to some frequently asked questions',
      'tps/4'
    )
    ->saveConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/shared-styles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/help/faq.css<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/before-content.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <?php
      $questions = [
        "SHiFT" => [
          [
            "q" => "What is SHiFT?",
            "a" => "SHiFT is a service created by Gearbox to reward their players with in-game loot and special events."
          ],
          [
            "q" => "Do you need an account to use SHiFT?",
            "a" => 'Yes. You can create a free account <a class="themed" href="https://shift.gearboxsoftware.com/registration/pre" rel="external noopener" target="_blank" title="Register for SHiFT (External Link)" aria-label="Register for SHiFT (External Link)"><span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span><span class="label">here</span></a>.</p>'
          ],
          [
            "q" => "How old do you need to be to use SHiFT?",
            "a" => "You must be at least 18 years old to sign up for SHiFT."
          ]
        ],
        "ShiftCodesTK" => [
          [
            "q" => "Is ShiftCodesTK affiliated with Gearbox or 2K?",
            "a" => "Nope, ShiftCodesTK is a completely fan-made service, and is not affiliated with Gearbox Software or 2K Games in any way."
          ],
          [
            "q" => "Can you create a SHiFT Code for me? Can you change the properties of a SHiFT Code? Can you give me a legendary?",
            "a" => "No, we have no control over the SHiFT Codes themselves, and just share the codes released by Gearbox."
          ],
          [
            "q" => "Why is ShiftCodesTK not updated as soon as SHiFT Codes are posted?",
            "a" => "At the time of writing, ShiftCodesTK does not have access to the necessary features of the Twitter & Facebook APIs to retrieve the necessary posts and automatically parse them, as well as lacking the ability to then update our own personal pages once this information has been obtained."
          ],
          [
            "q" => "Do you have any information on future titles, expansions, or events related to Borderlands?",
            "a" => "Nope, we have no information on anything regarding the future of Borderlands."
          ]
        ],
        "SHiFT Codes" => [
          [
            "q" => "What are SHiFT Codes?",
            "a" => "SHiFT Codes are 25-character keys that grant in-game rewards."
          ],
          [
            "q" => "Where are SHiFT Codes released?",
            "a" => "SHiFT Codes are typically released on Twitter and Facebook via official handles, but are also sometimes released during special events and livestreams."
          ],
          [
            "q" => "How often are SHiFT Codes released?",
            "a" => "SHiFT Codes have no strict schedule, but are often released on Friday's around 10AM PST."
          ],
          [
            "q" => "How many times can a SHiFT Code be redeemed?",
            "a" => "SHiFT Codes can be redeemed once per platform, per account."
          ],
          [
            "q" => "Do SHiFT Codes ever expire?",
            "a" => "Yes, most SHiFT Codes will be issued with an Expiration Date, typically about two to three weeks after their release. However, some SHiFT Codes have and can be issued without an Expiration Date."
          ],
          [
            "q" => "What games support SHiFT Codes?",
            "a" => 
              '
                <p>The following titles support SHiFT Codes:</p>
                  <ul class="styled">
                    <li>Borderlands: Game of the Year Edition</li>
                    <li>Borderlands 2</li>
                    <li>Borderlands: The Pre-Sequel</li>
                    <li>Borderlands 3</li>
                  </ul>
                <p><em>There are other titles that accept SHiFT Codes, such as Battleborn, but they are not supported on ShiftCodesTK.</em></p>
              '
          ],
          [
            "q" => "Can I add a SHiFT Code to ShiftCodesTK?",
            "a" => "At the moment, SHiFT Codes cannot be added to ShiftCodesTK by the community. This feature will likely be implemented in the future."
          ]
        ]
      ];

      // Add wrapper <p> tags as needed, Add IDs
      foreach ($questions as $section => &$questionList) {
        foreach ($questionList as &$question) {
          $question['id'] = clean_url($question['q']);
          $question['a'] = collapseWhitespace($question['a']);

          if (!strpos($question['a'], '<p>')) {
            $question['a'] = "<p>{$question['a']}</p>";
          }
        }
      }
    ?>
    <main class="content-wrapper">
      <header class="toc dropdown-panel">
        <button class="header dropdown-panel-toggle">
          <div class="wrapper">
            <div class="title">
              <div class="icon">
                <span class="fas fa-list-alt"></span>
              </div>
              <div class="string">
                <h2 class="primary">Table of Contents</h2>
              </div>
            </div>
            <div class="indicator">
              <span class="fas fa-chevron-right"></span>
            </div>
          </div>
        </button>
        <div class="body content-container" id="table_of_contents">
          <?php foreach ($questions as $section => &$questionList) : ?>
            <div class="section">
              <h3><a href="<?= '#' .clean_url($section); ?>"><?= $section; ?></a></h3>
              <ul class="styled">
                <?php foreach ($questionList as &$question) : ?>
                  <?php $title = clean_html('Jump to question: "' . $question['q'] . '"'); ?>
                  <li>
                    <a href="<?= "#{$question['id']}"; ?>"
                       title="<?= $title; ?>"
                       aria-label="<?= $title; ?>"><?= $question['q']; ?></a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>
        </div>
      </header>
      <?php foreach ($questions as $section => &$questionList) : ?>
        <section class="faq-group">
          <header id="<?= clean_url($section); ?>">
            <h2 class="title"><?= $section; ?></h2>
          </header>
            <?php foreach ($questionList as &$question) : ?>
              <?php $title = 'Jump to question: "' . $question['q'] . '"'; ?>
              <div class="dropdown-panel c" id="<?= $question['id']; ?>">
                <h3 class="primary"><?= $question['q']; ?></h3>
                <div class="body"><?= $question['a']; ?></div>
              </div>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endforeach; ?>
    </main>
    <!-- Support Footer -->
    <?php include_once('local/support-footer.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/after-content.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/shared-scripts.php'); ?>
  </body>
</html>
