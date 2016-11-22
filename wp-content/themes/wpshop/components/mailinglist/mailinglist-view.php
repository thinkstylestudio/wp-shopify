<section class="component component-mailinglist form-wrapper l-col l-row-center" id="mailinglist">

  <!-- <div class='wave'></div> -->

  <div class="waves">
  <div class="wave wave_1"></div>
      <div class="wave wave_2"></div>
      <div class="wave wave_3"></div>
      <div class="wave wave_4"></div>
      <div class="wave wave_5"></div>
</div>

  <div class="mailinglist-group-copy l-col l-col-center">
    <h1 class="mailinglist-heading">Stay in the loop</h1>
    <p class="mailinglist-copy">Sign up below to know when WPS is ready!</p>
  </div>

  <form id="mailinglist-form" class="form form-lg l-row-center" action="" method="post" data-nonce="<?php echo wp_create_nonce('mailinglist'); ?>">

    <div class="form-control l-row">
      <label for="email" class="form-label form-label-l">Email Address</label>
      <input name="email" id="mailinglist-email" type="text" class="form-input form-input-l" />
      <?php wp_nonce_field('mailinglist_signup'); ?>

      <div class="btn-group l-row-center">
        <button class="btn form-btn" type="submit" title="Sign up" value="Sign up" />Sign me up</button>
        <div class="btn-bg"></div>
      </div>

      <div class="spinner"></div>
    </div>

    <aside class="form-messages">
      <div class="form-message form-error"></div>
      <div class="form-message form-success"></div>
    </aside>

  </form>

</section>
