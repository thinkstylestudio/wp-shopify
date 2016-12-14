<header class="header">

  <div class="header-content l-row l-row-justify l-contain">
    <a class="logo-link" href="<?= esc_url(home_url('/')); ?>">
      <img src="<?php the_field('theme_logo_primary', 'option'); ?>" alt="WP Shopify" class="logo-header">
    </a>

    <?php if (has_nav_menu('primary_navigation')) : ?>
      <nav class="nav-primary l-row l-col-center">
        <?php wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav l-row']); ?>
      </nav>
    <?php endif; ?>
  </div>

</header>
