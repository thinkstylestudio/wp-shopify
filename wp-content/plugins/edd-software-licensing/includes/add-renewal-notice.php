<?php
/**
 * Edit Renewal Notice
 *
 * @package     EDD Software Licensing
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>
<h2><?php _e( 'Add Renewal Notice', 'edd_sl' ); ?> - <a href="<?php echo admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions&section=software-licensing' ); ?>" class="add-new-h2"><?php _e( 'Go Back', 'edd_sl' ); ?></a></h2>
<form id="edd-add-renewal-notice" action="" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" valign="top">
					<label for="edd-notice-subject"><?php _e( 'Email Subject', 'edd_sl' ); ?></label>
				</th>
				<td>
					<input name="subject" id="edd-notice-subject" class="edd-notice-subject regular-text" type="text" value="" />
					<p class="description"><?php _e( 'The subject line of the renewal notice email', 'edd_sl' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="edd-notice-period"><?php _e( 'Email Period', 'edd_sl' ); ?></label>
				</th>
				<td>
					<select name="period" id="edd-notice-period">
						<?php foreach( edd_sl_get_renewal_notice_periods() as $period => $label ) : ?>
							<option value="<?php echo esc_attr( $period ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php _e( 'When should this email be sent?', 'edd_sl' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top">
					<label for="edd-notice-message"><?php _e( 'Email Message', 'edd_sl' ); ?></label>
				</th>
				<td>
					<?php wp_editor( '', 'message', array( 'textarea_name' => 'message' ) ); ?>
					<p class="description"><?php _e( 'The email message to be sent with the renewal notice. The following template tags can be used in the message:', 'edd_sl' ); ?></p>
					<ul>
						<li>{name} <?php _e( 'The customer\'s name', 'edd_sl' ); ?></li>
						<li>{license_key} <?php _e( 'The license key that needs renewed', 'edd_sl' ); ?></li>
						<li>{product_name} <?php _e( 'The name of the product the license key belongs to', 'edd_sl' ); ?></li>
						<li>{expiration} <?php _e( 'The expiration date for the license key', 'edd_sl' ); ?></li>
						<li>{renewal_link} <?php _e( 'Link to the renewal checkout page', 'edd_sl' ); ?></li>
						<li>{renewal_url} <?php _e( 'Raw URL of the renewal checkout page', 'edd_sl' ); ?></li>
						<?php $discount = edd_get_option( 'edd_sl_renewal_discount', false ); ?>
						<?php if ( ! empty( $discount ) ) : ?>
							<li>{renewal_discount} <?php _e( 'The renewal discount, including the `%` symbol.', 'edd_sl' ); ?></li>
						<?php endif; ?>
					</ul>
				</td>
			</tr>

		</tbody>
	</table>
	<p class="submit">
		<input type="hidden" name="edd-action" value="add_renewal_notice"/>
		<input type="hidden" name="edd-renewal-notice-nonce" value="<?php echo wp_create_nonce( 'edd_renewal_nonce' ); ?>"/>
		<input type="submit" value="<?php _e( 'Add Renewal Notice', 'edd_sl' ); ?>" class="button-primary"/>
	</p>
</form>
