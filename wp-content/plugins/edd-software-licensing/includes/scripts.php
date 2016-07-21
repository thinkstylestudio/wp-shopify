<?php

/**
 * Enqueue admin scripts
 *
 * @since 2.6
 */
function edd_sl_admin_scripts() {
	$screen = get_current_screen();

	if ( ! is_object( $screen ) ) {
		return;
	}

	$allowed_screens = array(
		'download',
		'download_page_edd-licenses',
		'download_page_edd-license-renewal-notice',
		'download_page_edd-reports',
		'download_page_edd-settings'
	);

	$allowed_screens = apply_filters( 'edd-sl-admin-script-screens', $allowed_screens );

	if( ! in_array( $screen->id, $allowed_screens ) ) {
		return;
	}

	wp_enqueue_script( 'edd-sl-admin', plugins_url( '/js/edd-sl-admin.js', EDD_SL_PLUGIN_FILE ), array( 'jquery' ) );

	if( $screen->id === 'download' ) {
		wp_localize_script( 'edd-sl-admin', 'edd_sl', array( 'download' => get_the_ID(), 'no_prices' => __( 'N/A', 'edd_sl' ) ) );
	}

	wp_enqueue_style( 'edd-sl-admin-styles', plugins_url( '/css/edd-sl-admin.css', EDD_SL_PLUGIN_FILE ), false, EDD_SL_VERSION );
	wp_enqueue_style( 'edd-sl-styles', plugins_url( '/css/edd-sl.css', EDD_SL_PLUGIN_FILE ), false, EDD_SL_VERSION );

}
add_action( 'admin_enqueue_scripts', 'edd_sl_admin_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since 3.2
 */
function edd_sl_scripts() {
	global $post;

	if ( ! is_object( $post ) ) {
		return;
	}

	if( ! function_exists( 'edd_is_checkout' ) ) {
		return;
	}

	$load_scripts_manually = apply_filters( 'edd_sl_load_styles', false );

	wp_register_style( 'edd-sl-styles', plugins_url( '/css/edd-sl.css', EDD_SL_PLUGIN_FILE ), false, EDD_SL_VERSION );

	if ( is_admin() || edd_is_checkout() || has_shortcode( $post->post_content, 'purchase_history' ) || has_shortcode( $post->post_content, 'edd_license_keys' ) || true === $load_scripts_manually ) {
		wp_enqueue_style( 'edd-sl-styles' );
	}

}
add_action( 'wp_enqueue_scripts', 'edd_sl_scripts' );

/**
 * Output the SL JavaScript for the checkout page
 *
 * @since  3.2
 * @return void
 */
function edd_sl_checkout_js() {

	if( ! function_exists( 'edd_is_checkout' ) ) {
		return;
	}

	if ( ! edd_is_checkout() ) {
		return;
	}
?>
	<script>
	jQuery(document).ready(function($) {
		$('#edd_sl_show_renewal_form, #edd-cancel-license-renewal').click(function(e) {
			e.preventDefault();
			$('#edd-license-key-container-wrap,#edd_sl_show_renewal_form,.edd-sl-renewal-actions').toggle();
			$('#edd-license-key').focus();
		});

		$('#edd-license-key').keyup(function(e) {
			var input  = $('#edd-license-key');
			var button = $('#edd-add-license-renewal');

			if ( input.val() != '' ) {
				button.prop("disabled", false);
			} else {
				button.prop("disabled", true);
			}
		});
	});
	</script>
<?php
}
add_action( 'wp_head', 'edd_sl_checkout_js' );
