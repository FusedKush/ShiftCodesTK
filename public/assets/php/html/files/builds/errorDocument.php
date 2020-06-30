<?php
  class ErrorNames {
    public $e400 = 'Bad Request';
    public $e401 = 'Unauthorized';
    public $e403 = 'Forbidden';
    public $e404 = 'Not Found';
    public $e408 = 'Request Timeout';
    public $e500 = 'Internal Server Error';
    public $e503 = 'Service Unavailable';
  }
  class ErrorDescriptions {
    public $e400 = "We cannot seem to process your request. Please try again later.";
    public $e401 = "You don't seem to be authorized to be here. Don't worry, we won't tell anybody.";
    public $e403 = "You don't seem to be allowed in here. Don't worry, we'll keep it between us.";
    public $e404 = "We can't seem to find what you're looking for. Check the url and try again.";
    public $e408 = "Your request seems to have timed out. Please try again later.";
    public $e500 = "Our server seems to have encountered an error while processing your request. Please try again later.";
    public $e503 = "Our service seems to be currently unavailable. Sorry about that.";
  }

  $errorNames = new ErrorNames;
  $errorDescriptions = new ErrorDescriptions;

  $errorCode = $_GET['statusCode'];
  $errorCodeName = 'e' . $errorCode;
  $errorTitle = $errorCode . ' ' . $errorNames->$errorCodeName;
  $errorDescription = $errorDescriptions->$errorCodeName;
  $currentURL = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Styles -->
    <meta charset="utf-8">
    <style>
      body {
        background-color: #0f1e2d;
      }
      body * {
        opacity: 0;
      }
    </style>
    <link rel="stylesheet" href="/assets/styles/css/min/shared/global.min.css?v=1">
    <link rel="stylesheet" href="/assets/styles/css/min/errordocs/errorDocument.min.css?v=1">
    <!-- Important Metadata -->
    <title><?php echo $errorTitle . ' - ShiftCodesTK'?></title>
    <meta name="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body data-theme="main">
    <main class="no-header content-wrapper">
      <img class="logo" src="/assets/img/logo.svg" alt="ShiftCodesTK Logo">
      <div class="info">
        <h1 class="title">
          <?php echo $errorTitle; ?>
        </h1>
        <span class="currentURL">
          <?php echo $currentURL ?>
        </span>
        <p class="description">
          <?php echo $errorDescription; ?>
        </p>
      </div>
      <a class="return" href="/" title="ShiftCodesTK Home" aria-label="ShiftCodesTK Home"><span>Back to the SHiFT Codes</span></a>
    </main>
  </body>
</html>
