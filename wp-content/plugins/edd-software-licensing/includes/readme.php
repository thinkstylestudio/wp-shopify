<?php

/**
 * Parse the ReadMe URL
 *
 * @since  2.4
 * @link https://github.com/markjaquith/WordPress-Plugin-Readme-Parser/tree/WordPress.org WordPress_Readme_Parser package
 * @uses WordPress_Readme_Parser::parse_readme()
 * @param  string $url URL of the readme.txt file
 * @return array  Processed readme.txt
 */
function _edd_sl_readme_parse( $url = '' ) {

	if( ! class_exists( 'Automattic_Readme' ) ) {
		include_once( EDD_SL_PLUGIN_DIR . 'includes/parse-readme.php' );
	}

	$request = wp_remote_get( $url, array(
		'timeout' => 15,
		'sslverify' => false,
		'sslcertificates' => NULL
	) );

	if( ! empty( $request) && !is_wp_error( $request ) ) {

		$body = $request['body'];

		$Parser = new Automattic_Readme;

		return $Parser->parse_readme_contents( $request['body'] );
	}

	return false;
}


/**
 * Fetch the readme.txt data from cache or fresh.
 *
 * Use `cache` query string to force a fresh download of the readme.
 *
 * @since  2.4
 * @uses  _edd_sl_readme_parse()	Process the readme data
 * @param  string $readme_url URL of the readme.
 * @return boolean|array             False if not exists, array of data if exists.
 */
function _edd_sl_get_readme_data( $readme_url = '', $post_id = NULL ) {

	// Use cached readme for this version
	$readme = get_transient( _edd_sl_readme_get_transient_key( $post_id ) );

	// If the cache doesn't exist or overridden
	if( empty( $readme ) || isset( $_REQUEST['cache'] ) ) {

		if( $readme = _edd_sl_readme_parse( $readme_url) ) {

			// Store the parsed readme for a week.
			set_transient( _edd_sl_readme_get_transient_key( $post_id ), $readme, HOUR_IN_SECONDS * 6 );
		}
	}

	return $readme;
}

/**
 * Tap into the filter to use data from a readme.txt file
 *
 * @since  2.4
 * @see  EDD_Software_Licensing::get_latest_version_remote()
 * @param  array  $original_response License response array
 * @param  WP_Post $download          Post object of the Download item
 * @return array                    Modified array, if readme exists. Otherwise, original array is returned.
 */
function edd_sl_readme_modify_license_response( $response = array(), $download = NULL ) {

	if( is_admin() || defined( 'DOING_AJAX' ) ) {
		// Prevent errors and send headers
		ini_set( 'display_errors', 0 );
		ini_set( 'log_errors', 1 );
		error_reporting(0);
		define( 'DOING_AJAX', true );
		@header( 'Content-type: text/plain' );
		@send_nosniff_header();
	}

	// Get the URL to use in the WP.org validator
	$readme_url = get_post_meta( $download->ID, '_edd_readme_location', true );

	// If the URL doesn't exist, get outta here.
	if( empty( $readme_url) ) { 
		return $response;
	}

	// Fetch the cached/fresh readme data
	$readme = _edd_sl_get_readme_data( $readme_url, $download->ID );

	// The readme didn't exist or process. Return existing response.
	if( empty( $readme ) ) {
		return $response;
	}

	// Modify the homepage linked to in the Update Notice
	$response['homepage'] = edd_sl_readme_get_download_homepage( $download->ID );

	// Set the slug
	$response['new_version'] = edd_software_licensing()->get_latest_version( $download->ID );

	// The original response sections
	$response['sections'] = maybe_unserialize( @$response['sections'] );

	// Get the override readme sections settings
	if( $readme_sections = get_post_meta( $download->ID, '_edd_readme_sections', true ) ) {

		// We loop through the settings sections and make overwrite the
		// existing sections with the custom readme.txt sections.
		foreach ( (array) $readme_sections as $section ) {
			$response['sections'][$section] = $readme['sections']["$section"];
		}
	}

	if ( ! empty( $readme['tested_up_to'] ) ) {
		$response['tested'] = $readme['tested_up_to'];
	}

	// Reserialize it
	$response['sections'] = serialize( $response['sections'] );

	// Get the override readme meta settings
	if( $readme_meta = get_post_meta( $download->ID, '_edd_readme_meta', true ) ) {

		// We loop through the settings sections and make overwrite the
		// existing sections with the custom readme.txt sections.
		foreach ( (array) $readme_meta as $meta ) {
			$response[$meta] = $readme["$meta"];
		}
	}

	if( get_post_meta( $download->ID, '_edd_readme_plugin_added', true ) ) {
		$response['added'] = date( 'Y-m-d', strtotime( $download->post_date_gmt, current_time( 'timestamp' ) ) );
	}

	if( get_post_meta( $download->ID, '_edd_readme_plugin_last_updated', true ) ) {
		$response['last_updated'] = apply_filters( 'edd_sl_readme_last_updated', human_time_diff( strtotime( $download->post_modified_gmt, current_time( 'timestamp' ) ), current_time( 'timestamp', 1)).' ago', $download );
	}

	// Remove empty items
	$response = array_filter( $response );

	// Filter this if you want to.
	return apply_filters( 'edd_sl_license_readme_response', $response, $download, $readme );

}
add_filter( 'edd_sl_license_response', 'edd_sl_readme_modify_license_response', 10, 2);

