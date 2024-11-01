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
class Wcd_Subscriptions_Cart_Page {

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
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_add_to_cart_button_text' ), 10, 2 );
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'change_add_to_cart_button_text' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'change_cart_item_price' ), 1, 3 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'change_single_product_page_price' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'disable_quantity_field' ), 10, 3 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'disable_to_add_multiple_subscription' ), 10, 5 );
			add_filter( 'woocommerce_cart_needs_payment', array( $this, 'cart_needs_payment' ), 99, 2 );
		}

	}

	/**
	 * This function is to change text of cart.
	 *
	 * @name change_add_to_cart_button_text
	 * @since    1.0.0
	 * @param  string $text text.
	 * @param  int    $product product.
	 */
	public function change_add_to_cart_button_text( $text, $product ) {

		$product_id = $product->get_id();
		if ( wcd_is_this_subscription_product( $product_id ) ) {
			$new_text = get_option( 'wcd_woo_subs_changed_add_to_cart_button', 'Subscribe' );
			return $new_text;
		}
		return $text;
	}

	/**
	 * This function is to set price.
	 *
	 * @name change_cart_item_price
	 * @since    1.0.0
	 * @param  int   $product_price price.
	 * @param  int   $cart_item cart_item.
	 * @param  array $cart_item_key cart_item_key.
	 */
	public function change_cart_item_price( $product_price, $cart_item, $cart_item_key ) {

		$product_id = $cart_item['data']->get_id();
		// This line was inspired by code from the "wpswings" plugin.
		if ( wcd_is_this_subscription_product( $product_id ) ) {

			if ( $cart_item['data']->is_on_sale() ) {
				$price = $cart_item['data']->get_sale_price();
			} else {
				$price = $cart_item['data']->get_regular_price();
			}
			$price         = $price * $cart_item['quantity'];
			$product_price = wc_price( wc_get_price_to_display( $cart_item['data'], array( 'price' => $price ) ) );
			$product_price = $this->get_price_html( $product_price, $cart_item['data'], $cart_item );
		}
		return $product_price;
	}

	/**
	 * This function is to set price html.
	 *
	 * @name change_single_product_page_price
	 * @since    1.0.0
	 * @param  int $price price.
	 * @param  int $product product.
	 */
	public function change_single_product_page_price( $price, $product ) {

		$product_id = $product->get_id();

		if ( wcd_is_this_subscription_product( $product_id ) ) {
			if ( $product->is_on_sale() ) {
				$price = $product->get_sale_price();
			} else {
				$price = $product->get_regular_price();
			}
			$product_price = wc_price( wc_get_price_to_display( $product, array( 'price' => $price ) ) );
			// Use for role base pricing.
			$product_price = $this->get_price_html( $product_price, $product );
			$price = $product_price;
		}
		return $price;
	}

	/**
	 * This function is to set price html.
	 *
	 * @name get_price_html
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  int   $price price.
	 * @param  int   $product product.
	 * @param  array $cart_item cart_item.
	 */
	public function get_price_html( $price, $product, $cart_item = array() ) {
		$current_time = current_time( 'timestamp' );
		$product_id = $product->get_id();
		$period_span_number = get_post_meta( $product_id, 'wcd_woo_sub_product_total_period_number', true );
		$period_span_type = get_post_meta( $product_id, 'wcd_woo_sub_product_total_period_type', true );

		if ( $period_span_number > 1 ) {
			$period_span_type = interval_type( $period_span_type );
			/* translators: %s: susbcription interval */
			$price .= '<span class="wcd_interval">' . sprintf( esc_html( ' / %s %s ' ), $period_span_number, $period_span_type ) . '</span>';
		} else {
			/* translators: %s: susbcription interval */
			$price .= '<span class="wcd_interval">' . sprintf( esc_html( ' / %s ' ), $period_span_type ) . '</span>';
		}

		$trial_number = get_post_meta( $product_id, 'wcd_woo_sub_product_trial_number', true );
		$trial_interval = get_post_meta( $product_id, 'wcd_woo_sub_product_trial_number_interval', true );

		$expiry_number = get_post_meta( $product_id, 'wcd_woo_sub_product_expire_number', true );
		$expiry_interval = get_post_meta( $product_id, 'wcd_woo_sub_product_expire_interval', true );

		$initial_signup_fee = get_post_meta( $product_id, 'wcd_sub_initial_product_signup_fee', true );

		$expiry_infinite_or_finite = get_post_meta( $product_id, 'wcd_woo_radio_interval', true );

		if ( ! empty( $expiry_number ) ) {
			if ( $expiry_number > 0 ) {
				if ( 1 == $expiry_number ) {
					/* translators: %s: susbcription interval */
					$price .= '<span class="expiry">' . sprintf( esc_html__( ' For %1$s %2$s', 'wcd-subscriptions' ), $expiry_number, $expiry_interval ) . '</span>';
				} else {
					$expiry_interval = interval_type( $expiry_interval );
					if ( 0 == $expiry_infinite_or_finite ) {
						/* translators: %s: susbcription interval */
						$price .= '<span class="expiry">' . sprintf( esc_html__( ' For %s', 'wcd-subscriptions' ), 'Lifetime' ) . '</span>';
					} else {
						/* translators: %s: susbcription interval */
						$price .= '<span class="expiry">' . sprintf( esc_html__( ' For %1$s %2$s', 'wcd-subscriptions' ), $expiry_number, $expiry_interval ) . '</span>';
					}
				}
			}
		}

		if ( ! empty( $initial_signup_fee ) ) {
			$currency_symbol = get_woocommerce_currency_symbol();
			if ( $initial_signup_fee > 0 ) {
				/* translators: %s: susbcription interval */
				$price .= '<span class="signup_fee">' . sprintf( esc_html__( ' with %1$s%2$s signup fee', 'wcd-subscriptions' ), $currency_symbol, $initial_signup_fee ) . '</span>';
			}
		}

		if ( ! empty( $trial_number ) ) {
			if ( $trial_number > 0 ) {
				if ( 1 == $trial_number ) {
					/* translators: %s: susbcription interval */
					$price .= '<span class="expiry">' . sprintf( esc_html__( ' and %1$s %2$s free trial', 'wcd-subscriptions' ), $trial_number, $trial_interval ) . '</span>';
				} else {
					$trial_interval = interval_type( $trial_interval );
					/* translators: %s: susbcription interval */
					$price .= '<span class="expiry">' . sprintf( esc_html__( ' and %1$s %2$s free trial', 'wcd-subscriptions' ), $trial_number, $trial_interval ) . '</span>';
				}
			}
		}

		$price = apply_filters( 'wcd_show_one_time_subscription_price', $price, $product_id );

		return $price;
	}

	/**
	 * This function is to disable quantity field.
	 *
	 * @name disable_quantity_field
	 * @since    1.0.0
	 * @param  int $product_quantity product_quantity.
	 * @param  int $cart_item_key cart_item_key.
	 * @param  int $cart_item cart_item.
	 */
	public function disable_quantity_field( $product_quantity, $cart_item_key, $cart_item ) {
		$product_id = $cart_item['product_id'];
		if ( wcd_is_this_subscription_product( $product_id ) ) {
			return apply_filters( 'show_quantity_field', '<input type="number" id="quantity_6429d0eeab7ca" class="input-text qty text" name="cart[e369853df766fa44e1ed0ff613f563bd][qty]" value="1" title="Qty" size="4" min="0" max="" step="1" placeholder="" inputmode="numeric" autocomplete="off" disabled="">', $product_quantity );
		}
		return $product_quantity;
	}

	/**
	 * This function is to disable multiple buy.
	 *
	 * @name disable_to_add_multiple_subscription
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  string $validation_successfull validation_successful.
	 * @param  int    $product_id product_id.
	 * @param  int    $quantity quantity.
	 * @param  int    $variation_id variation_id.
	 * @param  int    $variations variations.
	 */
	public function disable_to_add_multiple_subscription( $validation_successfull, $product_id, $quantity, $variation_id = 0, $variations = null ) {

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$product_id = $cart_item['data']->get_id();
				if ( wcd_is_this_subscription_product( $product_id ) ) {
					$validation_successfull = false;
					$message = esc_html__( 'You already have subscription item in your cart , and you can not add multiple subscriptions in cart.', 'wcd-subscriptions' );
					break;
				}
			}
		}
		$validation = apply_filters( 'allow_multiple_subscriptions', $validation_successfull );
		if ( ! $validation ) {
			wc_add_notice( $message, 'error' );
		}
		return $validation;
	}


	/**
	 * This function is to add payment to cart.
	 *
	 * @name cart_needs_payment
	 * @since    1.0.0
	 * @param  string $cart_needs_payment cart_needs_payment.
	 * @param  object $cart cart.
	 */
	public function cart_needs_payment( $cart_needs_payment, $cart ) {
		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$product_id = $cart_item['data']->get_id();
				if ( wcd_is_this_subscription_product( $product_id ) ) {
					$product_id = $cart_item['data']->get_id();
					$free_trial = get_post_meta( $product_id, 'wcd_woo_sub_product_trial_number', true );
					if ( ! empty( $free_trial ) && $free_trial > 0 ) {
						if ( 0 == $cart->get_total( 'edit' ) ) {
							$cart_needs_payment = true;
						}
					} else if ( 0 == $cart->get_total( 'edit' ) ) {
						$cart_needs_payment = true;
					}
				}
			}
		}
		return $cart_needs_payment;
	}

}

$object_of_this_class = new Wcd_Subscriptions_Cart_Page();
$object_of_this_class::get_instance();
