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
class Wcd_Subscriptions {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wcd_Subscriptions_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WCD_SUBSCRIPTIONS_VERSION' ) ) {
			$this->version = WCD_SUBSCRIPTIONS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wcd-subscriptions';

		$this->load_dependencies();
		$this->set_locale();
		$this->init();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wcd_Subscriptions_Loader. Orchestrates the hooks of the plugin.
	 * - Wcd_Subscriptions_I18n. Defines internationalization functionality.
	 * - Wcd_Subscriptions_Admin. Defines all hooks for the admin area.
	 * - Wcd_Subscriptions_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wcd-subscriptions-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wcd-subscriptions-public.php';

		/**
		 * The class responsible for defining all actions that occur in the global callback function.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wcd-subscriptions-global-callback-function.php';

		/**
		 * The class responsible for defining all actions that occur in the global callback function.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-order-statuses.php';

		/**
		 * The class responsible for defining all actions that occur in the global callback function.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-product-edit-page.php';

		/**
		 * The class responsible for defining all actions that occur in the global callback function.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-cart-page.php';

		/**
		 * The class responsible for defining all actions that occur in the global callback function.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-checkout-page.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-creation.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-renewals.php';

		/**
		 * The class responsible for defining all actions that occur in the payment gateway function.
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wcd-payment-gateways/class-wcd-woo-all-payment-gateways.php';

		$this->loader = new Wcd_Subscriptions_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wcd_Subscriptions_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wcd_Subscriptions_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * The function is used to include email class.
	 */
	public function init() {
		add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wcd_Subscriptions_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add WCD menu and submenu.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_main_menu' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'remove_default_submenu', 50 );

		$this->loader->add_action( 'wp_ajax_subs_prod_creation_when_activated', $plugin_admin, 'subs_prod_creation_when_activated' );
		$this->loader->add_action( 'wp_ajax_nopriv_subs_prod_creation_when_activated', $plugin_admin, 'subs_prod_creation_when_activated' );

		$this->loader->add_action( 'init', $plugin_admin, 'register_post_type_of_subs_order' );

		$this->loader->add_action( 'init', $plugin_admin, 'subs_renewal_schedulers' );

		$this->loader->add_action( 'init', $plugin_admin, 'cancel_subs_for_admin' );

		$this->loader->add_action( 'wp_ajax_subs_payment_creation_when_activated', $plugin_admin, 'subs_payment_creation_when_activated' );
		$this->loader->add_action( 'wp_ajax_nopriv_subs_payment_creation_when_activated', $plugin_admin, 'subs_payment_creation_when_activated' );

		$this->loader->add_action( 'wp_ajax_subs_key_validation_for_paypal', $plugin_admin, 'subs_key_validation_for_paypal' );
		$this->loader->add_action( 'wp_ajax_nopriv_subs_key_validation_for_paypal', $plugin_admin, 'subs_key_validation_for_paypal' );

		if ( wcd_sub_plugin_is_enable() ) {
			$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'save_custom_product_fields_data_for_wcd_subs_tab', 10, 2 );

			$this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'subs_custom_prod_fields_for_settings', 10 );

		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wcd_Subscriptions_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_review_order_after_order_total', $plugin_public, 'show_subscription_price', 10, 1 );
		$this->loader->add_action( 'woocommerce_checkout_order_processed', $plugin_public, 'process_order_and_create_subscription', 99, 2 );
		$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_public, 'change_subs_status_and_save_subscription_meta', 99, 3 );

		$this->loader->add_action( 'woocommerce_account_wcd-woo-sub-customer-details_endpoint', $plugin_public, 'wcd_woocommerce_show_subscription_details', 10, 1 );
		$this->loader->add_action( 'init', $plugin_public, 'bbloomer_add_premium_support_endpoint', 10, );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'bbloomer_premium_support_query_vars', 10, 1 );
		$this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public, 'bbloomer_add_premium_support_link_my_account', 10, 1 );
		$this->loader->add_action( 'woocommerce_account_wcd-woo-sub-customer-listing_endpoint', $plugin_public, 'bbloomer_premium_support_content', 10, 1 );
		$this->loader->add_action( 'after_woocommerce_pay', $plugin_public, 'wcd_woo_subscription_after_woocommerce_pay', 10 );
		$this->loader->add_action( 'init', $plugin_public, 'wcd_woo_on_cancel_susbcription' );
		$this->loader->add_action( 'wp_loaded', $plugin_public, 'wcd_woo_lets_change_payment_method_form', 20 );
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'add_fees_and_signup_fee', 10, 1 );
	}

	/**
	 * The function include email class.
	 *
	 * @name add_email_classes.
	 * @since 1.0.0
	 * @param Array $emails emails.
	 */
	public function add_email_classes( $emails ) {
		$emails['wcd_subscription_cancel'] = require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-cancel-email.php';
		$emails['wcd_subscription_expire'] = require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wcd-subscriptions-expire-email.php';

		return apply_filters( 'wcd_email_classes', $emails );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wcd_Subscriptions_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Predefined default wcd tabs.
	 *
	 * @return  Array       An key=>value pair of wcd tabs.
	 */
	public function wcd_subs_woo_default_tabs() {

		$default_tabs = array();

		$default_tabs['wcd-woo-subs-overview'] = array(
			'name'       => __( 'Overview', 'wcd-subscriptions' ),
			'icon'       => 'fa fa-life-ring',
		);

		$default_tabs['wcd-woo-subs-settings'] = array(
			'name'       => __( 'Settings', 'wcd-subscriptions' ),
			'icon'       => 'fas fa-link',
		);

		$default_tabs['wcd-woo-subs-text-modification'] = array(
			'name'       => __( 'Text modifications', 'wcd-subscriptions' ),
			'icon'       => 'fa fa-list',
		);

		$default_tabs['class-wcd-woo-subs-subscription-table'] = array(
			'name'       => __( 'Subscription Table', 'wcd-subscriptions' ),
			'icon'       => 'fas fa-chart-pie',
		);

		$default_tabs = apply_filters( 'wcd_add_new_tab', $default_tabs );

		$default_tabs['wcd-woo-subs-system-status'] = array(
			'name'       => __( 'System status', 'wcd-subscriptions' ),
			'icon'       => 'fas fa-chart-pie',
		);

		return $default_tabs;
	}


	/**
	 * Locate and load appropriate temlpate.
	 *
	 * @since   1.0.0
	 * @param string $path This is path of template.
	 * @param array  $params params contain the array.
	 */
	public function load_template_view( $path, $params = array() ) {

		$file_path = WCD_WOO_SUBS_ABSPATH . $path;

		if ( file_exists( $file_path ) ) {

			include $file_path;

		} else {

			/* translators: %s: file path */
			$notice = sprintf( __( 'Unable to locate file path at location "%s". Some features may not work properly in WCD Subscriptions, please contact us!', 'wcd-subscriptions' ), $file_path );

			$this->wcd_woo_subs_notice( $notice, 'error' );
		}
	}

	/**
	 * Locate and load appropriate pro template.
	 *
	 * @since   1.0.0
	 * @param string $path This is path of template.
	 * @param array  $params params contain the array.
	 */
	public function load_pro_template_view( $path, $params = array() ) {

		$file_path = WCD_WOO_SUBS_PRO_ABSPATH . $path;

		if ( file_exists( $file_path ) ) {

			include $file_path;

		} else {

			/* translators: %s: file path */
			$notice = sprintf( __( 'Unable to locate file path at location "%s". Some features may not work properly in WCD Subscriptions, please contact us!', 'wcd-subscriptions' ), $file_path );

			$this->wcd_woo_subs_notice( $notice, 'error' );
		}
	}

	/**
	 * Show admin screen id.
	 *
	 * @since  1.0.0.
	 */
	public function get_screen_ids() {
		$arr = array(
			'webcuddle_page_wcd_woo_subs_page',
		);
		return $arr;
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0.
	 */
	public static function wcd_woo_subs_notice( $message, $type = 'error' ) {

		$classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$classes .= 'updated';
				break;

			case 'update-nag':
				$classes .= 'update-nag';
				break;
			case 'success':
				$classes .= 'notice-success is-dismissible';
				break;

			default:
				$classes .= 'error';
		}
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
		<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}

}
