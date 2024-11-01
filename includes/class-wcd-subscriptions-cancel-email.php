<?php
/**
 * Expired Email template
 *
 * @link       https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_Woocommerce
 * @subpackage Subscriptions_For_Woocommerce/email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// @credit taken refernce from "wpswings" plugin
if ( ! class_exists( 'Wcd_Subscriptions_Cancel_Email' ) ) {

	/**
	 * Expired Email template class
	 *
	 * @link       https://webcuddle.com/
	 * @since      1.0.0
	 *
	 * @package    Subscriptions_For_Woocommerce
	 * @subpackage Subscriptions_For_Woocommerce/email
	 */
	class Wcd_Subscriptions_Cancel_Email extends WC_Email {
		/**
		 * Create class for email notification.
		 *
		 * @access public
		 */
		public function __construct() {

			$this->id          = 'wcd_subscription_cancel';
			$this->title       = __( 'Email Notification When Subscription Cancelled', 'wcd-subscriptions' );
			$this->description = __( 'If any subscription is Cancelled then this Email Notification Send', 'wcd-subscriptions' );
			$this->template_html  = 'wcd_subscription_cancel-email-template.php';
			$this->template_plain = 'plain/wcd_subscription_cancel-email-template.php';
			$this->template_base  = WCD_SUBSCRIPTIONS_DIR_PATH . 'admin/partials/templates/emails';

			parent::__construct();

		}

		/**
		 * Get email subject.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Cancelled Susbcription Email {site_title}', 'wcd-subscriptions' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Subscription Cancelled', 'wcd-subscriptions' );
		}

		/**
		 * This function is used to trigger for email.
		 *
		 * @since  1.0.0
		 * @param int $wcd_subs wcd_subs.
		 * @access public
		 * @return void
		 */
		public function trigger( $wcd_subs ) {

			if ( $wcd_subs ) {

				$this->object = $wcd_subs;
				$wcd_parent_order_id = get_post_meta( $wcd_subs, 'wcd_parent_order', true );
				$wcd_parent_order = wc_get_order( $wcd_parent_order_id );
				if ( ! empty( $wcd_parent_order ) ) {
					$user_email = $wcd_parent_order->get_billing_email();
					$this->recipient = $user_email;

				}
			}

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->send( get_option( 'admin_email' ), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Get_content_html function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'wcd_subs'       => $this->object,
					'email_heading'      => $this->get_heading(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email' => $this,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Get_content_plain function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'wcd_subs'       => $this->object,
					'email_heading'      => $this->get_heading(),
					'sent_to_admin'      => true,
					'plain_text'         => true,
					'email' => $this,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @credit Inspired by code from the "wpswings" plugin.
		 * @access public
		 * @return void
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'wcd-subscriptions' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'wcd-subscriptions' ),
					'default' => 'no',
				),
				'recipient'  => array(
					'title'       => __( 'Recipient Email Address', 'wcd-subscriptions' ),
					'type'        => 'text',
					// translators: placeholder is admin email.
					'description' => sprintf( __( 'Enter recipient email address. Defaults to %s.', 'wcd-subscriptions' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'wcd-subscriptions' ),
					'type'        => 'text',
					'description' => __( 'Enter the email subject', 'wcd-subscriptions' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
					'desc_tip'    => true,
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'wcd-subscriptions' ),
					'type'        => 'text',
					'description' => __( 'Email Heading', 'wcd-subscriptions' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
					'desc_tip'    => true,
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'wcd-subscriptions' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'wcd-subscriptions' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}

	}

}

return new Wcd_Subscriptions_Cancel_Email();
