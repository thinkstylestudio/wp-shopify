<?php use Roots\Sage\Titles; ?>

<?php if(!get_field('page_settings_hide_title')) { ?>

  <div class="page-header">
    <h1><?= Titles\title(); ?></h1>
  </div>

<?php } ?>