/**
 * Get the custom homepage for the download. If not set, return download item URL.
 * @since  2.4
 * @param  int $download_id Download ID
 * @return string              URL of download.
 */
function edd_sl_readme_get_download_homepage( $download_id  ) {

	$custom_homepage = get_post_meta( $download_id, '_edd_readme_plugin_homepage', true );

	return empty( $custom_homepage) ? get_permalink( $download_id ) : $custom_homepage;

}

/**
 * The readme.txt files are cached. This outputs the cache status and a button to clear the cache.
 */
function edd_sl_render_readme_cache_status( ) {

	$deleted = NULL; // delete_transient returns false, so we don't want to use false
	if(isset( $_REQUEST['refresh'] ) && $_REQUEST['refresh'] === 'readmecache' ) {
		$deleted = delete_transient( _edd_sl_readme_get_transient_key() );
	}

	if( ! empty( $deleted ) ) {
		echo '<div class="updated inline">';
		echo wpautop( __( 'The cache has been deleted.', 'edd_sl' ) );
		echo '</div>';
	} elseif(!is_null( $deleted ) ) {
		echo '<div class="error inline">';
		echo wpautop( __( 'There was an error when deleting the cache. It may have already been deleted.', 'edd_sl' ) );
		echo '</div>';
	}

	$readme = get_transient(_edd_sl_readme_get_transient_key() );

	// The readme has been cached. Show the reset
	if( ! empty( $readme ) ) {
		$message = sprintf( __( 'the file has been cached. %sClear cached file%s', 'edd_sl' ), '<a href="' . esc_url( add_query_arg( array( 'refresh' => 'readmecache' ) ) ) . '#edd_readme_cache" class="button button-secondary">', '</a>' );
	} else {
		$message = __( 'the file is not cached.', 'edd_sl' );
	}

	echo '<div class="alignright" id="edd_readme_cache">';
	printf( wpautop( '<strong>%s</strong> %s'), __( 'Cache:', 'edd_sl' ), $message );
	echo '</div>';
}

/**
 * Get the cache key for the cached readme
 * @param  int  $post_id The ID of the download
 * @return string          Transient key
 */
function _edd_sl_readme_get_transient_key( $post_id = NULL ) {

	global $post;

	// Get the download ID
	$post_id = empty( $post_id ) ? $post->ID : $post_id;

	// Get the version of the plugin
	$version = empty( $version ) ? get_post_meta( $post_id, '_edd_sl_version', true ) : $version;

	// Use the URL as part of the transient key.
	$url_hash = hash( 'adler32', get_post_meta( $post_id, '_edd_readme_location', true ) );

	return sprintf( 'readme_%d_%s_%s', $post_id, $version, $url_hash );
}

/**
 * Verify that a string is structured as an URL.
 *
 * It checks if after parsing the URL that the `scheme` and `host` keys are set
 * and that the scheme is either `http` or `https`.
 *
 * @param  string $url    String to check
 * @return boolean         True: URL is valid, False: URL is not valid.
 */
function edd_sl_is_valid_readme_url( $url ) {

	// Test if the $url string is formatted as an URL
	$test_url = parse_url( $url );

	return ( isset( $test_url['scheme'] ) && isset( $test_url['host'] ) && in_array( $test_url['scheme'], array( 'http', 'https' ) ) );
}
