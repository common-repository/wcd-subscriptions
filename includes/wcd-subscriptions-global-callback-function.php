<?php
/**
 * Exit if accessed directly
 *
 * @since      1.0.0
 * @package    Subscriptions_For_Woocommerce
 * @subpackage Subscriptions_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'wcd_sub_plugin_is_enable' ) ) {
	/**
	 * This function is used to check plugin is enable.
	 *
	 * @name wcd_sub_plugin_is_enable
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	function wcd_sub_plugin_is_enable() {
		$wcd_is_enable = false;
		$wcd_is_sub_enable_plugin = get_option( 'wcd_woo_subs_enable_plugin', '' );
		if ( 'yes' == $wcd_is_sub_enable_plugin ) {
			$wcd_is_enable = true;
		}
		return $wcd_is_enable;
	}
}

if ( ! function_exists( 'wcd_sub_product_timespan' ) ) {

	/**
	 * This function is used to add subscription intervals.
	 *
	 * @name wcd_sub_product_timespan
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @return   Array  $subscription_interval
	 */
	function wcd_sub_product_timespan() {
		$subscription_interval = array(
			'day' => __( 'Days', 'wcd-subscriptions' ),
			'week' => __( 'Weeks', 'wcd-subscriptions' ),
			'month' => __( 'Months', 'wcd-subscriptions' ),
			'year' => __( 'Years', 'wcd-subscriptions' ),
		);
		return apply_filters( 'wcd_sub_product_timespan', $subscription_interval );
	}
}

if ( ! function_exists( 'wcd_is_this_subscription_product' ) ) {

	/**
	 * This function is to check subscription product.
	 *
	 * @name wcd_sub_product_timespan
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param  int $id id.
	 */
	function wcd_is_this_subscription_product( $id ) {
		$is_subscription = false;
		if ( empty( $id ) ) {
			return $is_subscription;
		} else {
			$subs_exist = get_post_meta( $id, '_wcd_woo_product_is_sub', true );
			if ( 'yes' == $subs_exist ) {
				$is_subscription = true;
			}
		}
		return $is_subscription;
	}
}

if ( ! function_exists( 'wcd_woo_sub_get_the_formated_date' ) ) {

	/**
	 * This function is used to get date format.
	 *
	 * @namewcd_woo_sub_get_the_formated_date
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int $saved_date saved_date.
	 */
	function wcd_woo_sub_get_the_formated_date( $saved_date ) {
		$return_date = 'xxxxxxxxxxx';
		if ( isset( $saved_date ) && ! empty( $saved_date ) ) {

			$date_format = get_option( 'date_format', 'Y-m-d' );
			$time_format = get_option( 'time_format', 'g:i a' );
			$wp_date = date_i18n( $date_format, $saved_date );

			$return_date = $wp_date;
		}

		return $return_date;
	}
}

if ( ! function_exists( 'wcd_payment_check_request' ) ) {
	/**
	 * This function is used to check plugin is enable.
	 *
	 * @name wcd_payment_check_request.
	 * @param Object $wcd_subs wcd_subs.
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	function wcd_payment_check_request( $wcd_subs ) {
		$result = true;
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		$wcd_nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( wp_verify_nonce( $wcd_nonce ) === false ) {
			$result = false;
			wc_add_notice( __( 'There was an error with your request.', 'wcd-subscriptions' ), 'error' );
		} elseif ( empty( $wcd_subs ) ) {
			$result = false;
			wc_add_notice( __( 'Invalid Subscription.', 'wcd-subscriptions' ), 'error' );
		} elseif ( $wcd_subs->get_order_key() !== $order_key ) {
			$result = false;
			wc_add_notice( __( 'Invalid subscription order.', 'wcd-subscriptions' ), 'error' );
		}
		return $result;
	}
}


if ( ! function_exists( 'wcd_woo_subscription_check_subscription' ) ) {
	/**
	 * This function is used to check susbcription post type.
	 *
	 * @name wcd_woo_subscription_check_subscription
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int $wcd_subs_id wcd_subs_id.
	 */
	function wcd_woo_subscription_check_subscription( $wcd_subs_id ) {
		$wcd_is_subs = false;

		if ( isset( $wcd_subs_id ) && ! empty( $wcd_subs_id ) ) {
			if ( 'wcd_subscriptions' == get_post_type( absint( $wcd_subs_id ) ) ) {
				$wcd_is_subs = true;
			}
		}
		return $wcd_is_subs;
	}
}

