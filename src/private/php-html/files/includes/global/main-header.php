<?php
  use ShiftCodesTK\Strings,
      ShiftCodesTK\PageConfiguration;
  
  $__breadcrumbs = [
    'page_configuration' => PageConfiguration::getCurrentPageConfiguration()
  ];
  $__breadcrumbs = array_merge($__breadcrumbs, [
    'title'       => $__breadcrumbs['page_configuration']->getTitle(),
    'description' => $__breadcrumbs['page_configuration']->getGeneralInfo('description'),
    'path'        => $__breadcrumbs['page_configuration']->getPath(),
    'banner'      => $__breadcrumbs['page_configuration']->getImage(PageConfiguration::IMAGE_FORMAT_BANNER)
  ]);
?>
<header class="main" id="primary_header">
  <div class="intro" data-webp='{"path": "<?= $__breadcrumbs['banner']; ?>", "alt": ".jpg", "type": "bg"}'>
    <div class="content-container">
      <div class="content-wrapper">
        <div class="content">
          <h1 class="title"><?= $__breadcrumbs['title']; ?></h1>
          <div class="description"><?= $__breadcrumbs['description']; ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcrumbs ready">
    <div class="content-wrapper">
      <?php
        $parents = PageConfiguration::getCurrentPageConfiguration()->getParents();
        $parent_names = array_keys($parents);

        /**
         * @var string $parent_path
         * @var PageConfiguration $parent_configuration
         */
        for ($i = (count($parents) - 1); $i >= 0; $i--) {
          $parent_name = $parent_names[$i];
          $parent_configuration = $parents[$parent_name];
          
          if (!$parent_configuration) {
            continue;
          }
  
          echo $parent_configuration->getBreadcrumb();
          echo <<<EOT
            <span class="separator" aria-hidden="true">/</span>
          EOT;
        }
        
        echo $__breadcrumbs['page_configuration']->getBreadcrumb();
      ?>
    </div>
  </div>
</header>
