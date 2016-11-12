<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;

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


/*

Checking for valid nonce

*/
function wps_check_valid_nonce() {

  $meta = get_user_meta(2);
  echo json_encode($meta['description']);
  die();

}

add_action('wp_ajax_wps_check_valid_nonce', 'wps_check_valid_nonce');
add_action('wp_ajax_nopriv_wps_check_valid_nonce', 'wps_check_valid_nonce');


/*

Saving auth data

*/
function wps_save_auth_data() {

  $data = $_POST['data'];
  update_user_meta(2, 'description', $data);

  echo json_encode($data);
  die();

}

add_action('wp_ajax_wps_save_auth_data', 'wps_save_auth_data');
add_action('wp_ajax_nopriv_wps_save_auth_data', 'wps_save_auth_data');



/*

Return Shopify Settings

*/
function wps_get_settings() {

  $settings = array(
    'wps_api_key' => WP_SHOPIFY_API_KEY,
    'wps_shared_secret' => WP_SHOPIFY_SHARED_SECRET,
    'wps_scopes' => WP_SHOPIFY_SCOPES,
    'wps_redirect' => WP_SHOPIFY_REDIRECT
  );

	return $settings;

}


add_action('rest_api_init', function () {
	register_rest_route('wp-shopify/v1', '/settings', array(
		'methods' => 'GET',
		'callback' => 'wps_get_settings'
	));
});


//
// Fetching Mailchimp API Key
//
function mailchimp_auth_creds() {
  return array('arobbins', '275cafe2706cafae258728fc0f11132d-us11');
}


//
// Fetching Mailchimp List ID
//
function mailinglist_get_list_id() {

  // Test list 53f4059701
  // Live list 5c6bd183d4

  echo '53f4059701';
  die();

}

add_action('wp_ajax_mailinglist_get_list_id', 'mailinglist_get_list_id');
add_action('wp_ajax_nopriv_mailinglist_get_list_id', 'mailinglist_get_list_id');


//
// Fetching Mailchimp List
//
function mailinglist_signup() {

  error_log( print_r($_POST, true) );

  $email = $_POST['email'];
  $nonce = $_POST['nonce'];

  if(wp_verify_nonce($nonce, 'mailinglist_signup')) {

    $resp = [];

    try {
      $client = new GuzzleHttp\Client(['base_uri' => 'https://us11.api.mailchimp.com/3.0/']);

      $response = $client->request('POST', 'lists/5c6bd183d4/members', [
        'auth' => [
          'arobbins', '275cafe2706cafae258728fc0f11132d-us11'
        ],
        'json' => [
          'email_address' => $email,
          "status" => "pending",
        ]
      ]);

      $statusCode = $response->getStatusCode();

      $resp['code'] = $statusCode;
      $resp['message'] = json_decode($response->getBody());

      echo json_encode($resp);
      die();

    } catch (GuzzleHttp\Exception\ClientException $e) {

      $response = $e->getResponse();
      $statusCode = $response->getStatusCode();
      $message = $e->getMessage();

      // print_r( json_encode($e->getMessage()) );
      // echo $e->getMessage();

      $resp['code'] = $statusCode;
      $resp['message'] = json_decode($response->getBody());

      echo json_encode($resp);
      die();

    }

  } else {
    echo 'Invalid Nonce';
    die();

  }

}

add_action('wp_ajax_mailinglist_signup', 'mailinglist_signup');
add_action('wp_ajax_nopriv_mailinglist_signup', 'mailinglist_signup');
