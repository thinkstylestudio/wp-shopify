<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);









// $data = get_userdata( get_current_user_id() );
//
// if ( is_object( $data) ) {
//     $current_user_caps = $data->allcaps;
//
//     // print it to the screen
//     echo '<pre>' . print_r( $current_user_caps, true ) . '</pre>';
// }



function wp_shopify_check_valid_nonce() {

  $meta = get_user_meta(2);
  echo json_encode($meta['description']);
  die();

}

add_action('wp_ajax_wp_shopify_check_valid_nonce', 'wp_shopify_check_valid_nonce');
add_action('wp_ajax_nopriv_wp_shopify_check_valid_nonce', 'wp_shopify_check_valid_nonce');


function wp_shopify_save_auth_data() {

  $data = $_POST['data'];
  update_user_meta(2, 'description', $data);

  echo json_encode($data);
  die();

}

add_action('wp_ajax_wp_shopify_save_auth_data', 'wp_shopify_save_auth_data');
add_action('wp_ajax_nopriv_wp_shopify_save_auth_data', 'wp_shopify_save_auth_data');




/*

Return Shopify Settings

*/
function wp_shopify_get_settings() {

  $settings = array(
    'wp_shopify_api_key' => WP_SHOPIFY_API_KEY,
    'wp_shopify_shared_secret' => WP_SHOPIFY_SHARED_SECRET,
    'wp_shopify_scopes' => WP_SHOPIFY_SCOPES,
    'wp_shopify_redirect' => WP_SHOPIFY_REDIRECT
  );

	return $settings;

}


add_action('rest_api_init', function () {
	register_rest_route('wp-shopify/v1', '/settings', array(
		'methods' => 'GET',
		'callback' => 'wp_shopify_get_settings'
	));
});
