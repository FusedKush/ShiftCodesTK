<?php
  class errorObject {};

  $errorNames = new errorObject;
  $errorDescriptions = new errorObject;

  $errorNames->e400 = 'Bad Request';
  $errorNames->e401 = 'Unauthorized';
  $errorNames->e403 = 'Forbidden';
  $errorNames->e404 = 'Not Found';
  $errorNames->e408 = 'Request Timeout';
  $errorNames->e500 = 'Internal Server Error';
  $errorNames->e503 = 'Service Unavailable';

  $errorDescriptions->e400 = "We cannot seem to process your request. Please try again later.";
  $errorDescriptions->e401 = "You don't seem to be authorized to be here. Don't worry, we won't tell anybody.";
  $errorDescriptions->e403 = "You don't seem to be allowed in here. Don't worry, we'll keep it between us.";
  $errorDescriptions->e404 = "We can't seem to find what you're looking for. Check the url and try again.";
  $errorDescriptions->e408 = "Your request seems to have timed out. Please try again later.";
  $errorDescriptions->e500 = "Our server seems to have encountered an error while processing your request. Please try again later.";
  $errorDescriptions->e503 = "Our service seems to be currently unavailable. Sorry about that.";

  $errorCode = $_GET['statusCode'];
  $errorCodeName = 'e' . $errorCode;
  $errorTitle = $errorCode . ' ' . $errorNames->$errorCodeName;
  $errorDescription = $errorDescriptions->$errorCodeName;
  $currentURL = $_SERVER['REQUEST_URI'];
?><!doctypehtml><html lang=en><meta charset=utf-8><title><?php echo $errorTitle . ' - ShiftCodesTK'?></title><meta name=icon href=/favicon.ico type=image/x-icon><meta name=viewport content="width=device-width,initial-scale=1"><style>body{background-color:#0f1e2d}body *{opacity:0}</style><link href="/assets/styles/css/min/global.min.css?v=1"rel=stylesheet><link href="/assets/styles/css/min/errordocs/errorDocument.min.css?v=1"rel=stylesheet><body data-theme=main><main class="content-wrapper no-header"><img alt="ShiftCodesTK Logo"class=logo src=/assets/img/logo.svg><div class=info><h1 class=title><?php echo $errorTitle; ?></h1><span class=currentURL><?php echo $currentURL ?></span><p class=description><?php echo $errorDescription; ?></div><a aria-label="ShiftCodesTK Home"class=return href=/ title="ShiftCodesTK Home">Back to the SHiFT Codes</a></main>