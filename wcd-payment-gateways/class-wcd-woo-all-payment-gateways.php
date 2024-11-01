<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/includes
 * @author     WebCuddle <support@webcuddle.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Wcd_Woo_All_Payment_Gateways' ) ) {
	/**
	 * The public-facing functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the public-facing stylesheet and JavaScript.
	 *
	 * @package    Wcd_Subscriptions
	 * @subpackage Wcd_Subscriptions/public
	 * @author     WebCuddle <support@webcuddle.com>
	 */
	class Wcd_Woo_All_Payment_Gateways {
		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wcd_woo_sub_product_on_cancel', array( $this, 'wcd_woo_sub_product_on_cancel_callback' ), 10, 2 );
			add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'wcd_woo_sub_add_order_status_on_payment_done' ), 10, 2 );
			add_filter( 'wc_stripe_force_save_source', array( $this, 'wcd_woo_sub_force_customer_to_stripe' ), 10, 2 );
			include plugin_dir_path( dirname( __FILE__ ) ) . 'wcd-payment-gateways/wcd-woo-stripe-payment-gateways/class-wcd-woo-stripe-payment-gateway-main.php';
		}

		/**
		 * This function is used to attache customer for future payment.
		 *
		 * @name wcd_woo_sub_force_customer_to_stripe.
		 * @param bool   $force_save_source force_save_source.
		 * @param object $customer customer.
		 * @since    1.0.1
		 */
		public function wcd_woo_sub_force_customer_to_stripe( $force_save_source, $customer = false ) {

			if ( ! $customer ) {
				return;
			}
			if ( ! $force_save_source && wcd_sub_plugin_is_enable() ) {
				$force_save_source = true;
			}
			return $force_save_source;
		}

		/**
		 * This function is add subscription order status.
		 *
		 * @name wcd_woo_sub_add_order_status_on_payment_done.
		 * @param array  $order_status order_status.
		 * @param object $order order.
		 * @since    1.0.2
		 * @credit Inspired by code from the "wpswings" plugin.
		 */
		public function wcd_woo_sub_add_order_status_on_payment_done( $order_status, $order ) {
			if ( $order && is_object( $order ) ) {
				$order_id = $order->get_id();
				$payment_method = get_post_meta( $order_id, 'wcd_woo_sub_payment_method', true );
				$wcd_renewal_order = get_post_meta( $order_id, 'wcd_woo_sub_is_renewal', true );
				if ( 'stripe' == $payment_method && 'yes' == $wcd_renewal_order ) {
					$order_status[] = 'renewal_woo_wcd';

				}
			}
			return apply_filters( 'wcd_woo_sub_is_added_for_order_status_on_payment_complete', $order_status, $order );

		}


		/**
		 * This function is used to cancel subscriptions status.
		 *
		 * @name wcd_woo_sub_product_on_cancel_callback.
		 * @param int    $wcd_woo_sub_product_id wcd_subscription_id.
		 * @param string $wcd_status status.
		 * @since    1.0.1
		 * @credit Inspired by code from the "wpswings" plugin.
		 */
		public function wcd_woo_sub_product_on_cancel_callback( $wcd_woo_sub_product_id, $wcd_status ) {

			$wcd_woo_payment_method = get_post_meta( $wcd_woo_sub_product_id, 'wcd_woo_sub_payment_method', true );
			if ( 'stripe' == $wcd_woo_payment_method || ( 'cod' == $wcd_woo_payment_method ) || ( 'bacs' == $wcd_woo_payment_method ) || ( 'cheque' == $wcd_woo_payment_method ) ) {
				if ( 'Cancel' == $wcd_status ) {
					if ( isset( $wcd_woo_sub_product_id ) && ! empty( $wcd_woo_sub_product_id ) ) {
						$mailer = WC()->mailer()->get_emails();
						// Send the "cancel" notification.
						if ( isset( $mailer['wcd_subscription_cancel'] ) ) {
							 $mailer['wcd_subscription_cancel']->trigger( $wcd_woo_sub_product_id );
						}
					}
					update_post_meta( $wcd_woo_sub_product_id, 'wcd_subscription_status', 'cancelled' );
				}
			}
		}

	}

}
return new Wcd_Woo_All_Payment_Gateways();
