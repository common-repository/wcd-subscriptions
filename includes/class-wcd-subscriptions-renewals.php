<?php
/**
 * Fired during plugin activation
 *
 * @link       https://https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/includes
 * @author     WebCuddle <support@webcuddle.com>
 */
class Wcd_Subscriptions_Renewals {

	/**
	 * Instance.
	 *
	 * @var float
	 */
	protected static $instance;

	/**
	 * Instance
	 *
	 * Initialize plugin and registers actions and filters to be used.
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 *
	 * Initialize plugin and registers actions and filters to be used.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( wcd_sub_plugin_is_enable() ) {
			add_action( 'renew_subscription', array( $this, 'wcd_renew_subscription' ) );
			add_action( 'do_subscriptions_expired', array( $this, 'wcd_do_subscriptions_expired' ) );
		}

	}

	/**
	 * This function is to do renewable thing.
	 *
	 * @name wcd_renew_subscription
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_renew_subscription() {

		$current_time = current_time( 'timestamp' );

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wcd_subscriptions',
			'post_status'   => 'wc-wcd_recurring',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'wcd_subscription_status',
					'value' => 'active',
				),
				array(
					'relation' => 'AND',
					array(
						'key'   => 'wcd_parent_order',
						'compare' => 'EXISTS',
					),
					array(
						'key'   => 'wcd_next_payment_date',
						'value' => $current_time,
						'compare' => '<',
					),
				),
			),
		);
		$wcd_subs = get_posts( $args );

		if ( ! empty( $wcd_subs ) ) {
			wcd_create_log( 'WCD Renewal Subscriptions: ' . wc_print_r( $wcd_subs, true ) );
		}
		if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {

			foreach ( $wcd_subs as $key => $value ) {
				$subs_id = $value->ID;
				if ( wcd_is_this_subscription_valid( $subs_id ) ) {

					$subscription = get_post( $subs_id );
					$parent_id  = $subscription->wcd_parent_order;
					$subp_id = get_post_meta( $value->ID, 'product_id', true );
					$check_variable = get_post_meta( $subp_id, 'wcd_variable_product', true );
					if ( 'yes' === $check_variable ) {
						continue;
					}
					$parent_order = wc_get_order( $parent_id );
					$billing_details = $parent_order->get_address( 'billing' );
					$shipping_details = $parent_order->get_address( 'shipping' );
					$parent_order_currency = $parent_order->get_currency();
					$customer_id = $subscription->user_id;
					$product_id = $subscription->product_id;
					$product_qty = $subscription->quantity;
					$payment_method = $subscription->_payment_method;
					$payment_method_title = $subscription->_payment_method_title;

					$wcd_old_payment_method = get_post_meta( $parent_id, '_payment_method', true );
					$args1 = array(
						'status'      => 'wc-wcd_recurring',
						'customer_id' => $customer_id,
					);
					$new_order = wc_create_order( $args1 );
					$new_order->set_currency( $parent_order_currency );
					$new_order->set_address( $billing_details, 'billing' );
					$new_order->set_address( $shipping_details, 'shipping' );

					$line_subtotal = get_post_meta( $subs_id, 'recurring_total', true );
					$line_total = get_post_meta( $subs_id, 'recurring_total', true );

					$product_data = wc_get_product( $product_id );

					$include_tax = get_option( 'woocommerce_prices_include_tax' );
					// if inculsie tax is applicable.
					if ( 'yes' == $include_tax ) {
						$substotal_taxes = WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $line_subtotal, WC_Tax::get_rates() ) );
						$total_taxes = WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $line_total, WC_Tax::get_rates() ) );
						$wcd_pro_args = array(
							'variation' => array(),
							'totals'    => array(
								'subtotal'     => $line_subtotal - $substotal_taxes,
								'subtotal_tax' => $substotal_taxes,
								'total'        => $line_total - $total_taxes,
								'tax'          => $total_taxes,
								'tax_data'     => array(
									'subtotal' => array( $substotal_taxes ),
									'total'    => array( $total_taxes ),
								),
							),
						);

					} else {
						// if tax is disable or exculsive tax is applicable.
						$substotal_taxes = WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $line_subtotal, WC_Tax::get_rates() ) );
						$total_taxes = WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $line_total, WC_Tax::get_rates() ) );

						$wcd_pro_args = array(
							'variation' => array(),
							'totals'    => array(
								'subtotal'     => $line_subtotal,
								'subtotal_tax' => $substotal_taxes,
								'total'        => $line_total,
								'tax'          => $total_taxes,
								'tax_data'     => array(
									'subtotal' => array( $substotal_taxes ),
									'total'    => array( $total_taxes ),
								),
							),
						);
					}

					$item_id = $new_order->add_product(
						$product_data,
						$product_qty,
						$wcd_pro_args
					);

					$new_order_id = $new_order->get_id();
					if ( ! empty( $wcd_subs ) ) {
						wcd_create_log( 'WCD Renewal Order ID: ' . wc_print_r( $new_order_id, true ) );
					}
					update_post_meta( $new_order_id, '_payment_method', $payment_method );
					update_post_meta( $new_order_id, '_payment_method_title', $payment_method_title );
					update_post_meta( $new_order_id, 'has_renewal_order', 'yes' );
					update_post_meta( $new_order_id, 'linked_subscription_id', $subs_id );
					update_post_meta( $new_order_id, 'parent_id', $parent_id );
					update_post_meta( $subs_id, 'renewal_order_id', $new_order_id );

					// Billing phone number added.
					$billing_address = get_post_meta( $parent_id, '_billing_address_index', true );
					update_post_meta( $new_order_id, '_billing_address_index', $billing_address );

					$total_renewal_order = get_post_meta( $subs_id, 'total_renewal_order_of_subscription', true );
					if ( empty( $total_renewal_order ) ) {
						$total_renewal_order = 1;
						update_post_meta( $subs_id, 'total_renewal_order_of_subscription', $total_renewal_order );
					} else {
						$total_renewal_order = (int) $total_renewal_order + 1;
						update_post_meta( $subs_id, 'total_renewal_order_of_subscription', $total_renewal_order );
					}
					$renewal_order_data = get_post_meta( $subs_id, 'renewal_order_data', true );
					if ( empty( $renewal_order_data ) ) {
						$renewal_order_data = array( $new_order_id );
						update_post_meta( $subs_id, 'renewal_order_data', $renewal_order_data );
					} else {
						$renewal_order_data[] = $new_order_id;
						update_post_meta( $subs_id, 'renewal_order_data', $renewal_order_data );
					}
					update_post_meta( $subs_id, 'last_renewal_id_of_subscription', $new_order_id );
					do_action( 'wcd_renewal_order_creation', $new_order, $subs_id );
					if ( $subscription->line_subtotal_tax || $subscription->line_tax ) {
						$new_order->update_taxes();
						$new_order->calculate_totals();
					} else {
						$new_order->calculate_totals( false );
					}
					$new_order->save();

					/*if trial period enable*/
					if ( '' == $wcd_old_payment_method ) {
						$parent_id = $subs_id;
					}

