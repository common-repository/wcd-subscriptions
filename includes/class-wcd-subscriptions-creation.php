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
class Wcd_Subscriptions_Creation {

	/**
	 * Instance.
	 *
	 * @var float
	 */
	protected static $instance;

	/**
	 * The subscription (post) ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Constructor
	 *
	 * Initialize plugin and registers actions and filters to be used.
	 *
	 * @param  array $subscription_id subscription_id.
	 * @param  array $args args.
	 * @param  array $order_id order_id.
	 * @since 1.0.0
	 */
	public function __construct( $subscription_id = 0, $args, $order_id ) {

		if ( wcd_sub_plugin_is_enable() ) {

			if ( $subscription_id ) {
				$this->id = $subscription_id;
			}
			if ( '' === $subscription_id && ! empty( $args ) ) {
				$this->wcd_create_subscription( $args, $order_id );
			}
		}

	}

	/**
	 * This function is to create subscription.
	 *
	 * @name wcd_create_subscription
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  array $args args.
	 * @param  array $order_id  order_id .
	 */
	public function wcd_create_subscription( $args, $order_id ) {
		$order = wc_get_order( $order_id );
		$subs_id = '';
		$subs_id = wp_insert_post(
			array(
				'post_status' => 'wc-wcd_recurring',
				'post_type'   => 'wcd_subscriptions',
				'post_author' => 1,
				'post_parent' => $order_id,
			),
			// true.
		);
		update_post_meta( $subs_id, '_customer_user', $order->get_customer_id() );
		update_post_meta( $subs_id, '_order_key', wc_generate_order_key() );
		$subs_order = new WC_Order( $subs_id );

		$billing_details = $order->get_address( 'billing' );
		$shipping_details = $order->get_address( 'shipping' );

		$subs_order->set_address( $billing_details, 'billing' );
		$subs_order->set_address( $shipping_details, 'shipping' );

		if ( isset( $args['payment_method'] ) && $args['payment_method'] ) {
			$enabled_gateways = WC()->payment_gateways->get_available_payment_gateways();

			if ( isset( $enabled_gateways[ $args['payment_method'] ] ) ) {
				$wcd_payment_method = $enabled_gateways[ $args['payment_method'] ];
				$wcd_payment_method->validate_fields();
				$wcd_args['_payment_method']       = $wcd_payment_method->id;
				$wcd_args['_payment_method_title'] = $wcd_payment_method->get_title();
			}
		}

		$current_date  = current_time( 'timestamp' );
		$wcd_args['wcd_order_currency'] = $order->get_currency();
		$wcd_args['wcd_subscription_status'] = 'pending';
		$wcd_args['wcd_schedule_start'] = $current_date;
		$wcd_args['wcd_parent_order'] = $order_id;

		if ( isset( $args['wcd_sub_initial_product_signup_fee'] ) && ! empty( $args['wcd_sub_initial_product_signup_fee'] ) && empty( $args['wcd_woo_sub_product_trial_number'] ) ) {
			$initial_signup_price = $args['wcd_sub_initial_product_signup_fee'];
			$include_tax = get_option( 'woocommerce_prices_include_tax' );

			if ( 'yes' === $include_tax ) {
				$line_subtotal = $args['recurring_total'] + $args['line_subtotal_tax'];
				$line_total = $args['recurring_total'] + $args['line_tax'];
			} else {
				$line_subtotal = $args['recurring_total'];
				$line_total    = $args['recurring_total'];
			}

			$wcd_args['line_subtotal'] = $line_subtotal;
			$wcd_args['line_total']    = $line_total;
		} else {
			$wcd_args['line_subtotal'] = $args['recurring_total'];
			$wcd_args['line_total'] = $args['recurring_total'];
			$line_subtotal = $wcd_args['line_subtotal'];
			$line_total = $wcd_args['line_total'];
		}

		$product_data = wc_get_product( $args['product_id'] );

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

		$item_id = $subs_order->add_product(
			$product_data,
			$args['quantity'],
			$wcd_pro_args
		);

		$subs_order->update_taxes();
		$subs_order->calculate_totals();
		$subs_order->save();

		do_action( 'wcd_subscription_order', $subs_order, $order_id );
		if ( $subs_id ) {
			$this->id = $subs_id;
			$this->update_subscription_metadata( $args, $wcd_args );
		}
		return $subs_id;
	}

	/**
	 * This function is to update subscription meta data.
	 *
	 * @name update_subscription_metadata
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  array $meta meta.
	 * @param  array $extra_meta extra_meta.
	 */
	public function update_subscription_metadata( $meta, $extra_meta ) {
		if ( ! empty( $meta ) ) {
			foreach ( $meta as $key => $value ) {
				update_post_meta( $this->id, $key, $value );
			}
		}
		if ( ! empty( $extra_meta ) ) {
			foreach ( $extra_meta as $key1 => $value1 ) {
				update_post_meta( $this->id, $key1, $value1 );
			}
		}
	}

}
