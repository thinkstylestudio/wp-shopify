<section class="component component-faq">

  <?php if( have_rows('theme_faqs_cats', 'option') ):

    // loop through the rows of data
    while ( have_rows('theme_faqs_cats', 'option') ) : the_row();

      $cat = get_sub_field('cat_name');

    ?>

      <div class="faqs-group">
        <h2 class="faqs-heading"><?php the_sub_field('cat_name'); ?></h2>

        <dl class="faqs">
        <?php if( have_rows('cat_faqs') ):

          // loop through the rows of data
          while ( have_rows('cat_faqs') ) : the_row(); ?>

            <dt class="faq-question">
              <i class="fa fa-plus"></i> <?php the_sub_field('question'); ?>
            </dt>
            <dd class="faq-answer is-gone">
              <?php the_sub_field('answer'); ?>
            </dd>

          <?php endwhile;

          else :

          echo "No FAQs found under " . $cat . 'category';

        endif; ?>
        
        </dl>
      </div>

    <?php endwhile;

    else :

    echo "No FAQs found";

  endif;

  ?>


</section>
