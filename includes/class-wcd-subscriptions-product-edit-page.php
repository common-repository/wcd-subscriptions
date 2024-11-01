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
class Wcd_Subscriptions_Product_Edit_Page {

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

			add_action( 'product_type_options', array( $this, 'wcd_subcription_product_type_created' ), 10, 1 );

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'wcd_subcription_custom_product_tab_callback' ), 10, 1 );

		}

	}

	/**
	 * This function is used Subscription type checkobox for simple products
	 *
	 * @name wcd_subcription_product_type_created
	 * @since    1.0.0
	 * @param    Array $products_type Products type.
	 * @return   Array  $products_type.
	 */
	public function wcd_subcription_product_type_created( $products_type ) {
		$products_type['wcd_woo_product_is_sub'] = array(
			'id'            => '_wcd_woo_product_is_sub',
			'wrapper_class' => 'show_if_simple show_if_variable',
			'label'         => __( 'WCD Subscription', 'wcd-subscriptions' ),
			'description'   => __( 'This is the Subscriptions type product.', 'wcd-subscriptions' ),
			'default'       => 'no',
		);
		return $products_type;

	}

	/**
	 * This function is used to add WCD subscription tab for product.
	 *
	 * @name wcd_subcription_custom_product_tab_callback
	 * @since    1.0.0
	 * @param    Array $tabs Products tabs array.
	 * @return   Array  $tabs
	 */
	public function wcd_subcription_custom_product_tab_callback( $tabs ) {
		$tabs['wcd_woo_product_is_sub'] = array(
			'label'    => __( 'WCD Subscription', 'wcd-subscriptions' ),
			'target'   => 'wcd_woo_product_is_sub_target_section',
			// Add class for product.
			'class'    => apply_filters( 'wcd_subscription_setting_tabs_class', array() ),
			'priority' => 70,
		);
		// Add tb for product.
		return apply_filters( 'wcd_subscription_extend_settings_tabs', $tabs );

	}

}

$object_of_this_class = new Wcd_Subscriptions_Product_Edit_Page();
$object_of_this_class::get_instance();
