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
if ( class_exists( 'Wcd_Woo_Stripe_Payment_Gateway_Main' ) ) {
	return;
}

/**
 * Define class and module for stripe.
 */
class Wcd_Woo_Stripe_Payment_Gateway_Main {

	/**
	 * Generate the request for the payment.
	 *
	 * @name wcd_woo_sub_call_on_payment_renewal.
	 * @since  1.0.0.
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  int $order_id order_id.
	 * @param  int $parent_order_id parent_order_id.
	 * @return array()
	 */
	public function wcd_woo_sub_call_on_payment_renewal( $order_id, $parent_order_id ) {

		$order = wc_get_order( $order_id );
		$wcd_woo_parent_order = wc_get_order( $parent_order_id );

		$wcd_is_successful = false;

		try {
			// Return if order total is zero.
			if ( 0 == $order->get_total() ) {
				$order->payment_complete();
				return;
			}

			$gateway = $this->wcd_woo_sub_get_payment_gateways();// get the payment gateways.

			if ( ! $gateway ) {
				$order_note = __( 'Stripe payment gateway not set/activated.', 'wcd-subscriptions' );
				$order->update_status( 'failed', $order_note );
				return;
			}
			$source = $gateway->prepare_order_source( $wcd_woo_parent_order );

			$response = WC_Stripe_API::request( $this->wcd_woo_sub_do_payent_call_request( $order, $source ) );
			// show the data in log file.
			WC_Stripe_Logger::log( 'WCD result: ' . wc_print_r( $response, true ) );
			// Log here complete response.
			if ( is_wp_error( $response ) ) {
				// show the data in log file.
				WC_Stripe_Logger::log( 'WCD error: ' . wc_print_r( $response, true ) );
				// @todo handle the error part here/failure of order.

				$error_message = sprintf( __( 'Something Went Wrong. Please see the log file for more info.', 'wcd-subscriptions' ) );

			} else {
				if ( ! empty( $response->error ) ) {
					WC_Stripe_Logger::log( 'WCD error: ' . wc_print_r( $response, true ) );
					$wcd_is_successful = false;
					$order_note = __( 'Stripe Transaction Failed', 'wcd-subscriptions' );
					$order->update_status( 'failed', $order_note );
					do_action( 'wcd_woo_sub_renewal_payment_failed', $order_id );

				} else {
					// show the data in log file.
					WC_Stripe_Logger::log( 'WCD succes: ' . wc_print_r( $response, true ) );

					update_post_meta( $order_id, '_wcd_woo_sub_payment_transaction_id', $response->id );
					/* translators: %s: transaction id */
					$order_note = sprintf( __( 'Stripe Renewal Transaction Successful (%s)', 'wcd-subscriptions' ), $response->id );
					$order->add_order_note( $order_note );
					$order->payment_complete( $response->id );
					do_action( 'wcd_woo_sub_renewal_payment_success', $order_id );

					$wcd_is_successful = true;
				}
			}

			// Returns boolean.
			return $wcd_is_successful;

		} catch ( Exception $e ) {
			WC_Stripe_Logger::log( 'WCD Failed Result: ' );
			// @todo transaction failure to handle here.
			$order_note = __( 'WCD Stripe Transaction Gets Failed', 'wcd-subscriptions' );
			$order->update_status( 'failed', $order_note );
			return false;
		}
	}

	/**
	 * Generate the request for the payment.
	 *
	 * @name wcd_woo_sub_do_payent_call_request.
	 * @since  1.0.00
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  object $order order.
	 * @param  object $source source.
	 *
	 * @return array()
	 */
	public function wcd_woo_sub_do_payent_call_request( $order, $source ) {
		$order_id = $order->get_id();
		$charge_amount = $order->get_total();

		$gateway                  = $this->wcd_woo_sub_get_payment_gateways();
		$post_data                = array();
		$post_data['currency']    = strtolower( $this->wcd_woo_sub_get_currency_of_order( $order ) );
		$post_data['amount']      = WC_Stripe_Helper::get_stripe_amount( $charge_amount, $post_data['currency'] );
		/* translators: 1$: site name,2$: order number */
		$post_data['description'] = sprintf( __( '%1$s - Order %2$s - Renewal Order.', 'wcd-subscriptions' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $order->get_order_number() );
		$post_data['capture']     = 'true';
		$billing_first_name       = $order->get_billing_first_name();
		$billing_last_name        = $order->get_billing_last_name();
		$billing_email            = $order->get_billing_email( $order, 'billing_email' );

		if ( ! empty( $billing_email ) && apply_filters( 'wc_stripe_send_stripe_receipt', false ) ) {
			$post_data['receipt_email'] = $billing_email;
		}
		$metadata              = array(
			'customer_name'  => sanitize_text_field( $billing_first_name ) . ' ' . sanitize_text_field( $billing_last_name ),
			'customer_email' => sanitize_email( $billing_email ),
			'order_id'                                           => $order_id,
		);
		$post_data['expand[]'] = 'balance_transaction';
		$post_data['metadata'] = apply_filters( 'wc_stripe_payment_metadata', $metadata, $order, $source );

		if ( $source->customer ) {
			$post_data['customer']  = ! empty( $source->customer ) ? $source->customer : '';
		}

		if ( $source->source ) {
			$post_data['source']  = ! empty( $source->source ) ? $source->source : '';
		}
		return apply_filters( 'wc_stripe_generate_payment_request', $post_data, $order, $source );
	}

	/**
	 * Get payment gateway.
	 *
	 * @since  1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @return WC_Payment_Gateway.
	 */
	public function wcd_woo_sub_get_payment_gateways() {
		global $woocommerce;
		$gateways = $woocommerce->payment_gateways->payment_gateways();
		if ( isset( $gateways['stripe'] ) && ! empty( $gateways['stripe'] ) ) {
			return $gateways['stripe'];
		}
		return false;
	}

	/**
	 * Get order currency.
	 *
	 * @name wcd_woo_sub_get_currency_of_order.
	 * @since  1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  object $order order.
	 *
	 * @return mixed|string
	 */
	public function wcd_woo_sub_get_currency_of_order( $order ) {

		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			return $order ? $order->get_currency() : get_woocommerce_currency();
		} else {
			return $order ? $order->get_order_currency() : get_woocommerce_currency();

		}
	}
}
