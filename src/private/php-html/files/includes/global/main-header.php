<?php
  use ShiftCodesTK\PageConfiguration;
?>
<?php (function () { ?>
  <?php 
    $page_configuration = PageConfiguration::getCurrentPageConfiguration();
    $breadcrumbs = [
      'title'       => $page_configuration->getTitle(),
      'description' => $page_configuration->getGeneralInfo('description'),
      'path'        => $page_configuration->getPath(),
      'banner'      => $page_configuration->getImage(PageConfiguration::IMAGE_FORMAT_BANNER)
    ];
  ?>
  <header class="main" id="primary_header">
    <div class="intro" data-webp='{"path": "<?= $breadcrumbs['banner']; ?>", "alt": ".jpg", "type": "bg"}'>
      <div class="content-container">
        <div class="content-wrapper">
          <div class="content">
            <h1 class="title"><?= $breadcrumbs['title']; ?></h1>
            <div class="description"><?= $breadcrumbs['description']; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="breadcrumbs ready">
      <div class="content-wrapper">
        <?php
          (function () use ($page_configuration) {
            $parents = $page_configuration->getParents();
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
            
            echo $page_configuration->getBreadcrumb();
          })();
        ?>
      </div>
    </div>
  </header>
<?php })(); ?>
