<section class="component component-mailinglist form-wrapper l-contain l-col l-row-center">

  <div class="l-col l-col-center">
    <h1>Sign up for updates</h1>
  </div>

  <form id="mailinglist-form" class="form form-lg l-row-center" action="" method="post" data-nonce="<?php echo wp_create_nonce('mailinglist'); ?>">

    <div class="form-control l-row">
      <label for="email" class="form-label">Email Address</label>
      <input name="email" id="mailinglist-email" type="text" class="form-input" />
      <?php wp_nonce_field('mailinglist_signup'); ?>
      <input class="btn form-btn" type="submit" title="Sign up" value="Sign up" />
      <div class="spinner"></div>
    </div>

    <aside class="form-messages">
      <div class="form-message form-error"></div>
      <div class="form-message form-success"></div>
    </aside>

  </form>

</section>
