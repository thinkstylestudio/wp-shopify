<?php

function edd_sl_register_license_section( $sections ) {
	$sections['software-licensing'] = __( 'Software Licensing', 'edd_sl' );

	return $sections;
}
add_filter( 'edd_settings_sections_extensions', 'edd_sl_register_license_section', 10, 1 );

/**
 * Registers the new Software Licensing license options in Misc
 * *
 * @access      private
 * @since       1.0
 * @param 		$settings array the existing plugin settings
 * @return      array
*/

function edd_sl_license_settings( $settings ) {

	$license_settings = array(
		array(
			'id' => 'edd_sl_header',
			'name' => '<strong>' . __( 'Software Licensing', 'edd_sl' ) . '</strong>',
			'desc' => '',
			'type' => 'header',
			'size' => 'regular'
		),
		array(
			'id' => 'edd_sl_force_increase',
			'name' => __( 'Disable URL Checking?', 'edd_sl' ),
			'desc' => __( 'Check this box if your software is not tied to to URLs. If you sell desktop software, check this.', 'edd_sl' ),
			'type' => 'checkbox'
		),
		array(
			'id' => 'edd_sl_bypass_local_hosts',
			'name' => __( 'Ignore Local Host URLs?', 'edd_sl' ),
			'desc' => __( 'Allow local development domains and IPs to be activated without counting towards the activation limit totals. The URL will still be logged.', 'edd_sl' ),
			'type' => 'checkbox'
		),
		array(
			'id' => 'edd_sl_readme_parsing',
			'name' => __( 'Selling WordPress Plugins?', 'edd_sl' ),
			'desc' => __( 'Check this box if you are selling WordPress plugins and wish to enable advanced ReadMe.txt file parsing.', 'edd_sl' ),
			'type' => 'checkbox'
		),
		array(
			'id' => 'edd_sl_renewals',
			'name' => __( 'Allow Renewals', 'edd_sl' ),
			'desc' => __( 'Check this box if you want customers to be able to renew their license keys.', 'edd_sl' ),
			'type' => 'checkbox'
		),
		array(
			'id' => 'edd_sl_renewal_discount',
			'name' => __( 'Renewal Discount', 'edd_sl' ),
			'desc' => __( 'Enter a discount amount as a percentage, such as 10. Or enter 0 for no discount.', 'edd_sl' ),
			'type' => 'text',
			'size' => 'small'
		),
		array(
			'id' => 'edd_sl_send_renewal_reminders',
			'name' => __( 'Send Renewal Reminders', 'edd_sl' ),
			'desc' => __( 'Check this box if you want customers to receive a renewal reminder when their license key is about to expire.', 'edd_sl' ),
			'type' => 'checkbox'
		),
		array(
			'id' => 'sl_renewal_notices',
			'name' => __( 'Renewal Notices', 'edd_sl' ),
			'desc' => __( 'Configure the renewal notice emails', 'edd_sl' ),
			'type' => 'hook'
		),
	);

	if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
		$license_settings = array( 'software-licensing' => $license_settings );
	}

	return array_merge( $settings, $license_settings );

}
add_filter('edd_settings_extensions', 'edd_sl_license_settings');

/**
 * Displays the renewal notices options
 *
 * @access      public
 * @since       3.0
 * @param 		$args array option arguments
 * @return      void
*/
function edd_sl_renewal_notices_settings( $args ) {

	$notices = edd_sl_get_renewal_notices();
	//echo '<pre>'; print_r( $notices ); echo '</pre>';
	ob_start(); ?>
	<table id="edd_sl_renewal_notices" class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th class="edd-sl-renewal-subject-col" scope="col"><?php _e( 'Subject', 'edd_sl' ); ?></th>
				<th class="edd-sl-renewal-period-col" scope="col"><?php _e( 'Send Period', 'edd_sl' ); ?></th>
				<th scope="col"><?php _e( 'Actions', 'edd_sl' ); ?></th>
			</tr>
		</thead>
		<?php if( ! empty( $notices ) ) : $i = 1; ?>
			<?php foreach( $notices as $key => $notice ) : $notice = edd_sl_get_renewal_notice( $key ); ?>
			<tr <?php if( $i % 2 == 0 ) { echo 'class="alternate"'; } ?>>
				<td><?php echo esc_html( stripslashes( $notice['subject'] ) ); ?></td>
				<td><?php echo esc_html( edd_sl_get_renewal_notice_period_label( $key ) ); ?></td>
				<td>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-license-renewal-notice&edd_sl_action=edit-renewal-notice&notice=' . $key ) ); ?>" class="edd-sl-edit-renewal-notice" data-key="<?php echo esc_attr( $key ); ?>"><?php _e( 'Edit', 'edd_sl' ); ?></a>&nbsp;|
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'edit.php?post_type=download&page=edd-license-renewal-notice&edd_action=delete_renewal_notice&notice-id=' . $key ) ) ); ?>" class="edd-delete"><?php _e( 'Delete', 'edd_sl' ); ?></a>
				</td>
			</tr>
			<?php $i++; endforeach; ?>
		<?php endif; ?>
	</table>
	<p>
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-license-renewal-notice&edd_sl_action=add-renewal-notice' ) ); ?>" class="button-secondary" id="edd_sl_add_renewal_notice"><?php _e( 'Add Renewal Notice', 'edd_sl' ); ?></a>
	</p>
	<?php
	echo ob_get_clean();
}
add_action( 'edd_sl_renewal_notices', 'edd_sl_renewal_notices_settings' );

