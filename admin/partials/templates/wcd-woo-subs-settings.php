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
	<h2><?php esc_html_e( 'General Settings', 'wcd-subscriptions' ); ?></h2>
</div>
<div class="wcd-settings-container">
	<div class="wcd-general-settings">
  <form action="" method="post">
  <?php

	if ( isset( $_POST['wcd_woo_subs_save_gensttings'] ) && check_admin_referer( 'wcd-woo-settings' ) ) {
		$settigns = wp_unslash( $_POST );

		unset( $settigns['wcd_woo_subs_save_gensttings'] );

		if ( ! isset( $settigns['wcd_woo_subs_enable_plugin'] ) ) {

			$settigns['wcd_woo_subs_enable_plugin'] = 'off';
		}

		if ( ! isset( $settigns['wcd_woo_subs_enable_log'] ) ) {

			$settigns['wcd_woo_subs_enable_log'] = 'off';
		}

		if ( ! isset( $settigns['wcd_woo_subs_track_quantity_of_products'] ) ) {

			$settigns['wcd_woo_subs_track_quantity_of_products'] = 'off';
		}

		if ( ! isset( $settigns['wcd_woo_subs_customer_can_cancel_subscription'] ) ) {

			$settigns['wcd_woo_subs_customer_can_cancel_subscription'] = 'off';
		}

		if ( ! isset( $settigns['wcd_woo_subs_delete_subscription_if_cancelled'] ) ) {

			$settigns['wcd_woo_subs_delete_subscription_if_cancelled'] = 'off';
		}

		foreach ( $settigns as $key => $value ) {

			if ( is_array( $value ) ) {

				$sanitized_value = array();
				foreach ( $value as $k => $v ) {
					$sanitized_value[ $k ] = sanitize_text_field( $v );
				}

				$value = $sanitized_value;
			}
			if ( 'on' == $value ) {
				$value = 'yes';
			} else if ( 'off' == $value ) {
				$value = 'no';
			}

			update_option( $key, $value );
		}

		$message = esc_html__( 'Settings saved', 'wcd-subscriptions' );
		$admin_class = new Wcd_Subscriptions_Admin( ONBOARD_PLUGIN_NAME, WCD_SUBSCRIPTIONS_VERSION );
		$admin_class->wcd_woo_notice( $message, 'success' );
	}
	$track_quantity_of_products = get_option( 'wcd_woo_subs_track_quantity_of_products', '' );
	$customer_can_cancel_subscription = get_option( 'wcd_woo_subs_customer_can_cancel_subscription', '' );
	$delete_subscription_if_cancelled = get_option( 'wcd_woo_subs_delete_subscription_if_cancelled', '' );
	$enable_plugin = get_option( 'wcd_woo_subs_enable_plugin', '' );
	$enable_log = get_option( 'wcd_woo_subs_enable_log', '' );
	?>
			<div class="wcd-general-settings-row">
				<label class="switch">Enable/Disable Plugin</label>
		<input type="checkbox" class="slider-round" name="wcd_woo_subs_enable_plugin" 
		<?php
		if ( 'yes' == $enable_plugin ) {
			?>
			 value="yes" <?php } ?>
			<?php
			if ( 'yes' == $enable_plugin ) {
				?>
			checked<?php } ?>><p class="description_tool_tip">Enable this to make plugin work</p>
			</div>
			<div class="wcd-general-settings-row">
		<label class="switch">Enable/Disable Log</label>
		<input type="checkbox" class="slider-round" name="wcd_woo_subs_enable_log" 
		<?php
		if ( 'yes' == $enable_plugin ) {
			?>
			 value="yes" <?php } ?>
			<?php
			if ( 'yes' == $enable_plugin ) {
				?>
			checked<?php } ?>><p class="description_tool_tip">Enable this to record log of created and renewal subscription</p>
			</div>
			<div class="wcd-general-settings-row">
		<label class="switch">Delete subscrption if main order cancelled</label>
		<input type="checkbox" class="slider-round" name="wcd_woo_subs_delete_subscription_if_cancelled" 
		<?php
		if ( 'yes' == $delete_subscription_if_cancelled ) {
			?>
			 value="yes" <?php } ?>
			<?php
			if ( 'yes' == $delete_subscription_if_cancelled ) {
				?>
			checked<?php } ?>><p class="description_tool_tip">Enable this to delete subscriptions if main order cancelled</p>
	  </div>
	  <hr>
	  <div class="wcd-settings-header wcd-common-header">
		  <h2><?php esc_html_e( 'Advance Settings', 'wcd-subscriptions' ); ?></h2>
	  </div>
	  <div class="wcd-general-settings-row">
				<label class="switch">Track quantity of products</label>
		<input type="checkbox" class="slider-round" name="wcd_woo_subs_track_quantity_of_products" 
		<?php
		if ( 'yes' == $track_quantity_of_products ) {
			?>
			 value="yes" <?php } ?>
			<?php
			if ( 'yes' == $track_quantity_of_products ) {
				?>
			checked<?php } ?>><p class="description_tool_tip">Enable this to reduce quantity of products while every recurrence</p>
			</div>
			<div class="wcd-general-settings-row">
		<label class="switch">Customer can cancel subscription</label>
		<input type="checkbox" class="slider-round" name="wcd_woo_subs_customer_can_cancel_subscription" 
		<?php
		if ( 'yes' == $customer_can_cancel_subscription ) {
			?>
			 value="yes" <?php } ?>
			<?php
			if ( 'yes' == $customer_can_cancel_subscription ) {
				?>
			checked<?php } ?>><p class="description_tool_tip">Enable this to allow customers to cancel their subscriptons</p>
			</div>
	  <hr>
			<p class="submit">
				<input type="submit" class="wcd-button" name="wcd_woo_subs_save_gensttings" value="<?php esc_html_e( 'Save settings', 'wcd-subscriptions' ); ?>">
			</p>
		<?php wp_nonce_field( 'wcd-woo-settings' ); ?>
	</form>
  </div>
</div>
