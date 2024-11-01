<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wcd_Subscriptions
 * @subpackage Wcd_Subscriptions/admin
 * @author     WebCuddle <support@webcuddle.com>
 */
class Wcd_Subscriptions_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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
		global $wcd_woo_subs;
		$screen = get_current_screen();
		$all_wcd_screens = $wcd_woo_subs->get_screen_ids();
		foreach ( $all_wcd_screens as $key => $value ) {
			if ( isset( $screen->id ) && $value === $screen->id ) {
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wcd-subscriptions-admin.css', array(), $this->version, 'all' );
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wcd-subscriptions-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name . 'wcd-woo-product-edit', plugin_dir_url( __FILE__ ) . 'js/wcd-woo-sub-product-edit-page.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name . 'wcd-admin-js', plugin_dir_url( __FILE__ ) . 'js/wcd-subscriptions-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name . 'wcd-admin-js',
			'wcd_admin_param',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'wcd_nonce_verification'    => wp_create_nonce( 'wcd_admin_nonce_subscription' ),
				'demo_subscription_product_created' => get_option( 'wcd_demo_subscripiton_product_created', 'no' ),
			)
		);
	}


	/**
	 * Adding main menu for plugin.
	 *
	 * @since    1.0.0
	 */
	public function add_main_menu() {
		// global $submenu.
		add_menu_page( 'WebCuddle', 'WebCuddle', 'manage_options', 'plugins-of-wcd', '', WCD_SUBSCRIPTIONS_DIR_URL . 'admin/images/webcuddle_logo.png', 15 );

		$menus = array(
			'name'            => __( 'WCD Subscriptions', 'wcd-subscriptions' ),
			'slug'            => 'wcd_woo_subs_page',
			'menu_link'       => 'wcd_woo_subs_page',
			'instance'        => $this,
			'function'        => 'plugin_page_render',
		);
		add_submenu_page( 'plugins-of-wcd', $menus['name'], $menus['name'], 'manage_options', $menus['menu_link'], array( $menus['instance'], $menus['function'] ) );
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 */
	public function remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'plugins-of-wcd', $submenu ) ) {
			if ( isset( $submenu['plugins-of-wcd'][0] ) ) {
				unset( $submenu['plugins-of-wcd'][0] );
			}
		}
	}

	/**
	 * Subscriptions For Woocommerce admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function plugin_page_render() {

		include_once WCD_SUBSCRIPTIONS_DIR_PATH . 'admin/partials/templates/wcd-subscriptions-main-template.php';
	}

	/**
	 * Subscriptions For Woocommerce subscription creation on activation.
	 *
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function subs_prod_creation_when_activated() {
		$wcd_nonce_verification = ! empty( $_POST['wcd_nonce_verification'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_nonce_verification'] ) ) : '';

		// check_ajax_referer( 'ajax-nonce', 'nonce' ).
		if ( isset( $wcd_nonce_verification ) ) {
			if ( wp_verify_nonce( $wcd_nonce_verification, 'pippin-sample-nonce' ) ) {

				$product_name = ! empty( $_POST['wcd_product_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_product_name'] ) ) : 'Test Subscription';
				$wcd_product_description = ! empty( $_POST['wcd_product_description'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_product_description'] ) ) : 'This is Subscription';
				$wcd_product_price = ! empty( $_POST['wcd_product_price'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_product_price'] ) ) : '';
				$wcd_subscription_interval = ! empty( $_POST['wcd_subscription_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_subscription_interval'] ) ) : '';
				$wcd_subscription_period = ! empty( $_POST['wcd_subscription_period'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_subscription_period'] ) ) : '';
				$wcd_subscription_demo_product_created = get_option( 'wcd_demo_subscripiton_product_created', 'no' );

				// To Create the Subscrption product on activation.
				if ( 'yes' !== $wcd_subscription_demo_product_created ) {
					$post_id = wp_insert_post(
						array(
							'post_title' => $product_name,
							'post_type' => 'product',
							'post_content' => $wcd_product_description,
							'post_status' => 'publish',
						)
					);

					wp_set_object_terms( $post_id, 'simple', 'product_type' );
					update_post_meta( $post_id, '_visibility', 'visible' );
					update_post_meta( $post_id, '_stock_status', 'instock' );

					update_post_meta( $post_id, '_wcd_woo_product_is_sub', 'yes' );
					update_post_meta( $post_id, 'wcd_wc_subscription_number', '1' );
					update_post_meta( $post_id, 'wcd_woo_sub_product_total_period_number', $wcd_subscription_interval );
					update_post_meta( $post_id, 'wcd_woo_sub_product_total_period_type', $wcd_subscription_period );

					update_post_meta( $post_id, '_regular_price', $wcd_product_price );
					update_post_meta( $post_id, '_sale_price', '' );
					update_post_meta( $post_id, '_price', $wcd_product_price );
					$product = wc_get_product( $post_id );

					$product->save();
					update_option( 'wcd_demo_subscripiton_product_created', 'yes' );
					$response = 'Subscription Product Created.';
				} else {
					$response = 'Subscription Product Already Created.';
				}
			}
		}
		echo json_encode( $response );
		wp_die();

	}


	/**
	 * Subscriptions For Woocommerce Payment Install on activation.
	 *
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function subs_payment_creation_when_activated() {
		$response = '';
		$wcd_plugin_name = ! empty( $_POST['wcd_payment_gateway'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_payment_gateway'] ) ) : '';
		$wcd_nonce_check = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! empty( $wcd_plugin_name ) && wp_verify_nonce( $wcd_nonce_check, 'wcd_admin_nonce_subscription' ) ) {
			$wcd_plugin_file_path = $wcd_plugin_name . '/' . $wcd_plugin_name . '.php';

			if ( file_exists( WP_PLUGIN_DIR . '/' . $wcd_plugin_file_path ) && ! is_plugin_active( $wcd_plugin_file_path ) ) {
				activate_plugin( $wcd_plugin_file_path );
				$response = true;
			} else {

				include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

				$wcd_plugin_api    = plugins_api(
					'plugin_information',
					array(
						'slug' => $wcd_plugin_name,
						'fields' => array( 'sections' => false ),
					)
				);
				if ( isset( $wcd_plugin_api->download_link ) ) {
					$wcd_ajax_obj = new WP_Ajax_Upgrader_Skin();
					$wcd_obj = new Plugin_Upgrader( $wcd_ajax_obj );
					$wcd_install = $wcd_obj->install( $wcd_plugin_api->download_link );
					activate_plugin( $wcd_plugin_file_path );
					$response = true;
				}
			}
		}
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0.
	 */
	public static function wcd_woo_notice( $message, $type = 'error' ) {

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

	/**
	 * Register subscription post type.
	 *
	 * @since  1.0.0.
	 */
	public function register_post_type_of_subs_order() {

		$labels = array(
			'name'               => __( 'Subscriptions', 'wcd-subscriptions' ),
			'singular_name'      => __( 'Subscription', 'wcd-subscriptions' ),
			'add_new'            => __( 'Add Subscription', 'wcd-subscriptions' ),
			'add_new_item'       => __( 'Add New Subscription', 'wcd-subscriptions' ),
			'edit'               => __( 'Edit', 'wcd-subscriptions' ),
			'edit_item'          => __( 'Edit Subscription', 'wcd-subscriptions' ),
			'new_item'           => __( 'New Subscription', 'wcd-subscriptions' ),
			'view'               => __( 'View Subscription', 'wcd-subscriptions' ),
			'view_item'          => __( 'View Subscription', 'wcd-subscriptions' ),
			'search_items'       => __( 'Search Subscriptions', 'wcd-subscriptions' ),
			'not_found'          => __( 'Not Found', 'wcd-subscriptions' ),
			'not_found_in_trash' => __( 'No Subscriptions found in the trash', 'wcd-subscriptions' ),
			'parent'             => __( 'Parent Subscriptions', 'wcd-subscriptions' ),
			'menu_name'          => __( 'Subscriptions', 'wcd-subscriptions' ),
		);

		$args = array(
			'label'               => esc_html__( 'wcd_subscription', 'yith-woocommerce-subscription' ),
			'labels'              => $labels,
			'description'                      => __( 'These subscriptions are stored.', 'wcd-subscriptions' ),
			'public'                           => true,
			'show_ui'                          => true,
			'capability_type'                  => 'shop_order',
			'map_meta_cap'                     => true,
			'publicly_queryable'               => false,
			'exclude_from_search'              => true,
			'show_in_menu'                     => false,
			'hierarchical'                     => false,
			'show_in_nav_menus'                => false,
			'rewrite'                          => false,
			'query_var'                        => false,
			'supports'                         => array( 'title', 'comments', 'custom-fields' ),
			'has_archive'                      => false,
			'exclude_from_orders_screen'       => true,
			'add_order_meta_boxes'             => true,
			'exclude_from_order_count'         => true,
			'exclude_from_order_views'         => true,
			'exclude_from_order_webhooks'      => true,
			'exclude_from_order_reports'       => true,
			'exclude_from_order_sales_reports' => true,
		);

		wc_register_order_type(
			'wcd_subscriptions',
			$args
		);

	}

	/**
	 * Subscription renewal schedulers.
	 *
	 * @since  1.0.0.
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function subs_renewal_schedulers() {
		if ( class_exists( 'ActionScheduler' ) ) {
			if ( function_exists( 'as_next_scheduled_action' ) && false === as_next_scheduled_action( 'renew_subscription' ) ) {
				as_schedule_recurring_action( strtotime( 'hourly' ), 3600, 'renew_subscription' );
			}
			if ( function_exists( 'as_next_scheduled_action' ) && false === as_next_scheduled_action( 'do_subscriptions_expired' ) ) {
				as_schedule_recurring_action( strtotime( 'hourly' ), 3600, 'do_subscriptions_expired' );
			}

			do_action( 'wcd_create_admin_scheduler' );
		}
	}

	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name cancel_subs_for_admin
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function cancel_subs_for_admin() {

		if ( isset( $_GET['wcd_woo_subscription_status_admin'] ) && isset( $_GET['wcd_woo_subscription_id'] ) && isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			$wcd_status   = sanitize_text_field( wp_unslash( $_GET['wcd_woo_subscription_status_admin'] ) );
			$wcd_subscription_id = sanitize_text_field( wp_unslash( $_GET['wcd_woo_subscription_id'] ) );
			if ( wcd_woo_subscription_check_subscription( $wcd_subscription_id ) ) {
				// Cancel subscription.
				update_post_meta( $wcd_subscription_id, 'wcd_subscription_status', 'cancelled' );// change the key.
				$redirect_url = admin_url() . 'admin.php?page=wcd_woo_subs_page&wcd_tab=class-wcd-woo-subs-subscription-table';
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}
	}

	/**
	 * This function is used to add custom fileds for WCD subscription tab.
	 *
	 * @name subs_custom_prod_fields_for_settings
	 * @since    1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function subs_custom_prod_fields_for_settings() {

		global $post;
		$post_id = $post->ID;
		$product = wc_get_product( $post_id );

		$wcd_subscription_number = get_post_meta( $post_id, 'wcd_woo_sub_product_total_period_number', true );
		if ( empty( $wcd_subscription_number ) ) {
			$wcd_subscription_number = 1;
		}
		$wcd_subscription_interval = get_post_meta( $post_id, 'wcd_woo_sub_product_total_period_type', true );
		if ( empty( $wcd_subscription_interval ) ) {
			$wcd_subscription_interval = 'day';
		}

		$wcd_subscription_expiry_number = get_post_meta( $post_id, 'wcd_woo_sub_product_expire_number', true );
		$wcd_subscription_expiry_interval = get_post_meta( $post_id, 'wcd_woo_sub_product_expire_interval', true );
		$wcd_subscription_initial_signup_price = get_post_meta( $post_id, 'wcd_sub_initial_product_signup_fee', true );
		$wcd_subscription_free_trial_number = get_post_meta( $post_id, 'wcd_woo_sub_product_trial_number', true );
		$wcd_subscription_free_trial_interval = get_post_meta( $post_id, 'wcd_woo_sub_product_trial_number_interval', true );
		?>
		<div id="wcd_woo_product_is_sub_target_section" class="panel woocommerce_options_panel ">
			
		<p class="form-field ">
			<label for="wcd_subscription_number">
			<?php esc_html_e( 'Subscription Time Span', 'wcd-subscriptions' ); ?>
			</label>
			<input type="number" class="short wc_input_number"  min="1" required name="wcd_woo_sub_product_total_period_number" id="wcd_woo_sub_product_total_period_number" value="<?php echo esc_attr( $wcd_subscription_number ); ?>" placeholder="<?php esc_html_e( 'Enter subscription interval', 'wcd-subscriptions' ); ?>"> 
		
			<select id="wcd_woo_sub_product_total_period_type" name="wcd_woo_sub_product_total_period_type" class="wcd_woo_sub_interval" >
				<?php foreach ( wcd_sub_product_timespan() as $value => $label ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $wcd_subscription_interval, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
		 <?php
			$description_text = __( 'Choose the subscriptions time interval for the product "for example 10 days"', 'wcd-subscriptions' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>
	
		
		<p class="form-field ">
		<label for="wcd_subscription_number">
			<?php esc_html_e( 'Subscription Will Ends', 'wcd-subscriptions' ); ?>
			</label>
			  <input type="radio" name="check_clr"  class='wcd_woo_expiry_active' value="0" 
			  <?php
				if ( get_post_meta( $post_id, 'wcd_woo_radio_interval', true ) == '0' ) {
					echo 'checked="checked"';}
				?>
				> Infinite </label>

			<input type="radio" name="check_clr" class='wcd_woo_expiry_active' value="1" 
			<?php
			if ( get_post_meta( $post_id, 'wcd_woo_radio_interval', true ) == '1' ) {
				echo 'checked="checked"';}
			?>
			>Set an Interval</label>
		</p>

		<p class="form-field wcd_sub_expiry" style="
		<?php
		if ( 0 == get_post_meta( $post_id, 'wcd_woo_radio_interval', true ) ) {
			echo 'display:none;'; }
		?>
		">
			<label for="wcd_subscription_expiry_number">
			<?php esc_html_e( 'Subscriptions Expiry Within', 'wcd-subscriptions' ); ?>
			</label>
			<input type="number" class="short wc_input_number"  min="1" name="wcd_woo_sub_product_expire_number" id="wcd_woo_sub_product_expire_number" value="<?php echo esc_attr( $wcd_subscription_expiry_number ); ?>" placeholder="<?php esc_html_e( 'Enter subscription expiry', 'wcd-subscriptions' ); ?>"> 
		
			<select id="wcd_woo_sub_product_expire_interval" name="wcd_woo_sub_product_expire_interval" class="wcd_woo_sub_product_expire_interval" >
				<?php foreach ( wcd_sub_product_timespan( $wcd_subscription_interval ) as $value => $label ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $wcd_subscription_expiry_interval, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
		 <?php
			$description_text = __( 'Subscription expiry time "leave empty for unlimited"', 'wcd-subscriptions' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>

		<p class="form-field">
			<label for="wcd_subscription_free_trial_number">
			<?php esc_html_e( 'Subscription Free trial For', 'wcd-subscriptions' ); ?>
			</label>
			<input type="number" class="short wc_input_number" name="wcd_woo_sub_product_trial_number" id="wcd_woo_sub_product_trial_number" value="<?php echo esc_attr( $wcd_subscription_free_trial_number ); ?>" placeholder="<?php esc_html_e( 'Enter free trial interval', 'wcd-subscriptions' ); ?>"> 
			
			
			<select id="wcd_sub_product_trial_number" name="wcd_woo_sub_product_trial_number_interval" class="wcd_sub_product_trial_number" >
				<?php foreach ( wcd_sub_product_timespan() as $value => $label ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $wcd_subscription_free_trial_interval, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
		 <?php
			$description_text = __( 'Free trial period for product, "leave empty for no trial period"', 'wcd-subscriptions' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>


		<p class="form-field">
			<label for="wcd_subscription_initial_signup_price">
			<?php
			esc_html_e( 'Initial Signup Charges', 'wcd-subscriptions' );
			echo esc_html( '(' . get_woocommerce_currency_symbol() . ')' );
			?>
			</label>
			<input type="number" class="short wc_input_price"  min="1" step="any" name="wcd_sub_initial_product_signup_fee" id="wcd_sub_initial_product_signup_fee" value="<?php echo esc_attr( $wcd_subscription_initial_signup_price ); ?>" placeholder="<?php esc_html_e( 'Enter signup fee', 'wcd-subscriptions' ); ?>"> 
			
		 <?php
			$description_text = __( 'Subscriptions Charges , "leave empty for no charge"', 'wcd-subscriptions' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>

		<?php
			wp_nonce_field( 'wcd_woo_product_edit_nonce', 'wcd_woo_nonce_verify' );
			do_action( 'wcd_product_edit_page_extra_fields', $post_id );
		?>
		</div>
		<?php
	}

	/**
	 * This function is used to save custom fields for subscription products.
	 *
	 * @name save_custom_product_fields_data_for_wcd_subs_tab
	 * @since    1.0.0
	 * @param    int    $post_id Post ID.
	 * @param    object $post post.
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function save_custom_product_fields_data_for_wcd_subs_tab( $post_id, $post ) {

		if ( ! isset( $_POST['wcd_woo_nonce_verify'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcd_woo_nonce_verify'] ) ), 'wcd_woo_product_edit_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		$wcd_product = isset( $_POST['_wcd_woo_product_is_sub'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wcd_woo_product_is_sub', $wcd_product );
		if ( isset( $_POST['_wcd_woo_product_is_sub'] ) && ! empty( $_POST['_wcd_woo_product_is_sub'] ) ) {
			$wcd_woo_sub_subscription_number = isset( $_POST['wcd_woo_sub_product_total_period_number'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_woo_sub_product_total_period_number'] ) ) : '';

			$wcd_woo_sub_subscription_interval = isset( $_POST['wcd_woo_sub_product_total_period_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_woo_sub_product_total_period_type'] ) ) : '';
			$wcd_woo_sub_subscription_expiry_number = isset( $_POST['wcd_woo_sub_product_expire_number'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_woo_sub_product_expire_number'] ) ) : '';
			$wcd_woo_sub_subscription_expiry_interval = isset( $_POST['wcd_woo_sub_product_expire_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_woo_sub_product_expire_interval'] ) ) : '';
			$wcd_woo_sub_subscription_initial_signup_price = isset( $_POST['wcd_sub_initial_product_signup_fee'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_sub_initial_product_signup_fee'] ) ) : '';
			$wcd_woo_sub_subscription_free_trial_number = isset( $_POST['wcd_woo_sub_product_trial_number'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_woo_sub_product_trial_number'] ) ) : '';
			$wcd_woo_sub_subscription_free_trial_interval = isset( $_POST['wcd_woo_sub_product_trial_number_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_woo_sub_product_trial_number_interval'] ) ) : '';
			$wcd_woo_sub_subscription_check_product_exp_or_not = isset( $_POST['check_clr'] ) ? sanitize_text_field( wp_unslash( $_POST['check_clr'] ) ) : '';
			$wcd_onetime_purchase = isset( $_POST['wcd_onetime_purchase'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_onetime_purchase'] ) ) : '';
			$wcd_onetime_price = isset( $_POST['wcd_onetime_price'] ) ? sanitize_text_field( wp_unslash( $_POST['wcd_onetime_price'] ) ) : '';

			if ( 0 == $wcd_woo_sub_subscription_check_product_exp_or_not ) {
				$wcd_woo_sub_subscription_expiry_number = 1000;
			}
			update_post_meta( $post_id, 'wcd_woo_sub_product_total_period_number', $wcd_woo_sub_subscription_number );
			update_post_meta( $post_id, 'wcd_woo_sub_product_total_period_type', $wcd_woo_sub_subscription_interval );
			update_post_meta( $post_id, 'wcd_woo_sub_product_expire_number', $wcd_woo_sub_subscription_expiry_number );
			update_post_meta( $post_id, 'wcd_woo_sub_product_expire_interval', $wcd_woo_sub_subscription_expiry_interval );
			update_post_meta( $post_id, 'wcd_sub_initial_product_signup_fee', $wcd_woo_sub_subscription_initial_signup_price );
			update_post_meta( $post_id, 'wcd_woo_sub_product_trial_number', $wcd_woo_sub_subscription_free_trial_number );
			update_post_meta( $post_id, 'wcd_woo_sub_product_trial_number_interval', $wcd_woo_sub_subscription_free_trial_interval );
			update_post_meta( $post_id, 'wcd_woo_radio_interval', $wcd_woo_sub_subscription_check_product_exp_or_not );
			update_post_meta( $post_id, 'wcd_onetime_purchase', $wcd_onetime_purchase );
			update_post_meta( $post_id, 'wcd_onetime_price', $wcd_onetime_price );

		}
	}

	/**
	 * subs_key_validation_for_paypal function
	 *
	 * @return void
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function subs_key_validation_for_paypal() { 

		$test_mode = isset( $_POST['testMode'] ) ? sanitize_text_field( wp_unslash( $_POST['testMode'] ) ) : '';
		$client_id = isset( $_POST['clientID'] ) ? sanitize_text_field( wp_unslash( $_POST['clientID'] ) ) : '';
		$client_secret = isset( $_POST['clientSecret'] ) ? sanitize_text_field( wp_unslash( $_POST['clientSecret'] ) ) : '';

		$endpoint = ( 'true' === $test_mode ) ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

		$response = wp_remote_post(
			$endpoint . '/v1/oauth2/token',
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(
					'Accept' => 'application/json',
					'Accept-Language' => 'en_US',
					'Authorization'   => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
				),
				'body' => array(
					'grant_type' => 'client_credentials',
				),
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( 200 == $response_code ) {
			$response = array(
				'msg' => esc_html__( 'Verification Successful', 'wcd-subscriptions' ),
				'status' => 'success',
				'code' => 200,
			);
		} else {
			$response = array(
				'msg' => $response_data->error_description,
				'status' => 'error',
				'code' => $response_code,
			);
		}
		echo json_encode( $response );
		wp_die();
	}

}
