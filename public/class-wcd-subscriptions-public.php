<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/public
 */

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
class Wcd_Subscriptions_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wcd_Subscriptions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wcd_Subscriptions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wcd-subscriptions-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wcd_Subscriptions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wcd_Subscriptions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wcd-subscriptions-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Display the wcd subscription details of the myaccount for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function show_subscription_price() {
		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$product_id = $cart_item['data']->get_id();
				if ( wcd_is_this_subscription_product( $product_id ) ) {
					if ( function_exists( 'wcd_if_product_is_onetime' ) && wcd_if_product_onetime( $product_id ) ) {
						return;
					}
					$product_price = $this->change_single_product_page_price( $cart_item['data'], $cart_item['key'] );
					?>
					<tr class="order-total wcd_recurring_total">
					<th class="wcd_recurring_total_td" data-title="<?php esc_attr_e( 'wcd-ws-recurring', 'wcd-subscriptions' ); ?>"><?php esc_attr_e( 'Subscription amount', 'wcd-subscriptions' ); ?></th>
					<td><?php echo esc_attr__( 'Subscription Amount will be', 'wcd-subscriptions' ) . ' ' . wp_kses_post( $product_price ) . ' ' . esc_attr__( 'For', 'wcd-subscriptions' ) . ' ' . esc_html( $cart_item['data']->get_name() ); ?></td>
					<tr>
					<?php
				}
			}
		}
	}

	/**
	 * Display the wcd subscription details of the myaccount for the public-facing side of the site.
	 *
	 * @param object $product $product.
	 * @param array  $cart_key cart_key.
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function change_single_product_page_price( $product, $cart_key = '' ) {

		if ( $product->is_on_sale() ) {
			$price = $product->get_sale_price();
		} else {
			$price = $product->get_regular_price();
		}
		$product_price = wc_price( wc_get_price_to_display( $product, array( 'price' => $price ) ) );
		// Use for role base pricing.
		$product_price = $this->get_price_html( $product_price, $product );
		return $product_price;
	}

	/**
	 * Display the wcd subscription details of the myaccount for the public-facing side of the site.
	 *
	 * @param int    $price $price.
	 * @param object $product $product.
	 * @param array  $cart_item cart_item.
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function get_price_html( $price, $product, $cart_item = array() ) {

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
		return $price;
	}

	/**
	 * Display the wcd subscription details of the myaccount for the public-facing side of the site.
	 *
	 * @param int    $order_id order_id.
	 * @param object $data data.
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function process_order_and_create_subscription( $order_id, $data ) {
		$order = wc_get_order( $order_id );
		$order_data = $order->get_data();
		$order_metadata = get_metadata( 'post', $order_id );
		$customer_id = $order->get_customer_id();
		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$wcd_skip_creating_subscription = apply_filters( 'wcd_skip_creating_subscription', true, $cart_item );
				if ( $wcd_skip_creating_subscription ) {
					$product_id = $cart_item['data']->get_id();
					$wcd_woo_sub_product_total_period_number = get_post_meta( $product_id, 'wcd_woo_sub_product_total_period_number', true );
					$wcd_woo_sub_product_total_period_type = get_post_meta( $product_id, 'wcd_woo_sub_product_total_period_type', true );
					$wcd_woo_sub_product_expire_number = get_post_meta( $product_id, 'wcd_woo_sub_product_expire_number', true );
					$wcd_woo_sub_product_expire_interval = get_post_meta( $product_id, 'wcd_woo_sub_product_expire_interval', true );
					$wcd_sub_initial_product_signup_fee = get_post_meta( $product_id, 'wcd_sub_initial_product_signup_fee', true );
					$wcd_woo_sub_product_trial_number = get_post_meta( $product_id, 'wcd_woo_sub_product_trial_number', true );
					$wcd_woo_sub_product_trial_number_interval = get_post_meta( $product_id, 'wcd_woo_sub_product_trial_number_interval', true );
					$wcd_woo_radio_interval = get_post_meta( $product_id, 'wcd_woo_radio_interval', true );
					if ( wcd_is_this_subscription_product( $product_id ) ) {
						if ( $cart_item['data']->is_on_sale() ) {
							$price = $cart_item['data']->get_sale_price();
						} else {
							$price = $cart_item['data']->get_regular_price();
						}
						$subscription_total = $price * $cart_item['quantity'];
	
						$args = array(
							'product_id'              => $product_id,
							'variation_id'            => $cart_item['variation_id'],
							'product_name'            => $cart_item['data']->get_name(),
	
							// order details.
							'order_id'                => $order_id,
							'order_item_id'           => $cart_item['key'],
							'order_ids'               => array( $order_id ),
							'line_subtotal'           => $cart_item['line_subtotal'],
							'line_total'              => $cart_item['line_total'],
							'line_subtotal_tax'       => $cart_item['line_subtotal_tax'],
							'line_tax'                => $cart_item['line_tax'],
							'line_tax_data'           => $cart_item['line_tax_data'],
							'cart_discount'           => $order_metadata['_cart_discount'][0],
							'cart_discount_tax'       => $order_metadata['_cart_discount_tax'][0],
							'coupons'                 => ( isset( $order_metadata['_coupons'][0] ) ) ? $order_metadata['coupons'][0] : '',
							'order_total'             => $order_metadata['_order_total'][0],
							'subscription_total'      => $order_metadata['_order_total'][0],
							'recurring_total'         => $subscription_total,
							'order_tax'               => $order_metadata['_order_tax'][0],
							'order_subtotal'          => $order->get_subtotal(),
							'order_shipping'          => $order_metadata['_order_shipping'][0],
							'order_shipping_tax'      => $order_metadata['_order_shipping_tax'][0],
							'payment_method'          => $order_data['payment_method'],
							'payment_method_title'    => $order_data['payment_method_title'],
							'order_currency'          => $order_data['currency'],
							'prices_include_tax'      => $order_metadata['_prices_include_tax'][0],
							// user details.
							'quantity'                => $cart_item['quantity'],
							'user_id'                 => $customer_id,
							'customer_ip_address'     => $order_metadata['_customer_ip_address'][0],
							'customer_user_agent'     => $order_metadata['_customer_user_agent'][0],
							// item subscription detail.
							'wcd_woo_sub_product_total_period_number'            => $wcd_woo_sub_product_total_period_number,
							'wcd_woo_sub_product_total_period_type'       => $wcd_woo_sub_product_total_period_type,
							'wcd_woo_sub_product_expire_number'              => $wcd_woo_sub_product_expire_number,
							'wcd_woo_sub_product_expire_interval'               => $wcd_woo_sub_product_expire_interval,
							'wcd_sub_initial_product_signup_fee'       => $wcd_sub_initial_product_signup_fee,
							'wcd_woo_sub_product_trial_number'                     => $wcd_woo_sub_product_trial_number,
							'wcd_woo_sub_product_trial_number_interval'            => $wcd_woo_sub_product_trial_number_interval,
							'wcd_woo_radio_interval'            => $wcd_woo_radio_interval,
						);
	
						$subscription = new Wcd_Subscriptions_Creation( '', $args, $order_id );
						if ( $subscription->id ) {
							/* translators: %d: search term */
							$order->add_order_note( sprintf( __( 'A new subscription #%d has been created from this order', 'yith-woocommerce-subscription' ), $subscription->id ) );
	
							$wcd_has_susbcription = get_post_meta( $order_id, 'wcd_order_has_subscription', true );
							if ( 'yes' != $wcd_has_susbcription ) {
								update_post_meta( $order_id, 'wcd_order_has_subscription', 'yes' );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Display the wcd subscription details of the myaccount for the public-facing side of the site.
	 *
	 * @param int    $order_id order_id.
	 * @param string $old_status old_status.
	 * @param string $new_status new_status.
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function change_subs_status_and_save_subscription_meta( $order_id, $old_status, $new_status ) {

		if ( $old_status != $new_status ) {
			if ( 'completed' == $new_status || 'processing' == $new_status ) {
				$subs_activated = get_post_meta( $order_id, 'subscription_activated', true );
				if ( 'yes' == $subs_activated ) {
					return;
				}
				$wcd_has_susbcription = get_post_meta( $order_id, 'wcd_order_has_subscription', true );

				if ( 'yes' == $wcd_has_susbcription ) {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wcd_subscriptions',
						'post_status'   => 'wc-wcd_recurring',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wcd_parent_order',
								'value' => $order_id,
							),
							array(
								'key'   => 'wcd_subscription_status',
								'value' => 'pending',
							),

						),
					);
					$wcd_subs = get_posts( $args );

					if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {
						foreach ( $wcd_subs as $key => $subscription ) {

							$status = 'active';
							$current_time = current_time( 'timestamp' );

							update_post_meta( $subscription->ID, 'wcd_subscription_status', $status );
							update_post_meta( $subscription->ID, 'wcd_schedule_start', $current_time );

							$wcd_susbcription_trial_end = 0;
							$trial_number = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_trial_number', true );
							$trial_interval = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_trial_number_interval', true );

							if ( isset( $trial_number ) && ! empty( $trial_number ) ) {
								$wcd_susbcription_trial_end = wcd_calculate_time_of_subs_entity( $current_time, $trial_number, $trial_interval );
							}
							update_post_meta( $subscription->ID, 'wcd_susbcription_trial_end', $wcd_susbcription_trial_end );

							$wcd_next_payment_date = 0;
							$wcd_subs_expiry_date = 0;
							$subs_number = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_total_period_number', true );
							$subs_interval = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_total_period_type', true );
							$expiry_number = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_expire_number', true );
							$expiry_interval = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_expire_interval', true );

							if ( 0 != $wcd_susbcription_trial_end ) {

								$wcd_next_payment_date = $wcd_susbcription_trial_end;
								if ( isset( $expiry_number ) && ! empty( $expiry_number ) ) {
									$wcd_subs_expiry_date = wcd_calculate_time_of_subs_entity( $wcd_susbcription_trial_end, $expiry_number, $expiry_interval );
								}
							} else {
								if ( isset( $subs_number ) && ! empty( $subs_number ) ) {
									$wcd_next_payment_date = wcd_calculate_time_of_subs_entity( $current_time, $subs_number, $subs_interval );
								}
								if ( isset( $expiry_number ) && ! empty( $expiry_number ) ) {
									$wcd_subs_expiry_date = wcd_calculate_time_of_subs_entity( $current_time, $expiry_number, $expiry_interval );
								}
							}

							update_post_meta( $subscription->ID, 'wcd_next_payment_date', $wcd_next_payment_date );

							update_post_meta( $subscription->ID, 'wcd_susbcription_end', $wcd_subs_expiry_date );

							// Set billing id.
							$billing_agreement_id = get_post_meta( $order_id, '_ppec_billing_agreement_id', true );
							if ( isset( $billing_agreement_id ) && ! empty( $billing_agreement_id ) ) {
								update_post_meta( $subscription->ID, 'wcd_paypal_subs_id', $billing_agreement_id );
							}
						}
						update_post_meta( $order_id, 'subscription_activated', 'yes' );
					}
				}
			}

			if ( 'cancelled' === $new_status ) {
				$wcd_has_susbcription = get_post_meta( $order_id, 'wcd_order_has_subscription', true );

				if ( 'yes' == $wcd_has_susbcription ) {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wcd_subscriptions',
						'post_status'   => 'wc-wcd_recurring',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wcd_parent_order',
								'value' => $order_id,
							),
							array(
								'key'   => 'wcd_subscription_status',
								'value' => array( 'active', 'pending' ),
							),
						),
					);
					$wcd_subs = get_posts( $args );
					if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {
						foreach ( $wcd_subs as $key => $subscription ) {
							if ( isset( $subscription->ID ) && ! empty( $subscription->ID ) ) {
								$mailer = WC()->mailer()->get_emails();
								// Send the "cancel" notification.
								if ( isset( $mailer['wcd_subscription_cancel'] ) ) {
									 $mailer['wcd_subscription_cancel']->trigger( $subscription->ID );
								}
							}
							update_post_meta( $subscription->ID, 'wcd_subscription_status', 'cancelled' );
						}
					}
				}
			} elseif ( 'failed' === $new_status ) {
				$mailer = WC()->mailer()->get_emails();
				if ( isset( $mailer['WC_Email_Failed_Order'] ) ) {
					$mailer['WC_Email_Failed_Order']->trigger( $order_id );
				}
				$wcd_has_renewal_susbcription = get_post_meta( $order_id, 'wcd_renewal_order', true );
				if ( 'yes' == $wcd_has_renewal_susbcription ) {
					$parent_order = get_post_meta( $order_id, 'wcd_parent_order_id', true );
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wcd_subscriptions',
						'post_status'   => 'wc-wcd_recurring',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wcd_parent_order',
								'value' => $parent_order,
							),
							array(
								'key'   => 'wcd_subscription_status',
								'value' => array( 'active', 'pending' ),
							),
						),
					);
					$wcd_subs = get_posts( $args );
					if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {
						foreach ( $wcd_subs as $key => $subscription ) {

							update_post_meta( $subscription->ID, 'wcd_subscription_status', 'on-hold' );
						}
					}
				} else {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wcd_subscriptions',
						'post_status'   => 'wc-wcd_recurring',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wcd_parent_order',
								'value' => $order_id,
							),
							array(
								'key'   => 'wcd_subscription_status',
								'value' => array( 'active', 'pending' ),
							),
						),
					);

					$wcd_subs = get_posts( $args );
					if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {
						foreach ( $wcd_subs as $key => $subscription ) {
							update_post_meta( $subscription->ID, 'wcd_subscription_status', 'on-hold' );
						}
					}
				}
			} elseif ( 'completed' == $new_status || 'processing' == $new_status ) {
				$wcd_has_renewal_susbcription = get_post_meta( $order_id, 'wcd_renewal_order', true );
				if ( 'yes' == $wcd_has_renewal_susbcription ) {
					$parent_order = get_post_meta( $order_id, 'wcd_parent_order_id', true );
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wcd_subscriptions',
						'post_status'   => 'wc-wcd_recurring',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wcd_parent_order',
								'value' => $parent_order,
							),
							array(
								'key'   => 'wcd_subscription_status',
								'value' => 'on-hold',
							),
						),
					);

					$wcd_subs = get_posts( $args );
					if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {
						foreach ( $wcd_subs as $key => $subscription ) {
							$subs_number = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_total_period_number', true );
							$subs_interval = get_post_meta( $subscription->ID, 'wcd_woo_sub_product_total_period_type', true );
							if ( isset( $subs_number ) && ! empty( $subs_number ) ) {
								$wcd_next_payment_date = wcd_calculate_time_of_subs_entity( $current_time, $subs_number, $subs_interval );
							}
							update_post_meta( $subscription->ID, 'wcd_subscription_status', 'active' );
							update_post_meta( $subscription->ID, 'wcd_next_payment_date', $wcd_next_payment_date );
						}
					}
				}
			}
		}

	}

	/**
	 * Display the wcd subscription details of the myaccount for the public-facing side of the site.
	 *
	 * @param int $wcd_woo_subscription_id wcd_woo_subscription_id.
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_woocommerce_show_subscription_details( $wcd_woo_subscription_id ) {
		?>
				<div class="wcd_woo_sub_details_parent_wrap">
		
				<table class="shop_table wcd_woo_sub_details_wrap">
					<h3><?php esc_html_e( 'My Subscription', 'wcd-subscriptions' ); ?></h3>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Status', 'wcd-subscriptions' ); ?></td>
						<?php $wcd_woo_sub_status = get_post_meta( $wcd_woo_subscription_id, 'wcd_subscription_status', true ); ?>
						<td class="<?php echo esc_html( 'wcd_woo_' . $wcd_woo_sub_status ); ?>">
						<?php
							echo esc_html( $wcd_woo_sub_status );
						?>
						</td>
					</tr>
		
					<tr>
						<td><?php esc_html_e( 'Subscription Date', 'wcd-subscriptions' ); ?></td>
						<td>
						<?php
							$wcd_woo_sub_start_time = get_post_meta( $wcd_woo_subscription_id, 'wcd_schedule_start', true );
							echo esc_html( wcd_woo_sub_get_the_formated_date( $wcd_woo_sub_start_time ) );
						?>
						</td>
					</tr>
		
		
					<tr>
						<td><?php esc_html_e( 'Next Payment Date', 'wcd-subscriptions' ); ?></td>
						<td>
						<?php
							$wcd_woo_next_payment_date_scheuled = get_post_meta( $wcd_woo_subscription_id, 'wcd_next_payment_date', true );
						if ( 'cancelled' === $wcd_woo_sub_status ) {
							$wcd_next_payment_date = '';
							$wcd_susbcription_end = '';
							$wcd_recurring_total = 'xxxxxxx';
						}
							echo esc_html( wcd_woo_sub_get_the_formated_date( $wcd_woo_next_payment_date_scheuled ) );
						?>
						</td>
					</tr>
		
					<?php
					$wcd_free_trial_period = get_post_meta( $wcd_woo_subscription_id, 'wcd_susbcription_trial_end', true );

					if ( ! empty( $wcd_free_trial_period ) ) {
						?>
						<tr>
							<td><?php esc_html_e( 'Trial End Date', 'wcd-subscriptions' ); ?></td>
							<td>
							<?php
								echo esc_html( wcd_woo_sub_get_the_formated_date( $wcd_free_trial_period ) );
							?>
							</td>
						</tr>
						<?php
					}
					?>
					
					<?php
						$wcd_woo_next_payment_date = get_post_meta( $wcd_woo_subscription_id, '_payment_method', true );
					if ( ! empty( $wcd_woo_next_payment_date ) ) {
							 $wcd_woo_subscription = wc_get_order( $wcd_woo_subscription_id );
							$wcd_woo_sub_add_new_payment_url = wp_nonce_url( add_query_arg( array( 'wcd-new-add-payment-method' => $wcd_woo_subscription_id ), $wcd_woo_subscription->get_checkout_payment_url() ) );
						?>
									<tr>
										<td>
											<a href="<?php echo esc_url( $wcd_woo_sub_add_new_payment_url ); ?>" class="button wcd_add_new_payment_url"><?php esc_html_e( 'Add Payment Method', 'wcd-subscriptions' ); ?></a>
										</td>
									</tr>
								<?php
					}
					?>
					<?php do_action( 'wcd_woo_sub_add_more_show_details', $wcd_woo_subscription_id ); ?>
				</tbody>
			</table>
		
			<table class="shop_table wcd_woo_sub_order_details">
				<h3><?php esc_html_e( 'WCD WOO Subscription Order Details', 'wcd-subscriptions' ); ?></h3>
				<thead>
					<tr>
						<th>
							<?php esc_html_e( 'Subscription Product Name', 'wcd-subscriptions' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Total Amount', 'wcd-subscriptions' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php
								$wcd_woo_sub_product_name = get_post_meta( $wcd_woo_subscription_id, 'product_name', true );
								$wcd_woo_sub_product_qty = get_post_meta( $wcd_woo_subscription_id, 'product_qty', true );

								echo esc_html( $wcd_woo_sub_product_name ) . ' x ' . esc_html( $wcd_woo_sub_product_qty );
								do_action( 'wcd_product_details_html', $wcd_woo_subscription_id );
							?>
							
						 </td>
						<td>
						<?php
							$this->account_page_of_subscription( $wcd_woo_subscription_id );
							do_action( 'wcd_woo_susbcription_show_recurring_total_account_page', $wcd_woo_subscription_id );
						?>
						</td>
					</tr>
					<?php do_action( 'wcd_woo_sub_show_html_before_cancel_button', $wcd_woo_subscription_id ); ?>
					<tr>
						<?php
							$wcd_woo_sub_is_cancel = get_option( 'wcd_woo_subs_customer_can_cancel_subscription', '' );
						if ( 'yes' == $wcd_woo_sub_is_cancel ) {

							$wcd_status = get_post_meta( $wcd_woo_subscription_id, 'wcd_subscription_status', true );
							if ( 'active' == $wcd_status ) {
								$wcd_woo_cancel_url = $this->wcd_woo_sub_is_cancel_url( $wcd_woo_subscription_id, $wcd_status );
								?>
									<td>
										<a href="<?php echo esc_url( $wcd_woo_cancel_url ); ?>" class="button wcd_woo_cancel_subscription"><?php esc_html_e( 'Cancel', 'wcd-subscriptions' ); ?></a>
									</td>
								<?php
							}
						}
						?>
							<?php do_action( 'wcd_woo_subscription_order_details_html_after_cancel_button', $wcd_woo_subscription_id ); ?>
						</tr>
				</tbody>
			</table>
			<?php do_action( 'wcd_woo_after_all_suscription_deatails', $wcd_woo_subscription_id ); ?>
		</div>
		
		<?php
	}


	/**
	 * Register end point of the myaccount for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function bbloomer_add_premium_support_endpoint() {
		add_rewrite_endpoint( 'wcd-woo-sub-customer-listing', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'wcd-woo-sub-customer-details', EP_PAGES );
		add_rewrite_endpoint( 'wcd-new-add-payment-method', EP_PAGES );
	}

	/**
	 * Register end point of the myaccount for the public-facing side of the site.
	 *
	 * @param array $vars vars.
	 * @since    1.0.0
	 */
	public function bbloomer_premium_support_query_vars( $vars ) {
		$vars[] = 'wcd-woo-sub-customer-listing';
		$vars[] = 'wcd-woo-sub-customer-details';
		$vars[] = 'wcd-new-add-payment-method';
		return $vars;
	}

	/**
	 * Register Name of the myaccount for the public-facing side of the site.
	 *
	 * @param array $items items.
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function bbloomer_add_premium_support_link_my_account( $items ) {
		$items['wcd-woo-sub-customer-listing'] = 'WCD Subscription';
		return $items;
	}

	/**
	 * Display the content  of the WCD Subscription for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function bbloomer_premium_support_content() {
		$user_id = get_current_user_id();

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wcd_subscriptions', // our post type.
			'post_status' => 'wc-wcd_recurring',
			'meta_query' => array(
				array(
					'key'   => 'user_id',
					'value' => $user_id,
				),
			),

		);
		$wcd_woo_subscriptions = get_posts( $args );

		$wcd_per_page = get_option( 'posts_per_page', 10 );
		$wcd_current_page = empty( $wcd_current_page ) ? 1 : absint( $wcd_current_page );
		$wcd_num_pages = ceil( count( $wcd_woo_subscriptions ) / $wcd_per_page );
		$subscriptions = array_slice( $wcd_woo_subscriptions, ( $wcd_current_page - 1 ) * $wcd_per_page, $wcd_per_page );
		?>
		<div class="wcd_account_wrap">
		<?php
		if ( ! empty( $wcd_woo_subscriptions ) && is_array( $wcd_woo_subscriptions ) ) {
			?>
		<table>
			<thead>
				<tr>
				<th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-number"><span class="nobr"><?php esc_html_e( 'IDs', 'wcd-subscriptions' ); ?></span></th>
				<th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-status"><span class="nobr"><?php esc_html_e( 'Status', 'wcd-subscriptions' ); ?></span></th>
				<th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-date"><span class="nobr"><?php echo esc_html_e( 'Next payment date', 'wcd-subscriptions' ); ?></span></th>
				<th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-total"><span class="nobr"><?php echo esc_html_e( 'Recurring Total Here', 'wcd-subscriptions' ); ?></span></th>
				<th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-actions"><?php esc_html_e( 'Action', 'wcd-subscriptions' ); ?></th>
				</tr>
			</thead>
			<tbody>
					<?php
					foreach ( $wcd_woo_subscriptions as $key => $wcd_woo_subscription ) {
						$parent_order_id   = get_post_meta( $wcd_woo_subscription->ID, 'wcd_parent_order', true );
						$wcd_is_order = false;
						if ( function_exists( 'wcd_check_valid_order' ) && ! wcd_check_valid_order( $parent_order_id ) ) {
							$wcd_is_order = apply_filters( 'wcd_check_parent_order', $wcd_is_order, $parent_order_id );
							if ( false == $wcd_is_order ) {
								continue;
							}
						}
						?>
						<!----Subscription Id Of the User Here.-->
						<tr class="wcd_account_row woocommerce-orders-table_row woocommerce-orders-table_row--status-processing order">
						  <td class="wcd_account_col woocommerce-orders-table_cell woocommerce-orders-table_cell-order-number">
						  <?php echo esc_html( $wcd_woo_subscription->ID ); ?>
						  </td>
						<!----Subscription Status Of the User Here.-->
						 <td class="wcd_account_col woocommerce-orders-table_cell woocommerce-orders-table_cell-order-status">
						  <?php
							$wcd_status = get_post_meta( $wcd_woo_subscription->ID, 'wcd_subscription_status', true );
							echo esc_html( $wcd_status );
							?>
						</td>
						<!----Subscription Next Payment Date Of the User Here.-->
						<td class="wcd_account_col woocommerce-orders-table_cell woocommerce-orders-table_cell-order-date">
							<?php
							$wcd_next_payment_date = get_post_meta( $wcd_woo_subscription->ID, 'wcd_next_payment_date', true );
							if ( 'cancelled' === $wcd_status ) {
								$wcd_next_payment_date = '';
							}
							echo esc_html( wcd_woo_sub_get_the_formated_date( $wcd_next_payment_date ) );
							?>
						</td>
						<!----Subscription Recurring Details Of the User Here.-->
						<td class="wcd_account_col woocommerce-orders-table_cell woocommerce-orders-table_cell-order-total">
						<?php
						$this->account_page_of_subscription( $wcd_woo_subscription->ID );
						?>
						</td>
						<!----Subscription Showing Of the User Here.-->
						<td class="wcd_account_col woocommerce-orders-table_cell woocommerce-orders-table_cell-order-actions">
							<span class="wcd_account_show_subscription">
								<a href="
						<?php
						echo esc_url( wc_get_endpoint_url( 'wcd-woo-sub-customer-details', $wcd_woo_subscription->ID, wc_get_page_permalink( 'myaccount' ) ) );
						?>
								">
						<?php
						esc_html_e( 'View', 'wcd-subscriptions' );
						?>
								</a>
							</span>
						</td>
					</tr>
						<?php
					}
					?>
				 </tbody>
		</table>
			<?php
			if ( 1 < $wcd_num_pages ) {
				?>
			<div class="wcd_pagination woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
				<?php if ( 1 !== $wcd_current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'wcd-woo-sub-customer-listing', $wcd_current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'wcd-subscriptions' ); ?></a>
			<?php } ?>
					<?php if ( intval( $wcd_num_pages ) !== $wcd_current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'wcd-woo-sub-customer-listing', $wcd_current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'wcd-subscriptions' ); ?></a>
			<?php } ?>
			</div>
			<?php } ?>
				<?php
		} else {
			esc_html_e( 'You do not have any active subscription(s).', 'wcd-subscriptions' );
		}
		?>
		</div>
		<?php
	}

	/**
	 * This function is used to add payment method form.
	 *
	 * @name wcd_woo_subscription_after_woocommerce_pay
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_woo_subscription_after_woocommerce_pay() {
		global $wp;
		$wcd_valid_request = false;

		if ( ! isset( $wp->query_vars['order-pay'] ) || ! wcd_woo_subscription_check_subscription( absint( $wp->query_vars['order-pay'] ) ) ) {
			return;
		}
		ob_clean();
		$wcd_woo_subscription_order_details  = wc_get_order( absint( isset( $_GET['wcd-new-add-payment-method'] ) ? $_GET['wcd-new-add-payment-method'] : '' ) );

		echo '<div class="woocommerce">';
		if ( ! isset( $_GET['wcd-new-add-payment-method'] ) && empty( $_GET['wcd-new-add-payment-method'] ) ) {
			return;
		}
		$wcd_woo_valid_request = wcd_payment_check_request( $wcd_woo_subscription_order_details );
		if ( true == $wcd_woo_valid_request ) {
			$this->wcd_woo_subscription_customer_address_set_for_payment( $wcd_woo_subscription_order_details );

			include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/wcd-woo-add-new-payment-method-for-subscription.php';
		}
	}


	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name wcd_woo_on_cancel_susbcription
	 * @since 1.0.0
	 */
	public function wcd_woo_on_cancel_susbcription() {

		if ( isset( $_GET['wcd_woo_subscription_status'] ) && isset( $_GET['wcd_woo_subscription_id'] ) && isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			$user_id      = get_current_user_id();

			$wcd_woo_subscription_status  = sanitize_text_field( wp_unslash( $_GET['wcd_woo_subscription_status'] ) );
			$wcd_woo_subscription_id = sanitize_text_field( wp_unslash( $_GET['wcd_woo_subscription_id'] ) );
			if ( wcd_woo_subscription_check_subscription( $wcd_woo_subscription_id ) ) {
				$this->wcd_woo_cancel_the_susbcription_by_customer_itself( $wcd_woo_subscription_id, $wcd_woo_subscription_status, $user_id );

			}
		}
	}

	/**
	 * This function is used to process payment method form.
	 *
	 * @name wcd_woo_lets_change_payment_method_form
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_woo_lets_change_payment_method_form() {
		if ( ! isset( $_POST['_wcd_woo_sub_noncee'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wcd_woo_sub_noncee'] ) ), 'wcd_woo_subcription_change_payment_method' ) ) {
			return;
		}

		if ( ! isset( $_POST['wcd_woo_lets_change_payment_method'] ) && empty( $_POST['wcd_woo_lets_change_payment_method'] ) ) {
			return;
		}
		$subscription_id = absint( $_POST['wcd_woo_lets_change_payment_method'] );
		$wcd_woo_subscription = wc_get_order( $subscription_id );

		ob_start();
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		if ( $wcd_woo_subscription->get_order_key() == $order_key ) {

			$this->wcd_woo_subscription_customer_address_set_for_payment( $wcd_woo_subscription );
			// Update payment method.
			$new_payment_method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '';
			if ( empty( $new_payment_method ) ) {

				$wcd_woo_notices = __( 'Please enable payment method', 'wcd-subscriptions' );
				wc_add_notice( $wcd_woo_notices, 'error' );
				$result_redirect = wc_get_endpoint_url( 'wcd-woo-sub-customer-listing', $wcd_woo_subscription->get_id(), wc_get_page_permalink( 'myaccount' ) );
				wp_safe_redirect( $result_redirect );
				exit;
			}
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			$available_gateways[ $new_payment_method ]->validate_fields();
			$payment_method_title = $available_gateways[ $new_payment_method ]->get_title();

			if ( wc_notice_count( 'error' ) == 0 ) {

				$result = $available_gateways[ $new_payment_method ]->process_payment( $wcd_woo_subscription->get_id(), false, true );

				if ( 'success' == $result['result'] ) {
					$result['redirect'] = wc_get_endpoint_url( 'wcd-woo-sub-customer-listing', $wcd_woo_subscription->get_id(), wc_get_page_permalink( 'myaccount' ) );
						update_post_meta( $wcd_woo_subscription->get_id(), '_payment_method', $new_payment_method );
						update_post_meta( $wcd_woo_subscription->get_id(), '_payment_method_title', $payment_method_title );
				}

				if ( 'success' != $result['result'] ) {
					return;
				}
				$wcd_woo_subscription->save();

				$wcd_woo_notices = __( 'Hurray !!, Payment Method Added Successfully', 'wcd-subscriptions' );
				wc_add_notice( $wcd_woo_notices );
				wp_safe_redirect( $result['redirect'] );
				exit;
			}
		}
		ob_get_clean();
	}


	/**
	 * This function is used to show recurring price on account page.
	 *
	 * @name account_page_of_subscription
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int $subscription_id subscription_id.
	 */
	public function account_page_of_subscription( $subscription_id ) {
		$susbcription = wc_get_order( $subscription_id );

		if ( isset( $susbcription ) && ! empty( $susbcription ) ) {
			$price = $susbcription->get_total();
			$wcd_curr_args = array(
				'currency' => $susbcription->get_currency(),
			);
		} else {
			$price = get_post_meta( $subscription_id, 'wcd_recurring_total', true );
		}

		$wcd_woo_subscription_curr_args = array();

		$price = wc_price( $price, $wcd_woo_subscription_curr_args );
		$wcd_woo_subscription_recurring_number = get_post_meta( $subscription_id, 'wcd_woo_sub_product_total_period_number', true );// chnage key.
		$wcd_woo_sub_recurring_interval = get_post_meta( $subscription_id, 'wcd_woo_sub_product_total_period_type', true );// change the key.
		$wcd_woo_the_price_html = $this->wcd_woo_subscription_fetch_time_format_for_price( $wcd_woo_subscription_recurring_number, $wcd_woo_sub_recurring_interval );

		/* translators: %s: subscription interval */
		$price .= sprintf( esc_html( ' / %s ' ), $wcd_woo_the_price_html );
		$wcd_woo_subscription_status = get_post_meta( $subscription_id, 'wcd_subscription_status', true );// change the key.
		if ( 'cancelled' === $wcd_woo_subscription_status ) {
			$price = 'xxx';
		}
		echo wp_kses_post( $price );
	}

	/**
	 * This function is used to show recurring price on account page with above function.
	 *
	 * @name wcd_woo_subscription_fetch_time_format_for_price
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int $wcd_woo_subscription_number wcd_woo_subscription_number.
	 * @param int $wcd_woo_subscription_interval subscription time period.
	 */
	public function wcd_woo_subscription_fetch_time_format_for_price( $wcd_woo_subscription_number, $wcd_woo_subscription_interval ) {
		$wcd_woo_sub_number = $wcd_woo_subscription_number;
		if ( 1 == $wcd_woo_subscription_number ) {
			$wcd_woo_subscription_number = '';
		}

		$wcd_woo_the_price_html = '';
		switch ( $wcd_woo_subscription_interval ) {
			case 'day':
				/* translators: %s: Day,%s: Days */
				$wcd_woo_the_price_html = sprintf( _n( '%s Day', '%s Days', $wcd_woo_sub_number, 'wcd-subscriptions' ), $wcd_woo_subscription_number );
				break;
			case 'week':
				/* translators: %s: Week,%s: Weeks */
				$wcd_woo_the_price_html = sprintf( _n( '%s Week', '%s Weeks', $wcd_woo_sub_number, 'wcd-subscriptions' ), $wcd_woo_subscription_number );
				break;
			case 'month':
				/* translators: %s: Month,%s: Months */
				$wcd_woo_the_price_html = sprintf( _n( '%s Month', '%s Months', $wcd_woo_sub_number, 'wcd-subscriptions' ), $wcd_woo_subscription_number );
				break;
			case 'year':
				/* translators: %s: Year,%s: Years */
				$wcd_woo_the_price_html = sprintf( _n( '%s Year', '%s Years', $wcd_woo_sub_number, 'wcd-subscriptions' ), $wcd_woo_subscription_number );
				break;
		}
		return $wcd_woo_the_price_html;

	}

	/**
	 * This function is used to set customer address.
	 *
	 * @name wcd_woo_subscription_customer_address_set_for_payment
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param object $wcd_woo_subscription wcd_woo_subscription.
	 * @since    1.0.1
	 */
	public function wcd_woo_subscription_customer_address_set_for_payment( $wcd_woo_subscription ) {
		$wcd_woo_subscription_billing_country  = $wcd_woo_subscription->get_billing_country();
		$wcd_woo_subscription_billing_state  = $wcd_woo_subscription->get_billing_state();
		$wcd_woo_subscription_billing_postcode = $wcd_woo_subscription->get_billing_postcode();
		$wcd_woo_subscription_billing_city     = $wcd_woo_subscription->get_billing_postcode();

		if ( $wcd_woo_subscription_billing_country ) {
			WC()->customer->set_billing_country( $wcd_woo_subscription_billing_country );
		}
		if ( $wcd_woo_subscription_billing_state ) {
			WC()->customer->set_billing_state( $wcd_woo_subscription_billing_state );
		}
		if ( $wcd_woo_subscription_billing_postcode ) {
			WC()->customer->set_billing_postcode( $wcd_woo_subscription_billing_postcode );
		}
		if ( $wcd_woo_subscription_billing_city ) {
			WC()->customer->set_billing_city( $wcd_woo_subscription_billing_city );
		}

	}

	/**
	 * This function is used to get the cancel url to cancel the subscription.
	 *
	 * @param int    $wcd_woo_subscription_id wcd_woo_subscription_id.
	 * @param string $wcd_woo_status wcd_woo_status.
	 * @name wcd_woo_sub_is_cancel_url
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_woo_sub_is_cancel_url( $wcd_woo_subscription_id, $wcd_woo_status ) {

		$wcd_woo_sub_cancel_link = add_query_arg(
			array(
				'wcd_woo_subscription_id'        => $wcd_woo_subscription_id,
				'wcd_woo_subscription_status' => $wcd_woo_status,
			)
		);
		$wcd_woo_sub_cancel_link = wp_nonce_url( $wcd_woo_sub_cancel_link, $wcd_woo_subscription_id . $wcd_woo_status );

		return $wcd_woo_sub_cancel_link;
	}


	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name wcd_woo_cancel_the_susbcription_by_customer_itself
	 * @param int    $wcd_woo_subscription_id wcd_woo_subscription_id.
	 * @param string $wcd_woo_subscription_status wcd_woo_subscription_statu.
	 * @param int    $user_id user_id.
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_woo_cancel_the_susbcription_by_customer_itself( $wcd_woo_subscription_id, $wcd_woo_subscription_status, $user_id ) {

		$wcd_woo_subscription_customer_id = get_post_meta( $wcd_woo_subscription_id, 'wcd_customer_id', true );
		if ( 'active' == $wcd_woo_subscription_status ) {
			do_action( 'wcd_woo_sub_product_on_cancel', $wcd_woo_subscription_id, 'Cancel' );

			do_action( 'wcd_woo_sub_cancel_susbcription_with_user_id', $wcd_woo_subscription_id, $user_id );
			update_post_meta( $wcd_woo_subscription_id, 'wcd_subscription_status', 'cancelled' );
			wc_add_notice( __( 'Subscription Cancelled Successfully', 'wcd-subscriptions' ), 'success' );
			$redirect_url = wc_get_endpoint_url( 'wcd-woo-sub-customer-details', $wcd_woo_subscription_id, wc_get_page_permalink( 'myaccount' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * This function is used to add fees and signup fees.
	 *
	 * @name add_fees_and_signup_fee.
	 * @param  object $cart detail of the cart.
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function add_fees_and_signup_fee( $cart ) {

		if ( isset( $cart ) && ! empty( $cart ) ) {

			foreach ( $cart->cart_contents as $key => $cart_data ) {

				$product_id = $cart_data['data']->get_id();
				if ( wcd_is_this_subscription_product( $product_id ) ) {
					$free_trial = get_post_meta( $product_id, 'wcd_woo_sub_product_trial_number', true );
					$signup_fee = get_post_meta( $product_id, 'wcd_sub_initial_product_signup_fee', true );
					$price = $cart_data['data']->get_price();
					if ( isset( $free_trial ) ) {
						if ( 0 == $signup_fee ) {
							$price = 0;
						} else {
							if ( 0 == $free_trial ) {
								$price += $signup_fee;
							} else {
								$price = $signup_fee;
							}
						}
					} else {
						$price += $signup_fee;
					}
					$cart_data['data']->set_price( $price );
				}
			}
		}

	}



}
