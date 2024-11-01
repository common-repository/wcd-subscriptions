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
class Wcd_Subscriptions_Order_Statuses {

	/**
	 * The instance of class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      object    $instance    The instance of class.
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
			add_filter( 'woocommerce_register_shop_order_post_statuses', array( $this, 'wcd_add_new_order_status' ) );

			add_filter( 'wc_order_statuses', array( $this, 'wcd_list_new_added_status' ) );
		}

	}

	/**
	 * Add new order status.
	 *
	 * @param  array $order_status    Order Status .
	 * @since  1.0.0.
	 */
	public function wcd_add_new_order_status( $order_status ) {

		$order_status['wc-wcd_recurring'] = array(
			'label'                     => _x( 'Wcd Recurring', 'Order status', 'wcd-subscriptions' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'Wcd Recurring <span class="count">(%s)</span>', 'Wcd Recurring <span class="count">(%s)</span>', 'wcd-subscriptions' ),
		);
		return $order_status;
	}

	/**
	 * List new order status.
	 *
	 * @param  array $order_statuses    Order Statuses .
	 * @since  1.0.0.
	 */
	public function wcd_list_new_added_status( $order_statuses ) {
		$order_statuses['wc-wcd_recurring'] = _x( 'Wcd Recurring', 'Order status', 'wcd-subscriptions' );

		return $order_statuses;
	}

}

$object_of_this_class = new Wcd_Subscriptions_Order_Statuses();
$object_of_this_class::get_instance();
