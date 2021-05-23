<?php
  use ShiftCodesTK\PageConfiguration;
  
  $t_breadcrumbs = [
    'page_configuration' => PageConfiguration::getCurrentPageConfiguration()
  ];
  $t_breadcrumbs = array_merge($t_breadcrumbs, [
    'title'       => $t_breadcrumbs['page_configuration']->getTitle(),
    'description' => $t_breadcrumbs['page_configuration']->getGeneralInfo('description'),
    'path'        => $t_breadcrumbs['page_configuration']->getPath(),
    'banner'      => $t_breadcrumbs['page_configuration']->getImage(PageConfiguration::IMAGE_FORMAT_BANNER)
  ]);
?>
<header class="main" id="primary_header">
  <div class="intro" data-webp='{"path": "<?= $t_breadcrumbs['banner']; ?>", "alt": ".jpg", "type": "bg"}'>
    <div class="content-container">
      <div class="content-wrapper">
        <div class="content">
          <h1 class="title"><?= $t_breadcrumbs['title']; ?></h1>
          <div class="description"><?= $t_breadcrumbs['description']; ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcrumbs ready">
    <div class="content-wrapper">
      <?php
        (function () use ($t_breadcrumbs) {
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
          
          echo $t_breadcrumbs['page_configuration']->getBreadcrumb();
        })();
      ?>
    </div>
  </div>
</header>

<?php unset($t_breadcrumbs); ?>
