<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/admin/partials
 */

?>

<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
 <a class="open-button" id="wcd-modal-open-id" popup-open="popup-1" href="javascript:void(0)">click</a>
	<div class="popup wcd_subscription_product_class" id="wcd_main_popup_id" popup-name="popup-1">

	<!---First From HTML Start Here----->
	<div class="wcd-content-popup" id='wcd_create_prod_id'>
	  <div class="wcd_container_class" id="myModal" class="modal">
	  <label><h1><i>Create the Subscription Product Here.</i></h1></label>
	  <div class="wcd-subscription-modal-content">
	<input type="hidden" name="pippin_sample_nonce" id="wcd_nonce_subs" value="<?php echo esc_html( wp_create_nonce( 'pippin-sample-nonce' ) ); ?>"/>
	<label for="email"><b>Product Name</b></label>
	<input type="text" placeholder="Product Name" name="wcd_product_name" id="wcd_product_name" value='WCD Subscription' required>

	<label for="email"><b>Product Description</b></label>
	<input type="text" placeholder="Product Description" name="wcd_product_description" id="wcd_product_description" value='This is the Subscription Product' required>

	<label for="psw"><b>Price</b></label>
	<input type="number" placeholder="Price" name="wcd_product_price" id="wcd_product_price" value='10' required>

	<label for="psw-repeat"><b>Interval</b></label>
	<input type="number" placeholder="Interval" name="wcd_subscription_interval" id="wcd_subscription_interval"  value='2' required>

	<label for="psw-repeat"><b>Interval Period</b></label>
	<select id="wcd_woo_demo_sub_product_trial_number" name="wcd_woo_demo_sub_product_trial_number" class="wcd_woo_sub_product_trial_number_class" >
	<?php foreach ( wcd_sub_product_timespan() as $value => $label ) { ?>
	<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
	<?php } ?>
	</select>
	<hr>
	<!-- <p>By creating an account you agree to our <a href="#">Terms & Privacy</a>.</p> -->
	<button type="button" class="wcd-registerbtn">Create Product</button>
  </div>
  </div>
 </div>
 <!---First From HTML End Here----->
 <?php $all_plugins = get_plugins(); ?>

 <!---Second From HTML Start Here----->
 <div class="wcd-content-popup" id='wcd_create_payment_id'>
 <div class="wcd_container_class" id="myModal" class="modal">
	  <div class="wcd-subscription-modal-content">

	  <label><h3><i>Please install/active the below Online Payment Gateway,if already install and activate click on finish</i></h3></label>
	  <div class="wcd-content-popup">
		<div>
		<?php if ( array_key_exists( 'woocommerce-gateway-stripe/woocommerce-gateway-stripe.php', $all_plugins ) ) { ?>
			<i class="fas fa-check-square fa-1x"></i>
		<?php } ?>
		<label>WooCommerce Stripe Gateway</label>
		<button type="button1" class="wcd-payment-gate-install" id='woocommerce-gateway-stripe' value="woocommerce-gateway-stripe"><i class="fa fa-download"></i> Install/Activate</button><br>
		</div><br><br><br>
		<div>
		<?php if ( array_key_exists( 'woocommerce-paypal-payments/woocommerce-paypal-payments.php', $all_plugins ) ) { ?>
			<i class="fas fa-check-square fa-1x"></i>
		<?php } ?>	
		<label>WooCommerce PayPal Gateway</label>
		<button type="button1" class="wcd-payment-gate-install" id='woocommerce-paypal-payments' value="woocommerce-paypal-payments"><i class="fa fa-download"></i> Install/Activate</button><br>
	</div>
	</div>
	<button type="button" class="wcd-registerbtn" id='wcd_finish_setup' >Finish Setup</button>
  </div>
  </div>
 </div>
 <!---Secnd Form End Here-->

<!-- <div class="wcd-closs-modal">
<a class="close-button" popup-close="popup-1" href="javascript:void(0)">x</a>
</div> -->
</div> <!--Parent Wrapper Of the Bith Form-->
