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

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}
$form_submitted = get_option( 'wcd_demo_subscripiton_product_created', 'no' );
if ( 'no' == $form_submitted ) {
	require_once WCD_SUBSCRIPTIONS_DIR_PATH . 'admin/partials/wcd-subscriptions-admin-display.php';
} else {
	define( 'ONBOARD_PLUGIN_NAME', 'WCD Subscriptions' );

	?>

	<?php
	global $wcd_woo_subs;
	// phpcs:ignore WordPress.Security.NonceVerification
	$active_tab   = isset( $_GET['wcd_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['wcd_tab'] ) ) : 'wcd-woo-subs-settings';
	$default_tabs = $wcd_woo_subs->wcd_subs_woo_default_tabs();
	?>
<div class="wcd-admin-wrap">
	<div class="wcd-go-pro">
		<div class="wcd-go-pro-banner">
			<div class="wcd-inner-container">
				<div class="wcd-name-wrapper">
					<?php
					$wcd_subs_pro_plugin_activated = false;
					if ( in_array( 'wcd-subscriptions-pro/wcd-subscriptions-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
						$wcd_subs_pro_plugin_activated = true;
					}
					if ( ! $wcd_subs_pro_plugin_activated ) {
					?>
					<p><?php esc_html_e( 'WCD Subscriptions', 'wcd-subscriptions' ); ?></p>
					<?php
					} else {
						?>
						<p><?php esc_html_e( 'WCD Subscriptions Pro', 'wcd-subscriptions' ); ?></p>
						<?php
					}
					?>
				</div>
				<div class="wcd-static-menu">
					<ul>
						<li><a href="<?php echo esc_url( 'https://webcuddle.com/contact/' ); ?>"
								target="_blank"><img src="<?php echo esc_url( WCD_SUBSCRIPTIONS_DIR_URL . 'admin/images/contact_us.jpeg' ); ?>"></a></li>
						<li class="wcd-main-menu-button"><a id="wcd-skype-link"
								href="<?php echo esc_url( 'https://join.skype.com/invite/wpKO1TfXofBg' ); ?>" class=""
								title="" target="_blank"><img src="<?php echo esc_url( WCD_SUBSCRIPTIONS_DIR_URL . 'admin/images/chat_now.jpeg' ); ?>"></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="wcd-main-template">

		<div class="wcd-body-template">
			<div class="wcd-navigator-template">
				<div class="wcd-navigations">
					<?php
					if ( is_array( $default_tabs ) && count( $default_tabs ) ) {

						foreach ( $default_tabs as $tab_key => $single_tab ) {

							$tab_classes = 'wcd-nav-tab ';

							if ( ! empty( $active_tab ) && $tab_key === $active_tab ) {

								$tab_classes .= 'nav-tab-active';
							}




							?>
					<div class="wcd-tabs">
						<a class="<?php echo esc_attr( $tab_classes ); ?>" id="<?php echo esc_attr( $tab_key ); ?>"
							href="<?php echo esc_url( admin_url( 'admin.php?page=wcd_woo_subs_page' ) . '&wcd_tab=' . $tab_key ); ?>">
							<i class="<?php echo esc_attr( $single_tab['icon'] ); ?>">
							</i>
							<span>
									<?php echo esc_html( $single_tab['name'] ); ?>
							</span>
						</a>
					</div>
									<?php
						}
					}
					?>
				</div>
			</div>
			<div class="wcd-content-template">
				<div class="wcd-content-container">
					<?php
					if( 'wcd-subscriptions-pro-settings' == $active_tab || 'wcd-subscriptions-pro-license' == $active_tab ) {
						$tab_content_path = 'admin/partials/templates/' . $active_tab . '.php';
						$wcd_woo_subs->load_pro_template_view( $tab_content_path );
					} else {
						$tab_content_path = 'admin/partials/templates/' . $active_tab . '.php';
						$wcd_woo_subs->load_template_view( $tab_content_path );
					}
					?>
				</div>
			</div>
		</div>
		<div style="display: none;" class="loading-style-bg" id="wcd_loader">
			<img src="<?php echo esc_url( WCD_SUBSCRIPTIONS_DIR_URL . 'admin/images/loader.gif' ); ?>">
		</div>
	</div>
</div>
<?php } ?>
