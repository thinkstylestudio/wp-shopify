<?php

  if(have_rows('components')):

    while(have_rows('components')) : the_row();

      // Mailing List
      if(get_row_layout() == 'component_mailinglist') {

        get_template_part('components/mailinglist/mailinglist-controller');

      // Default
      } else if(get_row_layout() == 'component_default') {

        get_template_part('components/default/default-controller');

      }

    endwhile;

  else:

  endif;

?>