					$wcd_next_payment_date = 0;

					$subs_number = get_post_meta( $subs_id, 'wcd_woo_sub_product_total_period_number', true );
					$subs_interval = get_post_meta( $subs_id, 'wcd_woo_sub_product_total_period_type', true );
					if ( isset( $subs_number ) && ! empty( $subs_number ) ) {
						$wcd_next_payment_date = wcd_calculate_time_of_subs_entity( $current_time, $subs_number, $subs_interval );
					}

					update_post_meta( $subs_id, 'wcd_next_payment_date', $wcd_next_payment_date );

					if ( 'stripe' == $payment_method ) {
						if ( class_exists( 'Wcd_Woo_Stripe_Payment_Gateway_Main' ) ) {
							$stripe = new Wcd_Woo_Stripe_Payment_Gateway_Main();
							$result = $stripe->wcd_woo_sub_call_on_payment_renewal( $new_order_id, $parent_id );
							update_post_meta( $new_order_id, '_stripe_charge_captured', 'yes' );
							do_action( 'wcd_cancel_failed_susbcription', $result, $new_order_id, $subs_id );
							$order = wc_get_order( $new_order_id );
							if ( isset( $order ) && is_object( $order ) ) {
								$mailer = WC()->mailer()->get_emails();
								if ( isset( $mailer['WC_Email_New_Order'] ) ) {
									$mailer['WC_Email_New_Order']->trigger( $new_order_id );
								}
							}
						}
					}

					do_action( 'wcd_other_payment_gateway_renewal', $new_order, $subs_id, $payment_method );
				}
			}
		}

	}

	/**
	 * This function is used to  expired susbcription.
	 *
	 * @name wcd_do_subscriptions_expired
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_do_subscriptions_expired() {
		$current_time = current_time( 'timestamp' );

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wcd_subscriptions',
			'post_status'   => 'wc-wcd_recurring',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'wcd_subscription_status',
					'value' => array( 'active', 'pending' ),
				),
				array(
					'relation' => 'AND',
					array(
						'key'   => 'wcd_parent_order',
						'compare' => 'EXISTS',
					),
					array(
						'relation' => 'AND',
						array(

							'key'   => 'wcd_susbcription_end',
							'value' => $current_time,
							'compare' => '<',
						),
						array(
							'key'   => 'wcd_susbcription_end',
							'value' => 0,
							'compare' => '!=',
						),
					),
				),
			),
		);
		$wcd_subs = get_posts( $args );
		if ( ! empty( $wcd_subs ) ) {
			wcd_create_log( 'WCD Expired Subscriptions: ' . wc_print_r( $wcd_subs, true ) );
		}
		if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {
			foreach ( $wcd_subs as $key => $value ) {
				$subs_id = $value->ID;

				if ( wcd_is_this_subscription_valid( $subs_id ) ) {
					$mailer = WC()->mailer()->get_emails();
					// Send the "expired" notification.
					if ( isset( $mailer['wcd_subscription_expire'] ) ) {
						$mailer['wcd_subscription_expire']->trigger( $subs_id );
					}
					update_post_meta( $subs_id, 'wcd_subscription_status', 'expired' );
					update_post_meta( $subs_id, 'wcd_next_payment_date', '' );
				}
			}
		}
	}

}

$object_of_this_class = new Wcd_Subscriptions_Renewals();
$object_of_this_class::get_instance();
