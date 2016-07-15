<header class="l-contain page-header">
  <?php
    the_archive_title( '<h1 class="page-title">', '</h1>' );
    the_archive_description( '<div class="taxonomy-description">', '</div>' );
  ?>
</header><!-- .page-header -->

<?php

  $strings = array("author", "/");
  $niceName = str_replace($strings, "", $_SERVER['REQUEST_URI']);

  $args = array(
    'post_type' => 'post',
    'posts_per_page' => 6,
    'author_name' => $niceName
  );

  $loop = new WP_Query($args);

  while($loop->have_posts()) : $loop->the_post();
    $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID)); ?>

      <article <?php post_class('l-contain'); ?>>
        <header class="l-contain">
          <div class="l-row l-row-center author-image-wrapper">

            <?php

              $cats = get_the_category();

              if(sizeof($cats) > 1) {
                foreach($cats as $key => $value) :
                  $catName = $value->cat_name;
                  $catID = get_cat_ID($catName);
                  $catLink = get_category_link($catID);
                  $catImage = z_taxonomy_image_url($catID);

                  include(locate_template('templates/categories.php'));

                endforeach;

              } else {
                $catName = $cats[0]->name;
                $catID = get_cat_ID($catName);
                $catLink = get_category_link( $catID );
                $catImage = z_taxonomy_image_url($catID);

                include(locate_template('templates/categories.php'));

              }

            ?>

          </div>

          <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php get_template_part('templates/entry-meta'); ?>
          <img src="<?php echo $feat_image; ?>" alt="<?php the_title(); ?>">

        </header>

        <div class="entry-summary">
          <?php the_excerpt(); ?>
        </div>

      </article>
    <?php
  endwhile;

  wp_reset_postdata();

?>
