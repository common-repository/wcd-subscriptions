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
class Wcd_Subscriptions_Checkout_Page {

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
			add_filter( 'woocommerce_order_button_text', array( $this, 'change_place_order_button_text' ), 10, 2 );
			add_action( 'woocommerce_available_payment_gateways', array( $this, 'show_only_online_gateways' ), 10, 1 );
			add_action( 'pre_option_woocommerce_enable_guest_checkout', array( $this, 'disable_guest_checkout' ), 10, 1 );
			add_filter( 'woocommerce_checkout_registration_required', array( $this, 'registration_required' ), 900, 1 );
		}

	}

	/**
	 * This function is to change text of place order.
	 *
	 * @name change_place_order_button_text
	 * @since    1.0.0
	 * @param  array $text text.
	 */
	public function change_place_order_button_text( $text ) {

		$new_text = get_option( 'wcd_woo_subs_changed_place_order_button', 'Signup' );
		return $new_text;
	}

	/**
	 * This function is to disable offline payment.
	 *
	 * @name show_only_online_gateways
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  array $available_gateways available_gateways.
	 */
	public function show_only_online_gateways( $available_gateways ) {
		if ( ! is_checkout() ) {

			return $available_gateways;
		}

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$product_id = $cart_item['data']->get_id();
				if ( wcd_is_this_subscription_product( $product_id ) ) {
					if ( isset( $available_gateways ) && ! empty( $available_gateways ) && is_array( $available_gateways ) ) {
						foreach ( $available_gateways as $key => $gateways ) {
							$supported_payment_gateways = array( 'stripe', 'ppec_paypal' );
							// Supported paymnet gateway.
							$payment_methods = apply_filters( 'wcd_supported_payment_gateway_for_woocommerce', $supported_payment_gateways, $key );

							if ( ! in_array( $key, $payment_methods ) ) {
								unset( $available_gateways[ $key ] );
							}
						}
					}
				}
			}
		}
		return $available_gateways;
	}

	/**
	 * This function is to disable guest checkout.
	 *
	 * @name disable_guest_checkout
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  string $value value.
	 */
	public function disable_guest_checkout( $value ) {

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$product_id = $cart_item['data']->get_id();
				if ( wcd_is_this_subscription_product( $product_id ) ) {
					$value = 'no';
				}
			}
		}
		return $value;

	}

	/**
	 * This function is to validate registration.
	 *
	 * @name registration_required
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  string $rgstr_req rgstr_req.
	 */
	public function registration_required( $rgstr_req ) {
		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$product_id = $cart_item['data']->get_id();
				if ( wcd_is_this_subscription_product( $product_id ) ) {
					$rgstr_req = true;
				}
			}
		}
		return $rgstr_req;
	}

}

$object_of_this_class = new Wcd_Subscriptions_Checkout_Page();
$object_of_this_class::get_instance();
