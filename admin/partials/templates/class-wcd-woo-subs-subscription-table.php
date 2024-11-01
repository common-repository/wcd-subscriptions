<?php
/**
 * Provide a admin settings page
 *
 * This file is used to markup the admin settings of the plugin.
 *
 * @since 1.0.0
 * @package  wcd-subscriptions
 * @subpackage wcd-subscriptions/admin/partials/templates
 */

// WP_List_Table is not loaded automatically so we need to load it in our application.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Wcd_Woo_Subs_Subscription_Table extends WP_List_Table {


	/**
	 * This is variable which is used for the store all the data.
	 *
	 * @var array $example_data variable for store data.
	 */
	public $example_data;

	/**
	 * This is variable which is used for the total count.
	 *
	 * @var array $wcd_total_count variable for total count.
	 */
	public $wcd_total_count;
	/**
	 * Prepare the items for the table to process
	 *
	 * @return void
	 */
	public function prepare_items() {
		$per_page              = 10;
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$current_page = $this->get_pagenum();

		$this->example_data = $this->get_all_subscriptions();
		$data               = $this->example_data;
		usort( $data, array( $this, 'wcd_usort_reorder' ) );
		$total_items = $this->wcd_total_count;
		$this->items  = $data;
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'cb'                            => '<input type="checkbox" />',
			'subscription_id'               => __( 'Subscription ID', 'wcd-subscriptions' ),
			'parent_order_id'               => __( 'Parent ID', 'wcd-subscriptions' ),
			'status'                        => __( 'Status', 'wcd-subscriptions' ),
			'product_name'                  => __( 'Product Name', 'wcd-subscriptions' ),
			'recurring_amount'              => __( 'Recurring Amount', 'wcd-subscriptions' ),
			'user_name'                     => __( 'User Name', 'wcd-subscriptions' ),
			'next_payment_date'             => __( 'Next Payment Date', 'wcd-subscriptions' ),
			'subscriptions_expiry_date'     => __( 'Subscription Expiry Date', 'wcd-subscriptions' ),

		);
		return $columns;
	}

	/**
	 * This function used to get all susbcriptions list.
	 *
	 * @name get_all_subscriptions.
	 * @since      1.0.0
	 * @return array
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function get_all_subscriptions() {
		$wcd_pro_plugin_activated = false;

		$current_page = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : 1;

		$args = array(
			'posts_per_page' => 10,
			'paged' => $current_page,
			'post_type'   => 'wcd_subscriptions', // key chnage here.
			'post_status' => 'wc-wcd_recurring', // key change here.
			'meta_query' => array(
				array(
					'key'   => 'user_id',    // key change here.
					'compare' => 'EXISTS',
				),
			),
			'fields' => 'ids',
		);

		if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
			$data           = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			$args['meta_query'] = array(
				array(
					'key'   => 'wcd_parent_order',    // key change here.
					'value' => $data,
					'compare' => 'LIKE',
				),
			);
		}

		$wcd_subs = get_posts( $args );

		$args2 = array(
			'numberposts' => -1,
			'post_type'   => 'wcd_subscriptions', // key change here.
			'post_status' => 'wc-wcd_recurring', // key change here.
			'meta_query' => array(
				array(
					'key'   => 'user_id', // key change here.
					'compare' => 'EXISTS',
				),
			),
			'fields' => 'ids',
		);
		if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
			$data           = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			$args2['meta_query'] = array(
				array(
					'key'   => 'wcd_parent_order', // key change here.
					'value' => $data,
					'compare' => 'LIKE',
				),
			);
		}
		$wcd_subs2 = get_posts( $args2 );
		$total_count = count( $wcd_subs2 );

		// search with subscription id code.
		if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
			$data           = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			if ( empty( $wcd_subs ) ) {
				$wcd_subs_id = get_post_meta( $data, 'wcd_parent_order', true ); // key change here.
				$args2['meta_query'] = array(
					array(
						'key'   => 'wcd_parent_order', // key change here.
						'value' => $wcd_subs_id,
						'compare' => 'LIKE',
					),
				);
				$wcd_subs = get_posts( $args2 );
			}
		}
		// search with subscription id code.

		$wcd_subs_data = array();

		if ( isset( $wcd_subs ) && ! empty( $wcd_subs ) && is_array( $wcd_subs ) ) {
			foreach ( $wcd_subs as $id ) {

				$parent_order_id   = get_post_meta( $id, 'wcd_parent_order', true );
				$wcd_subscription_status   = get_post_meta( $id, 'wcd_subscription_status', true ); // key change here.
				$product_name   = get_post_meta( $id, 'product_name', true );// key change here.
				$wcd_recurring_total   = get_post_meta( $id, 'wcd_recurring_total', true );// key change here.
				$wcd_curr_args = array();
				$susbcription = wc_get_order( $id );
				if ( isset( $susbcription ) && ! empty( $susbcription ) ) {
					$wcd_recurring_total = $susbcription->get_total();
					$wcd_curr_args = array(
						'currency' => $susbcription->get_currency(),
					);
				}
				$wcd_recurring_total = $this->recurring_table_for_total_price( wc_price( $wcd_recurring_total, $wcd_curr_args ), $id );

				$wcd_next_payment_date   = get_post_meta( $id, 'wcd_next_payment_date', true );
				$wcd_susbcription_end   = get_post_meta( $id, 'wcd_susbcription_end', true );
				if ( $wcd_next_payment_date === $wcd_susbcription_end ) {
					$wcd_next_payment_date = '';
				}

				if ( 'on-hold' === $wcd_subscription_status ) {
					$wcd_next_payment_date = '';
					$wcd_recurring_total = 'xxx';
				}
				if ( 'cancelled' === $wcd_subscription_status ) {
					$wcd_next_payment_date = '';
					$wcd_susbcription_end = '';
					$wcd_recurring_total = 'xxx';
				}
							$wcd_customer_id   = get_post_meta( $id, 'user_id', true );
							$user = get_user_by( 'id', $wcd_customer_id );

				if ( ! $wcd_pro_plugin_activated ) {
					$subp_id = get_post_meta( $id, 'product_id', true );
					$check_variable = get_post_meta( $subp_id, 'wcd_variable_product', true );
					if ( 'yes' === $check_variable ) {
						continue;
					}
				}

							$user_nicename = isset( $user->user_nicename ) ? $user->user_nicename : '';
							$wcd_subs_data[] = apply_filters(
								'wcd_subs_table_data',
								array(
									'subscription_id'           => $id,
									'parent_order_id'           => $parent_order_id,
									'status'                    => $wcd_subscription_status,
									'product_name'              => $product_name,
									'recurring_amount'          => $wcd_recurring_total,
									'user_name'                 => $user_nicename,
									'next_payment_date'         => $this->converted_wordpress_date_format( $wcd_next_payment_date ),
									'subscriptions_expiry_date' => $this->converted_wordpress_date_format( $wcd_susbcription_end ),
								)
							);
			}
		}
		$this->wcd_total_count = $total_count;
		return $wcd_subs_data;
	}

	/**
	 * This function used to setg date format.
	 *
	 * @name converted_wordpress_date_format.
	 *
	 * @param date $saved_date Saved date.
	 * @since      1.0.0
	 * @return array
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function converted_wordpress_date_format( $saved_date ) {
		$return_date = 'xxx';
		if ( isset( $saved_date ) && ! empty( $saved_date ) ) {

			$date_format = get_option( 'date_format', 'Y-m-d' );
			$time_format = get_option( 'time_format', 'g:i a' );
			$wp_date = date_i18n( $date_format, $saved_date );

			$return_date = $wp_date;
		}

		return $return_date;
	}


	/**
	 * This function is used show recuring interval on list.
	 *
	 * @name recurring_table_for_total_price
	 * @param string $wcd_price wcd_price.
	 * @param int    $wcd_subscription_id wcd_subscription_id.
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function recurring_table_for_total_price( $wcd_price, $wcd_subscription_id ) {
		if ( $this->wcd_woo_subscription_check_subscription( $wcd_subscription_id ) ) {
			$wcd_recurring_number = get_post_meta( $wcd_subscription_id, 'wcd_woo_sub_product_total_period_number', true );
			$wcd_recurring_interval = get_post_meta( $wcd_subscription_id, 'wcd_woo_sub_product_total_period_type', true );
			$wcd_price_html = $this->get_time_interval_for_price( $wcd_recurring_number, $wcd_recurring_interval );

			/* translators: %s: frequency interval. */
			$wcd_price .= sprintf( esc_html( ' / %s ' ), $wcd_price_html );
		}
		return $wcd_price;
	}

	/**
	 * This function is used check subs is valid or not.
	 *
	 * @name wcd_check_valid_subscription
	 * @param int $wcd_subscription_id wcd_subscription_id.
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_check_valid_subscription( $wcd_subscription_id ) {
		$wcd_is_subscription = false;

		if ( isset( $wcd_subscription_id ) && ! empty( $wcd_subscription_id ) ) {
			if ( 'wcd_subscriptions' == get_post_type( absint( $wcd_subscription_id ) ) ) {
				$wcd_is_subscription = true;
			}
		}
		return $wcd_is_subscription;
	}

	/**
	 * This function is used to get account page callback.
	 *
	 * @name account_page_of_subscription
	 * @param int $subscription_id subscription_id.
	 * @since 1.0.0
	 * @credit Inspired by code from the "wpswings" plugin.
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
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'subscription_id'   => array( 'subscription_id', false ),
			'parent_order_id'  => array( 'parent_order_id', false ),
			'status' => array( 'status', false ),
		);
		return $sortable_columns;
	}



	/**
	 * This show susbcriptions table list.
	 *
	 * @name column_default.
	 * @since      1.0.0
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 * @param array  $item  array of the items.
	 * @param string $column_name name of the colmn.
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'subscription_id':
				$actions = array();
				$wcd_status = array( 'active' );
				$wcd_status = apply_filters( 'wcd_status_array', $wcd_status );
				if ( in_array( $item['status'], $wcd_status ) ) {
					$actions = $this->wcd_cancel_url( $item['subscription_id'], $item['status'] );
				}
				$actions = apply_filters( 'wcd_add_action_details', $actions, $item['subscription_id'] );
				return $item[ $column_name ] . $this->row_actions( $actions );
			case 'parent_order_id':
				$html = '<a href="' . esc_url( get_edit_post_link( $item[ $column_name ] ) ) . '">' . $item[ $column_name ] . '</a>';
				return $html;
			case 'status':
				return $item[ $column_name ];
			case 'product_name':
				return $item[ $column_name ];
			case 'recurring_amount':
				return $item[ $column_name ];
			case 'user_name':
				return $item[ $column_name ];
			case 'next_payment_date':
				return $item[ $column_name ];
			case 'subscriptions_expiry_date':
				return $item[ $column_name ];
			default:
				return apply_filters( 'wcd_add_case_column', false, $column_name, $item );
		}
	}

	/**
	 * Get Cancel url.
	 *
	 * @name wcd_cancel_url.
	 * @since      1.0.0
	 * @param int    $subscription_id subscription_id.
	 * @param String $status status.
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_cancel_url( $subscription_id, $status ) {
		$wcd_link = add_query_arg(
			array(
				'wcd_woo_subscription_id'               => $subscription_id,
				'wcd_woo_subscription_status_admin'     => $status,
			)
		);

		$wcd_link = wp_nonce_url( $wcd_link, $subscription_id . $status );
		$actions = array(
			'wcd_cancel' => '<a href="' . $wcd_link . '">' . __( 'Cancel', 'wcd-subscriptions' ) . '</a>',

		);
		return $actions;
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @param array $a a.
	 * @param array $b b.
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults.
		$orderby = 'title';
		$order = 'asc';

		// If orderby is set, use this as the sort column.
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
		}

		// If order is set use this as the order.
		if ( ! empty( $_GET['order'] ) ) {
			$order = sanitize_text_field( wp_unslash( $_GET['order'] ) );
		}

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( 'asc' === $order ) {
			return $result;
		}

		return -$result;
	}

	/**
	 * Process bulk action
	 *
	 * @return void
	 */
	public function process_bulk_action() {

		if ( 'bulk-delete' === $this->current_action() ) {

			if ( isset( $_POST['susbcription_list_table'] ) ) {
				$susbcription_list_table = sanitize_text_field( wp_unslash( $_POST['susbcription_list_table'] ) );
				if ( wp_verify_nonce( $susbcription_list_table, 'susbcription_list_table' ) ) {
					if ( isset( $_POST['wcd_subscriptions_ids'] ) && ! empty( $_POST['wcd_subscriptions_ids'] ) ) {
						$all_id = map_deep( wp_unslash( $_POST['wcd_subscriptions_ids'] ), 'sanitize_text_field' );
						foreach ( $all_id as $key => $value ) {
							 wp_delete_post( $value, true );
						}
					}
				}
			}
			?>
			<div class="notice notice-success is-dismissible"> 
				<p><strong><?php esc_html_e( 'Subscriptions Deleted Successfully', 'wcd-subscriptions' ); ?></strong></p>
			</div>
			<?php
		}
		if ( 'bulk-export' === $this->current_action() ) { //@credit Inspired by code from the "wpswings" plugin.

			if ( empty( $_POST['wcd_subscriptions_ids'] ) ) {
				wp_redirect( admin_url( 'admin.php?page=wcd_woo_subs_page&wcd_tab=class-wcd-woo-subs-subscription-table&export_subscriptions=export' ) );
			} else {
				$subs_ids = $_POST['wcd_subscriptions_ids'];
				$ids = implode(",",$subs_ids);
				wp_redirect( admin_url( 'admin.php?page=wcd_woo_subs_page&wcd_tab=class-wcd-woo-subs-subscription-table&export_subscriptions_by_ids=' . $ids ) );
			}
		}

	}


	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @name process_bulk_action.
	 * @since      1.0.0
	 * @return array
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => __( 'Delete', 'wcd-subscriptions' ),
		);
		$actions = apply_filters( 'add_bulk_actions_in_subs_table', $actions );
		return $actions;
	}

	/**
	 * Return sorted associative array.
	 *
	 * @name wcd_usort_reorder.
	 * @since      1.0.0
	 * @return array
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 * @param array $cloumna column of the susbcriptions.
	 * @param array $cloumnb column of the susbcriptions.
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function wcd_usort_reorder( $cloumna, $cloumnb ) {

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'subscription_id';
		$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc';

		if ( is_numeric( $cloumna[ $orderby ] ) && is_numeric( $cloumnb[ $orderby ] ) ) {
			if ( $cloumna[ $orderby ] == $cloumnb[ $orderby ] ) {
				return 0;
			} elseif ( $cloumna[ $orderby ] < $cloumnb[ $orderby ] ) {
				$result = -1;
				return ( 'asc' === $order ) ? $result : -$result;
			} elseif ( $cloumna[ $orderby ] > $cloumnb[ $orderby ] ) {
				$result = 1;
				return ( 'asc' === $order ) ? $result : -$result;
			}
		} else {
			$result = strcmp( $cloumna[ $orderby ], $cloumnb[ $orderby ] );
			return ( 'asc' === $order ) ? $result : -$result;
		}
	}

	/**
	 * THis function is used for the add the checkbox.
	 *
	 * @name column_cb.
	 * @since      1.0.0
	 * @return array
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 * @param array $item array of the items.
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="wcd_subscriptions_ids[]" value="%s" />',
			$item['subscription_id']
		);
	}

	/**
	 * Create the extra table option.
	 *
	 * @name extra_tablenav.
	 * @since      1.0.0
	 * @param string $which which.
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 */
	public function extra_tablenav( $which ) {
		// Add list option.
		do_action( 'wcd_extra_tablenav_html', $which );
	}

	/**
	 * Get time interval for price.
	 *
	 * @name get_time_interval_for_price.
	 * @since      1.0.0
	 * @param string $wcd_subscription_number subscription_number.
	 * @param string $wcd_subscription_interval subscription_interval.
	 * @author WebCuddle<ticket@webcuddle.com>
	 * @link https://www.webcuddle.com/
	 * @credit Inspired by code from the "wpswings" plugin.
	 */
	public function get_time_interval_for_price( $wcd_subscription_number, $wcd_subscription_interval ) {
		$wcd_number = $wcd_subscription_number;
		if ( 1 == $wcd_subscription_number ) {
			$wcd_subscription_number = '';
		}

		$wcd_price_html = '';
		switch ( $wcd_subscription_interval ) {
			case 'day':
				/* translators: %s: Day,%s: Days */
				$wcd_price_html = sprintf( _n( '%s Day', '%s Days', $wcd_number, 'wcd-subscriptions' ), $wcd_subscription_number );
				break;
			case 'week':
				/* translators: %s: Week,%s: Weeks */
				$wcd_price_html = sprintf( _n( '%s Week', '%s Weeks', $wcd_number, 'wcd-subscriptions' ), $wcd_subscription_number );
				break;
			case 'month':
				/* translators: %s: Month,%s: Months */
				$wcd_price_html = sprintf( _n( '%s Month', '%s Months', $wcd_number, 'wcd-subscriptions' ), $wcd_subscription_number );
				break;
			case 'year':
				/* translators: %s: Year,%s: Years */
				$wcd_price_html = sprintf( _n( '%s Year', '%s Years', $wcd_number, 'wcd-subscriptions' ), $wcd_subscription_number );
				break;
		}
		return $wcd_price_html;

	}


}
?>
<form method="post">
	<input type="hidden" name="page" value="susbcription_list_table">
	<?php wp_nonce_field( 'susbcription_list_table', 'susbcription_list_table' ); ?>
	<div class="wcd_list_table">
		<?php
		$table_data = new Wcd_Woo_Subs_Subscription_Table();
		$table_data->prepare_items();
		$table_data->search_box( __( 'Search Order', 'wcd-subscriptions' ), 'wcd-order' );
		$table_data->display();
		?>
	</div>
</form>