if ( ! function_exists( 'wcd_is_this_subscription_valid' ) ) {
	/**
	 * This function is used to check susbcription post type.
	 *
	 * @name wcd_is_this_subscription_valid
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int $id id.
	 */
	function wcd_is_this_subscription_valid( $id ) {
		$return = false;

		if ( isset( $id ) && ! empty( $id ) ) {
			if ( 'wcd_subscriptions' == get_post_type( absint( $id ) ) ) {
				$return = true;
			}
		}
		return $return;
	}
}


if ( ! function_exists( 'converted_wordpress_date_format' ) ) {

	/**
	 * This function is used to get date format.
	 *
	 * @name converted_wordpress_date_format
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int $saved_date saved_date.
	 */
	function converted_wordpress_date_format( $saved_date ) {
		$return_date = '---';
		if ( isset( $saved_date ) && ! empty( $saved_date ) ) {

			$date_format = get_option( 'date_format', 'Y-m-d' );
			$wp_date = date_i18n( $date_format, $saved_date );

			$return_date = $wp_date;
		}

		return $return_date;
	}
}


if ( ! function_exists( 'wcd_check_valid_order' ) ) {
	/**
	 * This function is used to check valid order.
	 *
	 * @name wcd_check_valid_order
	 * @param string $order_id order_id.
	 * @since 1.0.2
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	function wcd_check_valid_order( $order_id ) {
		$valid = true;
		if ( empty( $order_id ) ) {
			$valid = false;
		} else {
			$status = get_post_status( $order_id );
			$order = wc_get_order( $order_id );
			if ( 'trash' == $status ) {
				$valid = false;
			} elseif ( ! $order ) {
				$valid = false;
			}
		}

		return $valid;
	}
}

if ( ! function_exists( 'wcd_calculate_time_of_subs_entity' ) ) {

	/**
	 * This function is used to calculate time.
	 *
	 * @name wcd_calculate_time_of_subs_entity
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int    $current_time current_time.
	 * @param int    $interval_number interval_number.
	 * @param string $interval_type interval_type.
	 */
	function wcd_calculate_time_of_subs_entity( $current_time, $interval_number, $interval_type ) {

		$wcd_date = 0;
		switch ( $interval_type ) {
			case 'days':
			case 'day':
				$wcd_date = wcd_get_timestamp( $current_time, intval( $interval_number ) );
				break;
			case 'weeks':
			case 'week':
				$wcd_date = wcd_get_timestamp( $current_time, intval( $interval_number ) * 7 );
				break;
			case 'months':
			case 'month':
				$wcd_date = wcd_get_timestamp( $current_time, 0, intval( $interval_number ) );
				break;
			case 'years':
			case 'year':
				$wcd_date = wcd_get_timestamp( $current_time, 0, 0, intval( $interval_number ) );
				break;
			default:
		}

		return $wcd_date;
	}
}


if ( ! function_exists( 'interval_type' ) ) {

	/**
	 * This function is used to calculate time.
	 *
	 * @name interval_type
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param string $interval_type interval_type.
	 */
	function interval_type( $interval_type ) {
		$interval = '';
		switch ( $interval_type ) {
			case 'day':
				$interval = 'days';
				break;
			case 'week':
				$interval = 'weeks';
				break;
			case 'month':
				$interval = 'months';
				break;
			case 'year':
				$interval = 'years';
				break;
			default:
		}

		return $interval;
	}
}


if ( ! function_exists( 'wcd_get_timestamp' ) ) {
	/**
	 * This function is used to get timestamp.
	 *
	 * @name wcd_get_timestamp
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 * @param int $current_time current_time.
	 * @param int $days days.
	 * @param int $months months.
	 * @param int $years years.
	 */
	function wcd_get_timestamp( $current_time, $days = 0, $months = 0, $years = 0 ) {

		$current_time = strtotime( '+' . $days . ' days', $current_time );
		$current_time = strtotime( '+' . $months . ' month', $current_time );
		$current_time = strtotime( '+' . $years . ' year', $current_time );
		return $current_time;
	}
}

if ( ! function_exists( 'wcd_create_log' ) ) {
	/**
	 * Create log of requests.
	 *
	 * @param  string $message     subscription log message.
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	function wcd_create_log( $message ) {

		if ( 'yes' == get_option( 'wcd_woo_subs_enable_log' ) ) {
			$log_dir = WC_LOG_DIR . 'wcd-subscriptions.log';

			if ( ! is_dir( $log_dir ) ) {

				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged , WordPress.WP.AlternativeFunctions.file_system_read_fopen
				@fopen( WC_LOG_DIR . 'wcd-subscriptions.log', 'a' );

			}
			$log = 'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL .
			'Subscription Entry: ' . $message . PHP_EOL .
			'-----------------------------------' . PHP_EOL;

			//phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			file_put_contents( $log_dir, $log, FILE_APPEND );
		}
	}
}
