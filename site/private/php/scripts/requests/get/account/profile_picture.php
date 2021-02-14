<?php
  use ShiftCodesTK\Strings;

  const IMAGE_SIZES = [
    'min' => 64,
    'max' => 512
  ];
  const IMAGE_TYPES = [
    'jpg'  => 'jpeg',
    'jpeg' => 'jpeg',
    'png'  => 'png',
    'webp' => 'webp'
  ];

  $path = $_GET['path'] ?? null;
  $size = (int) ($_GET['size'] ?? IMAGE_SIZES['min']);
  $response = new ResponseObject();

  if ($size % 2 !== 0 || !ShiftCodesTK\Validations\check_range($size, IMAGE_SIZES)) {
    $response->fatalError(-1, errorObject('InvalidImageSize', 'size', 'An invalid image size was provided.', $size));
    exit;
  }

  if ($path) {
    $image = [];

    $fullPath = (function () use ($path, $size, &$image) {
      $pathObj = new Strings\StringObj($path);
      $pathPieces = $pathObj->preg_match('/^([\w\d\/_-]+)\/([\w\d_-]+)\.([\w\d]+)$/', Strings\PREG_RETURN_SUB_MATCHES);
      /** @var array The pieces of the full image path. */
      $image = [
        /** @var string The path to the profile picture */
        'path' => $pathPieces[0],
        /** @var string The filename of the profile picture */
        'name' => $pathPieces[1],
        /** @var string The parent directories of the file, made up of the first four characters of the filename */
        'dirs' => implode('/', 
                    (new Strings\StringObj($pathPieces[1]))
                    ->slice(0, 4)
                    ->split(2)
                  ),
        /** @var string The filename extension without the leading dot (`.`) */
        'ext' => $pathPieces[2]
      ];

      return \ShiftCodesTK\PRIVATE_PATHS['root'] . "img/users/profiles/${image['path']}/{$image['dirs']}/{$image['name']}/{$size}x{$size}.{$image['ext']}";
    })();

    if (file_exists($fullPath)) {
      $imageType = IMAGE_TYPES[$image['ext']];

      response_type("image/{$imageType}");
      readfile($fullPath);
      exit;
    } 
  }

  response_http(404, true);
?>