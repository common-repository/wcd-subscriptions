<?php
/**
 * Provide a admin settings page
 *
 * This file is used to markup the admin settings of the plugin.
 *
 * @since 1.0.0
 * @package  wcd-subscriptions
 * @subpackage wcd-subscriptions/admin/partials/templates
 */

?>
<div class="wcd-settings-header wcd-common-header">
	<h2><?php esc_html_e( 'Text modifications of buttons labels', 'wcd-subscriptions' ); ?></h2>
</div>
<div class="wcd-settings-container">
<div class="wcd-general-settings">
  <form action="" method="post">
  <?php

	if ( isset( $_POST['wcd_woo_subs_save_text_modification_button'] ) && check_admin_referer( 'wcd-woo-settings' ) ) {

		$settigns = wp_unslash( $_POST );

		unset( $settigns['wcd_woo_subs_save_text_modification_button'] );

		if ( ! isset( $settigns['wcd_woo_subs_changed_add_to_cart_button'] ) ) {

			$settigns['wcd_woo_subs_changed_add_to_cart_button'] = 'Add to cart';
		}

		if ( ! isset( $settigns['wcd_woo_subs_changed_place_order_button'] ) ) {

			$settigns['wcd_woo_subs_changed_place_order_button'] = 'Place order';
		}

		foreach ( $settigns as $key => $value ) {

			if ( is_array( $value ) ) {

				$sanitized_value = array();
				foreach ( $value as $k => $v ) {
					$sanitized_value[ $k ] = sanitize_text_field( $v );
				}

				$value = $sanitized_value;
			}

			update_option( $key, $value );
		}

		$message = esc_html__( 'Texts saved', 'wcd-subscriptions' );
		$admin_class = new Wcd_Subscriptions_Admin( ONBOARD_PLUGIN_NAME, WCD_SUBSCRIPTIONS_VERSION );
		$admin_class->wcd_woo_notice( $message, 'success' );
	}
	$place_order_text = get_option( 'wcd_woo_subs_changed_place_order_button', 'Signup' );
	$add_to_cart_text = get_option( 'wcd_woo_subs_changed_add_to_cart_button', 'Subscribe' );
	?>
	<div class="wcd-general-settings-row">
		<label for="wcd_woo_subs_changed_add_to_cart_button">Change "Add to cart" Button label to:</label>
		<input type="text" name="wcd_woo_subs_changed_add_to_cart_button" value="<?php echo esc_attr( $add_to_cart_text ); ?>"><p class="description_tool_tip_text_modificatons">Enter any text to replace the text of 'Add to cart' button</p>
	</div>
	<div class="wcd-general-settings-row">
		<label>Change "Place Order" Button label to:</label>
		<input type="text" name="wcd_woo_subs_changed_place_order_button" value="<?php echo esc_attr( $place_order_text ); ?>"><p class="description_tool_tip_text_modificatons">Enter any text to replace the text of 'Place order' button</p>
	</div>
	<hr>
	<p class="submit">
		<input type="submit" class="wcd-button" name="wcd_woo_subs_save_text_modification_button" value="<?php esc_html_e( 'Save texts', 'wcd-subscriptions' ); ?>">
	</p>
  <?php wp_nonce_field( 'wcd-woo-settings' ); ?>
  </form>
</div>
</div>
