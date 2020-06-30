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
?><link href="/assets/css/shared/global.css<?= TK_VERSION_STR; ?>"rel=stylesheet><link href="/assets/css/errordocs/error-notice.css<?= TK_VERSION_STR; ?>"rel=stylesheet><title><?= ERROR_NOTICE_TITLE . ' - ShiftCodesTK'; ?></title><div class=error-notice><div class=content-wrapper><img alt="ShiftCodesTK Logo"class=logo src=/assets/img/logo.svg><div class=info><h1 class=title><?= ERROR_NOTICE_TITLE; ?></h1><?php if (ERROR_NOTICE_CODE > 3) : ?><span class=currentURL><?= ERROR_NOTICE_URL; ?></span><?php endif; ?><p class=description><?= ERROR_NOTICE_DESCRIPTION ?></div><?php if (ERROR_NOTICE_CODE > 3) : ?><a aria-label="ShiftCodesTK Home"class="action return"href=/ title="ShiftCodesTK Home"><span>Back to the SHiFT Codes</span></a><?php elseif (ERROR_NOTICE_CODE == -6) : ?><?php
        $href = preg_replace("/(^|\&)cookies_disabled=1/", "", $_SERVER['QUERY_STRING']);
        $href = $href ? "?$href" : "";
      ?><a aria-label="Refresh the current page and try again"class=action href="<?= $href; ?>"title="Refresh the current page and try again"><span>Refresh</span></a><?php else : ?><a aria-label="Refresh the current page and try again"class=action href=""title="Refresh the current page and try again"><span>Refresh</span></a><?php endif; ?></div></div>