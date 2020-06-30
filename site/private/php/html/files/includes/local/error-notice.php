<?php
  /* Call errorNotice() to use this script. */

  /**
   * The provided error
   */
  define('ERROR_NOTICE_ERROR', STATUS_CODES[ERROR_NOTICE_CODE]);
  // Error Display Title
  (function () {
    $title = '';

    if (ERROR_NOTICE_CODE > 3) { $title = ERROR_NOTICE_CODE . ' ' . ERROR_NOTICE_ERROR['name']; }
    else                       { $title = ERROR_NOTICE_ERROR['name']; }

    /**
     * The display title of the error
     */
    define('ERROR_NOTICE_TITLE', $title);
  })();
  // Error Display URL
  define('ERROR_NOTICE_URL', $_SERVER['REQUEST_URI']);
  /**
   * The display discription of the error
   */
  define('ERROR_NOTICE_DESCRIPTION', ERROR_NOTICE_ERROR['description']);
?>

<link rel="stylesheet" href="/assets/css/shared/global.css<?= TK_VERSION_STR; ?>">
<link rel="stylesheet" href="/assets/css/errordocs/error-notice.css<?= TK_VERSION_STR; ?>">

<title><?= ERROR_NOTICE_TITLE . ' - ShiftCodesTK'; ?></title>
  
<div class="error-notice">
  <div class="content-wrapper">
    <img class="logo" src="/assets/img/logo.svg" alt="ShiftCodesTK Logo">
    <div class="info">
      <h1 class="title"><?= ERROR_NOTICE_TITLE; ?></h1>  

      <?php if (ERROR_NOTICE_CODE > 3) : ?>
      <span class="currentURL"><?= ERROR_NOTICE_URL; ?></span>
      <?php endif; ?>

      <p class="description"><?= ERROR_NOTICE_DESCRIPTION ?></p>
    </div>
    
    <?php if (ERROR_NOTICE_CODE > 3) : ?>
      <a 
        class="action return" 
        href="/" 
        title="ShiftCodesTK Home" 
        aria-label="ShiftCodesTK Home">
        <span>Back to the SHiFT Codes</span>
      </a>
    <?php elseif (ERROR_NOTICE_CODE == -6) : ?>
      <?php
        $href = preg_replace("/(^|\&)cookies_disabled=1/", "", $_SERVER['QUERY_STRING']);
        $href = $href ? "?$href" : "";
      ?>

      <a 
        class="action" 
        href="<?= $href; ?>" 
        title="Refresh the current page and try again" 
        aria-label="Refresh the current page and try again">
        <span>Refresh</span>
      </a>
    <?php else : ?>
      <a 
        class="action" 
        href=" " 
        title="Refresh the current page and try again" 
        aria-label="Refresh the current page and try again">
        <span>Refresh</span>
      </a>
    <?php endif; ?>
  </div>
</div>