/**
 * Renders the add / edit renewal notice screen
 *
 * @since 3.0
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function edd_sl_license_renewal_notice_edit() {

	$action = isset( $_GET['edd_sl_action'] ) ? sanitize_text_field( $_GET['edd_sl_action'] ) : 'add-renewal-notice';

	if( 'edit-renewal-notice' === $action ) {
		include EDD_SL_PLUGIN_DIR . 'includes/edit-renewal-notice.php';
	} else {
		include EDD_SL_PLUGIN_DIR . 'includes/add-renewal-notice.php';
	}

}

/**
 * Processes the creation of a new renewal notice
 *
 * @since 3.0
 * @param array $data The post data
 * @return void
 */
function edd_sl_process_add_renewal_notice( $data ) {

	if( ! is_admin() ) {
		return;
	}

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		wp_die( __( 'You do not have permission to add renewal notices', 'edd_sl' ), __( 'Error', 'edd_sl' ), array( 'response' => 401 ) );
	}

	if( ! wp_verify_nonce( $data['edd-renewal-notice-nonce'], 'edd_renewal_nonce' ) ) {
		wp_die( __( 'Nonce verification failed', 'edd_sl' ), __( 'Error', 'edd_sl' ), array( 'response' => 401 ) );
	}

	$subject = isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : __( 'Your License Key is About to Expire', 'edd_sl' );
	$period  = isset( $data['period'] )  ? sanitize_text_field( $data['period'] )  : '+1month';
	$message = isset( $data['message'] ) ? wp_kses( stripslashes( $data['message'] ), wp_kses_allowed_html( 'post' ) ) : false;

	if( empty( $message ) ) {
		$message = 'Hello {name},

Your license key for {product_name} is about to expire.

If you wish to renew your license, simply click the link below and follow the instructions.

Your license expires on: {expiration}.

Your expiring license key is: {license_key}.

Renew now: {renewal_link}.';
	}


	$notices = edd_sl_get_renewal_notices();
	$notices[] = array(
		'subject'     => $subject,
		'message'     => $message,
		'send_period' => $period
	);

	update_option( 'edd_sl_renewal_notices', $notices );

	wp_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions&section=software-licensing' ) );
	exit;

}
add_action( 'edd_add_renewal_notice', 'edd_sl_process_add_renewal_notice' );

/**
 * Processes the update of an existing renewal notice
 *
 * @since 3.0
 * @param array $data The post data
 * @return void
 */
function edd_sl_process_update_renewal_notice( $data ) {

	if( ! is_admin() ) {
		return;
	}

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		wp_die( __( 'You do not have permission to add renewal notices', 'edd_sl' ), __( 'Error', 'edd_sl' ), array( 'response' => 401 ) );
	}

	if( ! wp_verify_nonce( $data['edd-renewal-notice-nonce'], 'edd_renewal_nonce' ) ) {
		wp_die( __( 'Nonce verification failed', 'edd_sl' ), __( 'Error', 'edd_sl' ), array( 'response' => 401 ) );
	}

	if( ! isset( $data['notice-id'] ) ) {
		wp_die( __( 'No renewal notice ID was provided', 'edd_sl' ) );
	}

	$subject = isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : __( 'Your License Key is About to Expire', 'edd_sl' );
	$period  = isset( $data['period'] )  ? sanitize_text_field( $data['period'] )  : '1month';
	$message = isset( $data['message'] ) ? wp_kses( stripslashes( $data['message'] ), wp_kses_allowed_html( 'post' ) ) : false;

	if( empty( $message ) ) {
		$message = 'Hello {name},

Your license key for {product_name} is about to expire.

If you wish to renew your license, simply click the link below and follow the instructions.

Your license expires on: {expiration}.

Your expiring license key is: {license_key}.

Renew now: {renewal_link}.';
	}


	$notices = edd_sl_get_renewal_notices();
	$notices[ absint( $data['notice-id'] ) ] = array(
		'subject'     => $subject,
		'message'     => $message,
		'send_period' => $period
	);

	update_option( 'edd_sl_renewal_notices', $notices );

	wp_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions&section=software-licensing' ) );
	exit;

}
add_action( 'edd_edit_renewal_notice', 'edd_sl_process_update_renewal_notice' );

/**
 * Processes the deletion of an existing renewal notice
 *
 * @since 3.0
 * @param array $data The post data
 * @return void
 */
function edd_sl_process_delete_renewal_notice( $data ) {

	if( ! is_admin() ) {
		return;
	}

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		wp_die( __( 'You do not have permission to add renewal notices', 'edd_sl' ), __( 'Error', 'edd_sl' ), array( 'response' => 401 ) );
	}

	if( ! wp_verify_nonce( $data['_wpnonce'] ) ) {
		wp_die( __( 'Nonce verification failed', 'edd_sl' ), __( 'Error', 'edd_sl' ), array( 'response' => 401 ) );
	}

	if( empty( $data['notice-id'] ) && 0 !== (int) $data['notice-id'] ) {
		wp_die( __( 'No renewal notice ID was provided', 'edd_sl' ), __( 'Error', 'edd_sl' ), array( 'response' => 409 ) );
	}

	$notices = edd_sl_get_renewal_notices();
	unset( $notices[ absint( $data['notice-id'] ) ] );

	update_option( 'edd_sl_renewal_notices', $notices );

	wp_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions&section=software-licensing' ) );
	exit;

}
add_action( 'edd_delete_renewal_notice', 'edd_sl_process_delete_renewal_notice' );
