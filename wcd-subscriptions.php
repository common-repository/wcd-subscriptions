<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://webcuddle.com/
 * @since             1.0.0
 * @package           Wcd_Subscriptions
 *
 * @wordpress-plugin
 * Plugin Name:       WCD Subscriptions
 * Plugin URI:        https://wordpress.org/plugins/wcd-subscriptions/
 * Description:       WCD Subscriptions allows you to sell subscription-based products or services on your website, and help user to collect recurring revenue
 * Version:           1.0.7
 * Author:            WebCuddle
 * Author URI:        https://webcuddle.com/?utm_source=wcd-official&utm_medium=wcd-subs-org-backend&utm_campaign=official
 * Requires at least:        5.0.0
 * Tested up to:             6.3.1
 * WC requires at least:     5.0.0
 * WC tested up to:          8.0.3
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wcd-subscriptions
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$active_plugins = get_option( 'active_plugins', array() );
$woo_path = 'woocommerce/woocommerce.php';
if ( in_array( $woo_path, $active_plugins, true ) ) {
	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Plugin version.
	 */
	if ( ! defined( 'WCD_SUBSCRIPTIONS_VERSION' ) ) {
		define( 'WCD_SUBSCRIPTIONS_VERSION', '1.0.7' );
	}

	/**
	 * Currently plugin directory path.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Plugin directory path.
	 */
	if ( ! defined( 'WCD_SUBSCRIPTIONS_DIR_PATH' ) ) {
		define( 'WCD_SUBSCRIPTIONS_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Currently plugin directory url.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Plugin directory url.
	 */
	if ( ! defined( 'WCD_SUBSCRIPTIONS_DIR_URL' ) ) {
		define( 'WCD_SUBSCRIPTIONS_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Currently plugin directory url.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Plugin directory url.
	 */
	if ( ! defined( 'WCD_WOO_SUBS_ABSPATH' ) ) {
		define( 'WCD_WOO_SUBS_ABSPATH', dirname( __FILE__ ) . '/' );
	}

	add_action( 'before_woocommerce_init', function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} );

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-wcd-subscriptions-activator.php
	 */
	function activate_wcd_subscriptions() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcd-subscriptions-activator.php';
		Wcd_Subscriptions_Activator::activate();
		$active_plugins_of_wcd = get_option( 'active_plugins_of_wcd', false );
		if ( is_array( $active_plugins_of_wcd ) && ! empty( $active_plugins_of_wcd ) ) {
			$active_plugins_of_wcd['wcd-subscriptions'] = array(
				'plugin_name' => __( 'WCD Subscriptions', 'wcd-subscriptions' ),
				'active' => '1',
			);
		} else {
			$active_plugins_of_wcd = array();
			$active_plugins_of_wcd['wcd-subscriptions'] = array(
				'plugin_name' => __( 'WCD Subscriptions', 'wcd-subscriptions' ),
				'active' => '1',
			);
		}
		update_option( 'active_plugins_of_wcd', $active_plugins_of_wcd );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-wcd-subscriptions-deactivator.php
	 */
	function deactivate_wcd_subscriptions() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wcd-subscriptions-deactivator.php';
		Wcd_Subscriptions_Deactivator::deactivate();
		$active_plugins_of_wcd = get_option( 'active_plugins_of_wcd', false );
		if ( is_array( $active_plugins_of_wcd ) && ! empty( $active_plugins_of_wcd ) ) {
			foreach ( $active_plugins_of_wcd as $wcd_deactivate_plugin => $wcd_deactivate ) {
				if ( 'wcd-subscriptions' === $wcd_deactivate_plugin ) {
					$active_plugins_of_wcd[ $wcd_deactivate_plugin ]['active'] = '0';
				}
			}
		}
		update_option( 'active_plugins_of_wcd', $active_plugins_of_wcd );
	}

	register_activation_hook( __FILE__, 'activate_wcd_subscriptions' );
	register_deactivation_hook( __FILE__, 'deactivate_wcd_subscriptions' );

	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'add_setting_link' );

	/**
	 * Settings link.
	 *
	 * @since    1.0.0
	 * @param   Array $links    Settings link array.
	 */
	function add_setting_link( $links ) {

		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=wcd_woo_subs_page' ) . '">' . __( 'Settings', 'wcd-subscriptions' ) . '</a>',
		);
		return array_merge( $my_link, $links );
	}

	add_action( 'activated_plugin', 'wcd_woo_subs_activation_redirect' );
	/**
	 * Redirect after activation
	 *
	 * @since 1.0.0
	 * @param string $plugin path of plugin file.
	 */
	function wcd_woo_subs_activation_redirect( $plugin ) {

		if ( 'wcd-subscriptions/wcd-subscriptions.php' === $plugin ) {
			echo esc_html( $plugin );
			wp_safe_redirect( esc_url( admin_url( 'admin.php?page=wcd_woo_subs_page' ) ) );
			exit();
		}
	}

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-wcd-subscriptions.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_wcd_subscriptions() {

		$plugin = new Wcd_Subscriptions();
		$plugin->run();
		$GLOBALS['wcd_woo_subs'] = $plugin;

	}
	run_wcd_subscriptions();

	/*
	 * This action hook registers our PHP class as a WooCommerce payment gateway
 	*/
	add_filter( 'woocommerce_payment_gateways', 'wcd_add_gateway_class' );
	// @credit taken refernce from "wpswings" plugin
	function wcd_add_gateway_class( $gateways ) {
	$gateways[] = 'WCD_Paypal_Gateway_Integration'; // your class name is here.
	return $gateways;
	}

} else {
	deactivate_plugins( plugin_basename( __FILE__ ) );

	add_action( 'admin_notices', 'wcd_add_notice_when_wocommerce_not_activated' );

	/**
	 * Add the Notice For WooCommerce.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function wcd_add_notice_when_wocommerce_not_activated() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Please activate WooCommerce first.', 'wcd-subscriptions' ); ?></p>
		</div>
		<?php
	}
}
